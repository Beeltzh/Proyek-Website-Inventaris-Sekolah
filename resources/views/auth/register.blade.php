<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Peminjam — Sistem Inventaris Sekolah | SMAN 1 Nusantara</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f1f5f9;
            background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
            background-size: 24px 24px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            color: #0f172a;
            -webkit-font-smoothing: antialiased;
        }
        .register-card {
            max-width: 600px;
            width: 100%;
            background: #ffffff;
            border-radius: 16px;
            padding: 2.25rem 2.5rem;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
            border: 1px solid #e2e8f0;
        }
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            padding: 0.35rem 0.75rem;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .register-card h2 {
            font-size: 1.35rem;
            font-weight: 800;
            margin-bottom: 0.35rem;
            color: #0f172a;
            letter-spacing: -0.01em;
        }
        .register-card .subtitle {
            color: #64748b;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .form-group { margin-bottom: 1rem; }
        .form-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.35rem;
        }
        .req { color: #dc2626; font-weight: 700; margin-left: 2px; }
        .form-control {
            width: 100%;
            padding: 0.58rem 0.85rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.88rem;
            font-family: inherit;
            color: #0f172a;
            transition: all 0.15s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }
        .btn-primary {
            width: 100%;
            padding: 0.72rem 1rem;
            background: #2563eb;
            color: #fff;
            border: 1px solid #2563eb;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            margin-top: 0.5rem;
            transition: background-color 0.15s ease;
        }
        .btn-primary:hover { background: #1d4ed8; }
        .alert-error {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.82rem;
            margin-bottom: 1.15rem;
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        @media (max-width: 580px) {
            .register-card { padding: 1.75rem 1.25rem; }
            .form-row { grid-template-columns: 1fr; gap: 0; }
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="brand-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            <span>SMAN 1 Nusantara • SIM-Sarpras</span>
        </div>

        <h2>Registrasi Peminjam Baru</h2>
        <p class="subtitle">Daftarkan akun resmi Guru, Siswa, atau Staf untuk mengajukan peminjaman sarana prasarana sekolah.</p>

        @if($errors->any())
            <div class="alert-error">
                <ul style="padding-left:1.1rem;">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Nama Lengkap <span class="req">*</span></label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="Contoh: Budi Santoso, S.Pd / Siti Rahma" required autofocus>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Alamat Email <span class="req">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="nama@sekolah.sch.id" required>
                </div>
                <div class="form-group">
                    <label for="user_type">Status Warga Sekolah <span class="req">*</span></label>
                    <select id="user_type" name="user_type" class="form-control" required>
                        <option value="Siswa" {{ old('user_type') === 'Siswa' ? 'selected' : '' }}>Siswa</option>
                        <option value="Guru" {{ old('user_type') === 'Guru' ? 'selected' : '' }}>Guru / Tenaga Pengajar</option>
                        <option value="Staf" {{ old('user_type') === 'Staf' ? 'selected' : '' }}>Staf Tata Usaha</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="nomor_induk">NISN / NIP <span class="req">*</span></label>
                    <input type="text" id="nomor_induk" name="nomor_induk" class="form-control" value="{{ old('nomor_induk') }}" placeholder="Contoh: 0084512390" required>
                </div>
                <div class="form-group">
                    <label for="kelas_jabatan">Kelas / Jabatan <span class="req">*</span></label>
                    <input type="text" id="kelas_jabatan" name="kelas_jabatan" class="form-control" value="{{ old('kelas_jabatan') }}" placeholder="Contoh: XII MIPA 1 / Guru Fisika" required>
                </div>
            </div>

            <div class="form-group">
                <label for="no_telepon">No. WhatsApp / Telepon (Opsional)</label>
                <input type="text" id="no_telepon" name="no_telepon" class="form-control" value="{{ old('no_telepon') }}" placeholder="Contoh: 081234567890">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Kata Sandi <span class="req">*</span></label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                </div>
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Kata Sandi <span class="req">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Ulangi kata sandi" required>
                </div>
            </div>

            <button type="submit" class="btn-primary">Daftar Akun Peminjam</button>
        </form>

        <p style="text-align:center; margin-top:1.35rem; font-size:0.83rem; color:#64748b;">
            Sudah memiliki akun?
            <a href="{{ route('login') }}" style="color:#2563eb; font-weight:700; text-decoration:none;">Masuk di sini</a>
        </p>
    </div>
</body>
</html>
