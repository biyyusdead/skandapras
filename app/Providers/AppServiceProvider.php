<?php

namespace App\Providers;

use App\Models\Approval;
use App\Models\DamageReport;
use App\Models\RepairReport;
use App\Models\Room;
use App\Policies\ApprovalPolicy;
use App\Policies\DamageReportPolicy;
use App\Policies\RepairReportPolicy;
use App\Policies\RoomPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Room::class, RoomPolicy::class);
        Gate::policy(DamageReport::class, DamageReportPolicy::class);
        Gate::policy(RepairReport::class, RepairReportPolicy::class);
        Gate::policy(Approval::class, ApprovalPolicy::class);

        // Super admin bypass (optional)
        Gate::before(function ($user, $ability) {
            if ($user->isAdmin() && str_starts_with($ability, 'admin.')) {
                return true;
            }

            return null;
        });

        // ====================================================================
        // RATE LIMITERS
        // ====================================================================

        // Rate limiter formulir pengaduan sarpras
        RateLimiter::for('damage-report', function (Request $request) {
            if ($user = $request->user()) {
                $limitKey = 'damage_report:user:'.$user->id;
                $limit = Limit::perMinutes(10, 10)->by($limitKey);
            } else {
                $sessionIdentifier = $request->hasSession() ? $request->session()->getId() : '';
                $guestFingerprint = sha1($request->ip().'|'.$sessionIdentifier.'|'.$request->userAgent());
                $limitKey = 'damage_report:guest:'.$guestFingerprint;
                $limit = Limit::perMinutes(10, 5)->by($limitKey);
            }

            return $limit->response(function (Request $request, array $headers) {
                $retryAfter = $headers['Retry-After'] ?? 60;
                $message = "Terlalu banyak permintaan pengiriman pengaduan. Demi keamanan, silakan tunggu {$retryAfter} detik sebelum mencoba kembali.";

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => $message,
                        'retry_after' => $retryAfter,
                    ], 429, $headers);
                }

                return back()->with('error', $message);
            });
        });

        // Rate limiter chatbot (terpisah dari pengaduan sarpras)
        RateLimiter::for('chatbot', function (Request $request) {
            $perMinute = (int) config('chatbot.rate_limit.per_minute', 5);
            $perDay = (int) config('chatbot.rate_limit.per_day', 20);

            if ($user = $request->user()) {
                $limitKey = 'chatbot:user:'.$user->id;
            } else {
                $sessionIdentifier = $request->hasSession() ? $request->session()->getId() : '';
                $guestFingerprint = sha1($request->ip().'|'.$sessionIdentifier.'|'.$request->userAgent());
                $limitKey = 'chatbot:guest:'.$guestFingerprint;
            }

            return [
                Limit::perMinute($perMinute)->by($limitKey)->response(function (Request $request, array $headers) use ($perMinute) {
                    $retryAfter = $headers['Retry-After'] ?? 60;

                    return response()->json([
                        'reply' => "Batas pengiriman pesan sementara tercapai (maksimal {$perMinute} pesan per menit). Silakan coba lagi dalam {$retryAfter} detik.",
                        'retry_after' => (int) $retryAfter,
                    ], 429, $headers);
                }),
                Limit::perDay($perDay)->by($limitKey)->response(function (Request $request, array $headers) use ($perDay) {
                    return response()->json([
                        'reply' => "Batas penggunaan chatbot harian Anda telah tercapai (maksimal {$perDay} pesan per hari). Silakan coba kembali besok.",
                        'daily_limit' => true,
                    ], 429, $headers);
                }),
            ];
        });
    }
}
