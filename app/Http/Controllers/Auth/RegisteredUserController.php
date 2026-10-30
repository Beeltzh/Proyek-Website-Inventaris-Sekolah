<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => 'user',
            'user_type' => $validated['user_type'],
            'nomor_induk' => $validated['nomor_induk'],
            'kelas_jabatan' => $validated['kelas_jabatan'],
            'no_telepon' => $validated['no_telepon'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));

        Auth::login($user);
        $request->session()->regenerate();

        ActivityLog::record(
            'REGISTER',
            'Auth',
            "Registrasi akun peminjam baru: {$user->name} ({$user->user_type} - {$user->kelas_jabatan}).",
            $user,
            ['new' => $user->only(['name', 'email', 'role', 'user_type', 'nomor_induk', 'kelas_jabatan'])],
            $user
        );

        return redirect()->route('dashboard')
            ->with('success', 'Akun berhasil dibuat! Selamat datang di Sistem Inventaris Sekolah.');
    }
}
