<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Proses login.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => 'login',
            'module'      => 'auth',
            'description' => 'User login ke sistem',
            'ip_address'  => $request->ip(),
        ]);

        // Set flag welcome popup — muncul sekali setelah login
        session()->flash('show_welcome', true);

        return redirect()->intended($this->redirectPath(Auth::user()));
    }

    /**
     * Logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Catat activity log sebelum logout
        if (Auth::check()) {
            ActivityLog::create([
                'user_id'     => Auth::id(),
                'action'      => 'logout',
                'module'      => 'auth',
                'description' => 'User logout dari sistem',
                'ip_address'  => $request->ip(),
            ]);
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Tentukan redirect path berdasarkan role.
     */
    protected function redirectPath(User $user): string
    {
        return match ($user->role->value) {
            'admin'            => route('admin.dashboard'),
            'waka_sarpras'     => route('waka.dashboard'),
            'kepala_sekolah'   => route('kepala.dashboard'),
            'pengguna_ruangan' => route('pengguna.dashboard'),
            default            => '/',
        };
    }
}