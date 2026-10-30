<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Sistem Inventaris Sekolah | SMAN 1 Nusantara</title>
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
            padding: 1.5rem;
            color: #0f172a;
            -webkit-font-smoothing: antialiased;
        }
        .auth-wrapper {
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            max-width: 940px;
            width: 100%;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.04);
            border: 1px solid #e2e8f0;
        }
        .auth-banner {
            background: linear-gradient(160deg, #1e3a8a 0%, #1e293b 100%);
            color: #f8fafc;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #e2e8f0;
            padding: 0.4rem 0.85rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 600;
            width: fit-content;
            margin-bottom: 1.75rem;
        }
        .brand-header {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            margin-bottom: 1.25rem;
        }
        .brand-logo-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #2563eb;
            border: 1px solid rgba(255, 255, 255, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            flex-shrink: 0;
        }
        .brand-header-text h3 {
            font-size: 1.05rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.01em;
            line-height: 1.2;
        }
        .brand-header-text span {
            font-size: 0.75rem;
            color: #94a3b8;
            display: block;
        }
        .auth-banner h1 {
            font-size: 1.65rem;
            font-weight: 800;
            line-height: 1.3;
            margin-bottom: 0.85rem;
            color: #ffffff;
            letter-spacing: -0.02em;
        }
        .auth-banner p {
            color: #cbd5e1;
            font-size: 0.88rem;
            line-height: 1.6;
        }
        .feature-list {
            list-style: none;
            margin-top: 2rem;
            display: grid;
            gap: 0.85rem;
        }
        .feature-list li {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 0.84rem;
            color: #e2e8f0;
            line-height: 1.45;
        }
        .feature-icon-box {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            background: rgba(255, 255, 255, 0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #93c5fd;
            margin-top: 1px;
        }
        .auth-form-side {
            padding: 2.75rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #ffffff;
        }
        .auth-form-side h2 {
            font-size: 1.35rem;
            font-weight: 800;
            margin-bottom: 0.35rem;
            color: #0f172a;
            letter-spacing: -0.01em;
        }
        .auth-form-side .subtitle {
            color: #64748b;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }
        .form-group { margin-bottom: 1.1rem; }
        .form-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.35rem;
        }
        .form-control {
            width: 100%;
            padding: 0.65rem 0.85rem;
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
            color: #ffffff;
            border: 1px solid #2563eb;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            transition: background-color 0.15s ease;
        }
        .btn-primary:hover { background: #1d4ed8; }
        .demo-box {
            margin-top: 1.5rem;
            padding: 1rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
        }
        .demo-box-title {
            font-size: 0.74rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #475569;
            margin-bottom: 0.65rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }
        .demo-buttons {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
        }
        .demo-btn {
            padding: 0.5rem 0.4rem;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            border-radius: 6px;
            font-size: 0.76rem;
            font-weight: 600;
            cursor: pointer;
            color: #1e293b;
            transition: all 0.15s ease;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
        }
        .demo-btn svg {
            color: #2563eb;
        }
        .demo-btn:hover {
            border-color: #2563eb;
            color: #2563eb;
            background: #eff6ff;
        }
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.82rem;
            margin-bottom: 1.15rem;
        }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        @media (max-width: 768px) {
            .auth-wrapper { grid-template-columns: 1fr; }
            .auth-banner { display: none; }
            .auth-form-side { padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-banner">
            <div>
                <div class="brand-header">
                    <div class="brand-logo-icon" aria-hidden="true">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>
                    </div>
                    <div class="brand-header-text">
                        <h3>SMAN 1 Nusantara</h3>
                        <span>Sistem Sarpras & Inventarisasi</span>
                    </div>
                </div>

                <div class="brand-badge">
                    <span>Portal Layanan Inventaris Sekolah</span>
                </div>

                <h1>Pengelolaan Sarana & Prasarana Terpadu</h1>
                <p>Pencatatan data barang, kategori inventaris, hingga pemantauan peminjaman dan pengembalian oleh Guru maupun Siswa secara transparan, tertib, dan akuntabel.</p>

                <ul class="feature-list">
                    <li>
                        <div class="feature-icon-box">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            </svg>
                        </div>
                        <span>Pendataan barang dan stok otomatis berdasarkan kategori dan lokasi</span>
                    </li>
                    <li>
                        <div class="feature-icon-box">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="9 10 4 15 9 20"></polyline>
                                <path d="M20 4v7a4 4 0 0 1-4 4H4"></path>
                            </svg>
                        </div>
                        <span>Alur peminjaman & pengembalian tertib dengan verifikasi admin</span>
                    </li>
                    <li>
                        <div class="feature-icon-box">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <polyline points="14 2 14 8 20 8"></polyline>
                            </svg>
                        </div>
                        <span>Laporan inventaris siap cetak & ekspor ke spreadsheet CSV/Excel</span>
                    </li>
                </ul>
            </div>
            <div style="font-size: 0.76rem; color: #94a3b8; margin-top: 2rem;">
                &copy; {{ date('Y') }} SMAN 1 Nusantara • Sistem Sarpras Sekolah
            </div>
        </div>

        <div class="auth-form-side">
            <h2>Masuk ke Sistem</h2>
            <p class="subtitle">Gunakan akun Pengelola Sarpras, Guru, atau Siswa.</p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" id="loginForm">
                @csrf
                <div class="form-group">
                    <label for="email">Alamat Email <span style="color:#dc2626; font-weight:700;">*</span></label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="contoh@sekolah.sch.id" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Kata Sandi <span style="color:#dc2626; font-weight:700;">*</span></label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Masukkan kata sandi Anda" required>
                </div>

                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; font-size:0.82rem;">
                    <label style="display:flex; align-items:center; gap:0.4rem; cursor:pointer; color:#475569;">
                        <input type="checkbox" name="remember"> Ingat saya di perangkat ini
                    </label>
                </div>

                <button type="submit" class="btn-primary">Masuk Sekarang</button>
            </form>

            <div class="demo-box">
                <div class="demo-box-title">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                    </svg>
                    <span>Pilihan Akun Uji Coba (Klik untuk mengisi):</span>
                </div>
                <div class="demo-buttons">
                    <button type="button" class="demo-btn" onclick="fillDemo('admin@sekolah.sch.id', 'password123')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                        <span>Admin Sarpras</span>
                    </button>
                    <button type="button" class="demo-btn" onclick="fillDemo('guru@sekolah.sch.id', 'password123')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span>Akun Guru</span>
                    </button>
                    <button type="button" class="demo-btn" onclick="fillDemo('siswa@sekolah.sch.id', 'password123')">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
                            <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
                        </svg>
                        <span>Akun Siswa</span>
                    </button>
                </div>
            </div>

            <p style="text-align:center; margin-top:1.35rem; font-size:0.83rem; color:#64748b;">
                Belum memiliki akun peminjam?
                <a href="{{ route('register') }}" style="color:#2563eb; font-weight:700; text-decoration:none;">Daftar Akun Guru/Siswa</a>
            </p>
        </div>
    </div>

    <script>
        function fillDemo(email, password) {
            document.getElementById('email').value = email;
            document.getElementById('password').value = password;
            document.getElementById('loginForm').submit();
        }
    </script>
</body>
</html>
