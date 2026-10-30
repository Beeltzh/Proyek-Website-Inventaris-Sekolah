<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
 
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $validated = $request->validated();

        $oldData = $user->only(['name', 'email', 'nomor_induk', 'kelas_jabatan', 'no_telepon']);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->nomor_induk = $validated['nomor_induk'] ?? $user->nomor_induk;
        $user->kelas_jabatan = $validated['kelas_jabatan'] ?? $user->kelas_jabatan;
        $user->no_telepon = $validated['no_telepon'] ?? null;

        $passwordChanged = ! empty($validated['password']);
        if ($passwordChanged) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        ActivityLog::record(
            'UPDATE',
            'Profile',
            "Pengguna {$user->name} memperbarui profil akun".($passwordChanged ? ' beserta kata sandi.' : '.'),
            $user,
            [
                'old' => $oldData,
                'new' => $user->only(['name', 'email', 'nomor_induk', 'kelas_jabatan', 'no_telepon']),
                'password_changed' => $passwordChanged,
            ],
            $user
        );

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }
}
