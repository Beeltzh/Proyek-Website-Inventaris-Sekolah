@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page_title', 'Manajemen Akun Pengguna (Admin, Guru & Siswa)')
@section('page_subtitle', 'Kelola hak akses pengelola inventaris serta akun peminjam warga sekolah')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span aria-current="page">Data Pengguna</span>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <form action="{{ route('users.index') }}" method="GET" class="filter-bar">
                <input type="text" name="search" class="form-control" style="width:240px;" placeholder="Cari nama, email, NIP/NISN, kelas..." value="{{ $search }}" autofocus>

                <select name="role" class="form-control" style="width:160px;">
                    <option value="">Semua Hak Akses</option>
                    <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin Inventaris</option>
                    <option value="user" {{ $role === 'user' ? 'selected' : '' }}>User (Peminjam)</option>
                </select>

                <select name="user_type" class="form-control" style="width:160px;">
                    <option value="">Semua Status</option>
                    <option value="Pengelola" {{ $userType === 'Pengelola' ? 'selected' : '' }}>Pengelola</option>
                    <option value="Guru" {{ $userType === 'Guru' ? 'selected' : '' }}>Guru</option>
                    <option value="Siswa" {{ $userType === 'Siswa' ? 'selected' : '' }}>Siswa</option>
                    <option value="Staf" {{ $userType === 'Staf' ? 'selected' : '' }}>Staf</option>
                </select>

                <button type="submit" class="btn btn-outline">Filter</button>
                @if($search || $role || $userType)
                    <a href="{{ route('users.index') }}" class="btn btn-outline">Reset</a>
                @endif
            </form>

            <button type="button" class="btn btn-primary" onclick="document.getElementById('addUserModal').showModal()">
                + Tambah Pengguna
            </button>
        </div>

        <div tabindex="0" role="region" aria-labelledby="users-table-caption" class="table-wrapper">
            <table class="data-table" style="--label-2: 'Email & Telepon'; --label-3: 'Peran & Status'; --label-4: 'NIP / NISN & Kelas'; --label-5: 'Riwayat Pinjam'; --label-6: 'Aksi';">
                <caption id="users-table-caption">Daftar pengguna sistem inventaris sekolah</caption>
                <thead>
                    <tr>
                        <th scope="col">Nama Lengkap</th>
                        <th scope="col">Email & Kontak</th>
                        <th scope="col">Role & Status</th>
                        <th scope="col">NIP/NISN & Kelas/Jabatan</th>
                        <th scope="col">Peminjaman</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <th scope="row">{{ $u->name }}</th>
                            <td>
                                <div>{{ $u->email }}</div>
                                <small style="color:var(--text-muted);">{{ $u->no_telepon ?: '-' }}</small>
                            </td>
                            <td>
                                @if($u->role === 'admin')
                                    <span class="badge badge-primary"><span class="badge-dot"></span><span>Admin</span></span>
                                @else
                                    <span class="badge badge-success"><span class="badge-dot"></span><span>User</span></span>
                                @endif
                                <span class="badge badge-neutral">{{ $u->user_type }}</span>
                            </td>
                            <td>
                                <div><strong>ID:</strong> {{ $u->nomor_induk ?: '-' }}</div>
                                <small style="color:var(--text-muted);">{{ $u->kelas_jabatan ?: '-' }}</small>
                            </td>
                            <td>
                                <div>Total: <strong>{{ format_number($u->loans_count) }}x</strong></div>
                                @if($u->active_loans_count > 0)
                                    <span class="badge badge-warning"><span class="badge-dot"></span><span>{{ format_number($u->active_loans_count) }} Aktif</span></span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex; gap:0.4rem;">
                                    <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('editUserModal-{{ $u->id }}').showModal()">
                                        Edit
                                    </button>
                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $u) }}" method="POST" onsubmit="return confirm('Hapus akun {{ $u->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Pengguna">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:2rem; color:var(--text-muted);">
                                Tidak ada data pengguna yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="pagination-wrap">
                {{ $users->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Tambah Pengguna -->
    <dialog id="addUserModal" closedby="any" aria-labelledby="addUserTitle" class="modal-dialog">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h3 id="addUserTitle">Tambah Akun Pengguna Baru</h3>
                <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name">Nama Lengkap <span class="req">*</span></label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Contoh: Dewi Sartika, S.Pd." required autofocus>
                </div>
                <div class="form-group">
                    <label for="email">Alamat Email <span class="req">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="contoh@sekolah.sch.id" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="role">Hak Akses Sistem <span class="req">*</span></label>
                        <select id="role" name="role" class="form-control" required>
                            <option value="user">User (Guru / Siswa Peminjam)</option>
                            <option value="admin">Admin (Pengelola Inventaris)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="user_type">Kategori Pengguna <span class="req">*</span></label>
                        <select id="user_type" name="user_type" class="form-control" required>
                            <option value="Siswa">Siswa</option>
                            <option value="Guru">Guru</option>
                            <option value="Staf">Staf</option>
                            <option value="Pengelola">Pengelola Sarpras</option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="nomor_induk">NIP / NISN</label>
                        <input type="text" id="nomor_induk" name="nomor_induk" class="form-control" placeholder="Masukkan NIP atau NISN">
                    </div>
                    <div class="form-group">
                        <label for="kelas_jabatan">Kelas / Jabatan</label>
                        <input type="text" id="kelas_jabatan" name="kelas_jabatan" class="form-control" placeholder="Contoh: XII MIPA 1 / Guru Kimia">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="no_telepon">No. Telepon</label>
                        <input type="text" id="no_telepon" name="no_telepon" class="form-control" placeholder="Contoh: 081234567890">
                    </div>
                    <div class="form-group">
                        <label for="password">Kata Sandi Awal <span class="req">*</span></label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Pengguna</button>
            </div>
        </form>
    </dialog>

    <!-- Modal Edit Pengguna -->
    @foreach($users as $u)
        <dialog id="editUserModal-{{ $u->id }}" closedby="any" aria-labelledby="editUserTitle-{{ $u->id }}" class="modal-dialog">
            <form action="{{ route('users.update', $u) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h3 id="editUserTitle-{{ $u->id }}">Edit Pengguna: {{ $u->name }}</h3>
                    <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name_{{ $u->id }}">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" id="name_{{ $u->id }}" name="name" class="form-control" value="{{ $u->name }}" placeholder="Masukkan nama lengkap" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="email_{{ $u->id }}">Alamat Email <span class="req">*</span></label>
                        <input type="email" id="email_{{ $u->id }}" name="email" class="form-control" value="{{ $u->email }}" placeholder="Masukkan alamat email" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="role_{{ $u->id }}">Hak Akses Sistem <span class="req">*</span></label>
                            <select id="role_{{ $u->id }}" name="role" class="form-control" required>
                                <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>User (Peminjam)</option>
                                <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin (Pengelola)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="user_type_{{ $u->id }}">Kategori Pengguna <span class="req">*</span></label>
                            <select id="user_type_{{ $u->id }}" name="user_type" class="form-control" required>
                                <option value="Siswa" {{ $u->user_type === 'Siswa' ? 'selected' : '' }}>Siswa</option>
                                <option value="Guru" {{ $u->user_type === 'Guru' ? 'selected' : '' }}>Guru</option>
                                <option value="Staf" {{ $u->user_type === 'Staf' ? 'selected' : '' }}>Staf</option>
                                <option value="Pengelola" {{ $u->user_type === 'Pengelola' ? 'selected' : '' }}>Pengelola Sarpras</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nomor_induk_{{ $u->id }}">NIP / NISN</label>
                            <input type="text" id="nomor_induk_{{ $u->id }}" name="nomor_induk" class="form-control" value="{{ $u->nomor_induk }}" placeholder="Masukkan NIP atau NISN">
                        </div>
                        <div class="form-group">
                            <label for="kelas_jabatan_{{ $u->id }}">Kelas / Jabatan</label>
                            <input type="text" id="kelas_jabatan_{{ $u->id }}" name="kelas_jabatan" class="form-control" value="{{ $u->kelas_jabatan }}" placeholder="Masukkan kelas atau jabatan">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="no_telepon_{{ $u->id }}">No. Telepon</label>
                            <input type="text" id="no_telepon_{{ $u->id }}" name="no_telepon" class="form-control" value="{{ $u->no_telepon }}" placeholder="Contoh: 081234567890">
                        </div>
                        <div class="form-group">
                            <label for="password_{{ $u->id }}">Reset Kata Sandi (Kosongkan jika tetap)</label>
                            <input type="password" id="password_{{ $u->id }}" name="password" class="form-control" placeholder="Isi jika ingin mengganti kata sandi">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </dialog>
    @endforeach
@endsection
