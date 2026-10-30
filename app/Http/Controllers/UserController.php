<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $role = $request->query('role');
        $userType = $request->query('user_type');

        $users = User::withCount([
            'loans',
            'loans as active_loans_count' => function ($q) {
                $q->whereIn('status', ['menunggu_persetujuan', 'dipinjam', 'menunggu_pengembalian']);
            },
        ])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('nomor_induk', 'like', "%{$search}%")
                        ->orWhere('kelas_jabatan', 'like', "%{$search}%");
                });
            })
            ->when($role, fn ($q) => $q->where('role', $role))
            ->when($userType, fn ($q) => $q->where('user_type', $userType))
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users', 'search', 'role', 'userType'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);

        $newUser = User::create($validated);

        ActivityLog::record(
            'CREATE',
            'Users',
            "Admin menambahkan akun pengguna baru: {$newUser->name} ({$newUser->role} - {$newUser->user_type}).",
            $newUser,
            ['new' => $newUser->only(['name', 'email', 'role', 'user_type', 'nomor_induk', 'kelas_jabatan'])]
        );

        return redirect()->route('users.index')
            ->with('success', 'Pengguna "'.$validated['name'].'" berhasil ditambahkan.');
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        $oldData = $user->only(['name', 'email', 'role', 'user_type', 'nomor_induk', 'kelas_jabatan']);
        $passwordReset = ! empty($validated['password']);

        if ($passwordReset) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        ActivityLog::record(
            'UPDATE',
            'Users',
            "Admin memperbarui data akun pengguna: {$user->name} ({$user->role}).",
            $user,
            [
                'old' => $oldData,
                'new' => $user->only(['name', 'email', 'role', 'user_type', 'nomor_induk', 'kelas_jabatan']),
                'password_reset' => $passwordReset,
            ]
        );

        return redirect()->route('users.index')
            ->with('success', 'Data pengguna "'.$user->name.'" berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $hasActive = $user->loans()
            ->whereIn('status', ['menunggu_persetujuan', 'dipinjam', 'menunggu_pengembalian'])
            ->exists();

        if ($hasActive) {
            return back()->with('error', 'Pengguna "'.$user->name.'" tidak dapat dihapus karena masih memiliki tanggungan peminjaman barang aktif.');
        }

        $deletedData = $user->only(['id', 'name', 'email', 'role', 'user_type', 'nomor_induk']);
        $nama = $user->name;
        $user->delete();

        ActivityLog::record(
            'DELETE',
            'Users',
            "Admin menghapus akun pengguna: {$deletedData['name']} ({$deletedData['email']}).",
            null,
            ['deleted' => $deletedData]
        );

        return redirect()->route('users.index')
            ->with('success', 'Pengguna "'.$nama.'" berhasil dihapus.');
    }
}
