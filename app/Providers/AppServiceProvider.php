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
use Illuminate\Support\Facades\Gate;
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
    }
}