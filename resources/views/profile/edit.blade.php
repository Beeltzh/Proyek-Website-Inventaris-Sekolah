@extends('layouts.app')

@section('title', 'Profil Akun Saya')
@section('page_title', 'Pengaturan Profil Akun')
@section('page_subtitle', 'Perbarui identitas diri, informasi kelas/jabatan, dan keamanan kata sandi Anda')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span aria-current="page">Profil Akun</span>
@endsection

@section('content')
    <div class="card" style="max-width: 720px;">
        <div class="card-header">
            <h3>Informasi Akun Pengguna</h3>
            <span class="badge badge-primary"><span class="badge-dot"></span><span>{{ strtoupper($user->role) }} — {{ $user->user_type }}</span></span>
        </div>
        <div class="card-body">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="form-row">
                    <div class="form-group">
                        <label for="name">Nama Lengkap <span class="req">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap Anda" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="email">Alamat Email <span class="req">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" placeholder="contoh@sekolah.sch.id" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nomor_induk">NIP / NISN</label>
                        <input type="text" id="nomor_induk" name="nomor_induk" class="form-control" value="{{ old('nomor_induk', $user->nomor_induk) }}" placeholder="Masukkan NIP atau NISN">
                    </div>
                    <div class="form-group">
                        <label for="kelas_jabatan">Kelas / Jabatan</label>
                        <input type="text" id="kelas_jabatan" name="kelas_jabatan" class="form-control" value="{{ old('kelas_jabatan', $user->kelas_jabatan) }}" placeholder="Contoh: XII MIPA 1 / Guru Fisika">
                    </div>
                </div>

                <div class="form-group">
                    <label for="no_telepon">Nomor Telepon / WhatsApp</label>
                    <input type="text" id="no_telepon" name="no_telepon" class="form-control" value="{{ old('no_telepon', $user->no_telepon) }}" placeholder="Contoh: 081234567890">
                </div>

                <hr style="border:none; border-top:1px solid var(--border-color); margin: 1.25rem 0;">

                <h4 style="font-size:0.88rem; font-weight:700; margin-bottom:0.75rem;">Ubah Kata Sandi (Opsional)</h4>
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Kata Sandi Baru</label>
                        <input type="password" id="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah">
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi baru">
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:0.5rem;">
                    <small style="color:var(--text-muted);">Kolom bertanda <span class="req">*</span> wajib diisi.</small>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan Profil</button>
                </div>
            </form>
        </div>
    </div>
@endsection

