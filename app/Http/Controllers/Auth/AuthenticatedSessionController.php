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
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        /** @var User $user */
        $user = Auth::user();

        ActivityLog::record(
            'LOGIN',
            'Auth',
            "Pengguna {$user->name} ({$user->user_type}) berhasil login ke dalam sistem.",
            $user,
            ['email' => $user->email, 'role' => $user->role],
            $user
        );

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Selamat datang kembali, '.$user->name.'!');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if ($user) {
            ActivityLog::record(
                'LOGOUT',
                'Auth',
                "Pengguna {$user->name} keluar (logout) dari sistem.",
                $user,
                null,
                $user
            );
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
