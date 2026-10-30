@extends('layouts.app')

@section('title', 'Dashboard Peminjam')
@section('page_title', 'Selamat Datang, ' . auth()->user()->name)
@section('page_subtitle', 'Pantau status peminjaman barang Anda dan temukan perlengkapan inventaris sekolah yang tersedia')

@section('breadcrumbs')
    <span aria-current="page">Dashboard</span>
@endsection

@section('content')
    <div class="stats-grid">
        <div class="stat-card" style="border-left: 3px solid var(--warning);">
            <div class="stat-header">
                <span class="stat-label">Menunggu Persetujuan</span>
                <div class="stat-icon warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ format_number($stats['menunggu_persetujuan']) }}</div>
            <div class="stat-sub">Pengajuan sedang ditinjau oleh tim sarpras</div>
        </div>

        <div class="stat-card" style="border-left: 3px solid var(--primary);">
            <div class="stat-header">
                <span class="stat-label">Sedang Dipinjam</span>
                <div class="stat-icon primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ format_number($stats['sedang_dipinjam']) }}</div>
            <div class="stat-sub">Barang aktif dalam tanggung jawab Anda</div>
        </div>

        <div class="stat-card" style="border-left: 3px solid var(--success);">
            <div class="stat-header">
                <span class="stat-label">Selesai Dikembalikan</span>
                <div class="stat-icon success">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
            </div>
            <div class="stat-value">{{ format_number($stats['dikembalikan']) }}</div>
            <div class="stat-sub">Tercatat rapi di riwayat pengembalian</div>
        </div>

        <div class="stat-card" style="border-left: 3px solid {{ $stats['terlambat'] > 0 ? 'var(--danger)' : '#cbd5e1' }};">
            <div class="stat-header">
                <span class="stat-label">Keterlambatan</span>
                <div class="stat-icon {{ $stats['terlambat'] > 0 ? 'danger' : 'info' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
            </div>
            <div class="stat-value" style="color: {{ $stats['terlambat'] > 0 ? 'var(--danger)' : 'var(--text-main)' }};">
                {{ format_number($stats['terlambat']) }}
            </div>
            <div class="stat-sub">
                {{ $stats['terlambat'] > 0 ? 'Harap segera kembalikan barang ke sarpras' : 'Tertib, tidak ada keterlambatan' }}
            </div>
        </div>
    </div>

    <!-- Peminjaman Aktif Saya -->
    <div class="card">
        <div class="card-header">
            <h3>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                </svg>
                <span>Status Peminjaman Aktif Saya</span>
            </h3>
            <div style="display:flex; gap:0.5rem;">
                <a href="{{ route('items.index') }}" class="btn btn-primary btn-sm">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>
                    <span>Ajukan Pinjam Baru</span>
                </a>
                <a href="{{ route('loans.index') }}" class="btn btn-outline btn-sm">Riwayat Lengkap</a>
            </div>
        </div>

        @if($activeLoans->isEmpty())
            <div class="card-body" style="text-align:center; padding: 2.5rem 1.5rem; color: var(--text-muted); font-size:0.88rem;">
                Anda belum memiliki peminjaman barang yang sedang aktif saat ini.
                <div style="margin-top:0.85rem;">
                    <a href="{{ route('items.index') }}" class="btn btn-primary btn-sm">Jelajahi Katalog Barang →</a>
                </div>
            </div>
        @else
            <div tabindex="0" role="region" aria-labelledby="user-active-loans-caption" class="table-wrapper">
                <table class="data-table" style="--label-2: 'Barang'; --label-3: 'Jumlah'; --label-4: 'Jadwal'; --label-5: 'Status'; --label-6: 'Aksi';">
                    <caption id="user-active-loans-caption">Daftar peminjaman barang yang sedang aktif milik Anda</caption>
                    <thead>
                        <tr>
                            <th scope="col">Kode Pinjam</th>
                            <th scope="col">Nama Barang</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col">Tgl Pinjam & Rencana Kembali</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activeLoans as $b)
                            <tr>
                                <th scope="row">
                                    <span style="font-family:monospace; font-weight:700; color:var(--primary); font-size:0.8rem;">
                                        {{ $b->kode_peminjaman }}
                                    </span>
                                </th>
                                <td>
                                    <div style="font-weight:700;">{{ $b->item->nama_barang }}</div>
                                    <small style="color:var(--text-muted);">Lokasi: {{ $b->item->lokasi }}</small>
                                </td>
                                <td>{{ format_number($b->jumlah) }} {{ $b->item->satuan }}</td>
                                <td>
                                    <div>{{ format_date($b->tanggal_pinjam) }} → {{ format_date($b->rencana_kembali) }}</div>
                                    @if($b->is_terlambat)
                                        <span class="badge badge-danger">Melewati batas kembali!</span>
                                    @endif
                                </td>
                                <td>
                                    @if($b->status === 'menunggu_persetujuan')
                                        <span class="badge badge-warning"><span class="badge-dot"></span><span>Menunggu Persetujuan Admin</span></span>
                                    @elseif($b->status === 'dipinjam')
                                        <span class="badge badge-primary"><span class="badge-dot"></span><span>Sedang Dipinjam</span></span>
                                    @elseif($b->status === 'menunggu_pengembalian')
                                        <span class="badge badge-info"><span class="badge-dot"></span><span>Verifikasi Pengembalian</span></span>
                                    @endif
                                </td>
                                <td>
                                    @if($b->status === 'dipinjam')
                                        <form action="{{ route('loans.request-return', $b) }}" method="POST" onsubmit="return confirm('Ajukan pengembalian untuk barang ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-success btn-sm">Kembalikan Barang</button>
                                        </form>
                                    @elseif($b->status === 'menunggu_pengembalian')
                                        <small style="color:var(--text-muted); font-size:0.78rem;">Silakan serahkan fisik barang ke petugas sarpras</small>
                                    @else
                                        <small style="color:var(--text-muted); font-size:0.78rem;">Menunggu persetujuan</small>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Katalog Cepat Barang Tersedia -->
    <div class="card">
        <div class="card-header">
            <h3>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                </svg>
                <span>Barang Siap Dipinjam</span>
            </h3>
            <a href="{{ route('items.index') }}" class="btn btn-outline btn-sm">Lihat Semua Katalog →</a>
        </div>
        <div class="card-body">
            <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.15rem;">
                @foreach($availableItems as $item)
                    <div style="border: 1px solid var(--border-color); border-radius: var(--radius); padding: 1.15rem; display:flex; flex-direction:column; justify-content:space-between; gap:0.85rem; background:#fff; transition:transform 0.15s ease, box-shadow 0.15s ease;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--shadow-md)';" onmouseout="this.style.transform='none'; this.style.boxShadow='none';">
                        <div>
                            <div style="display:flex; justify-content:space-between; align-items:center; gap:0.5rem; margin-bottom:0.6rem;">
                                <span class="badge badge-neutral">{{ $item->category->nama_kategori }}</span>
                                <span class="badge badge-success"><span class="badge-dot"></span><span>Stok: {{ format_number($item->stok_tersedia) }} {{ $item->satuan }}</span></span>
                            </div>
                            <h4 style="font-size:0.95rem; font-weight:700; margin-bottom:0.25rem; color:var(--text-main);">{{ $item->nama_barang }}</h4>
                            <p style="font-size:0.78rem; color:var(--text-muted); line-height:1.4;">
                                Kode: <strong>{{ $item->kode_barang }}</strong> • Lokasi: {{ $item->lokasi }}
                            </p>
                        </div>
                        <div style="display:flex; gap:0.5rem; margin-top:0.25rem;">
                            <a href="{{ route('items.show', $item) }}" class="btn btn-outline btn-sm" style="flex:1;">Detail</a>
                            <button type="button" class="btn btn-primary btn-sm" style="flex:1.4;" onclick="document.getElementById('borrowModal-{{ $item->id }}').showModal()">
                                Pinjam Barang
                            </button>
                        </div>
                    </div>

                    <!-- Modal Pinjam Langsung -->
                    <dialog id="borrowModal-{{ $item->id }}" closedby="any" aria-labelledby="borrowModalTitle-{{ $item->id }}" class="modal-dialog">
                        <form action="{{ route('loans.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="item_id" value="{{ $item->id }}">
                            <div class="modal-header">
                                <h3 id="borrowModalTitle-{{ $item->id }}">Form Pengajuan Peminjaman</h3>
                                <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
                            </div>
                            <div class="modal-body">
                                <div style="padding:0.75rem 1rem; background:var(--primary-soft); border-radius:8px; border:1px solid var(--primary-border); margin-bottom:1rem;">
                                    <div style="font-weight:700; color:var(--primary); font-size:0.9rem;">{{ $item->nama_barang }}</div>
                                    <small style="color:#1e40af; font-size:0.78rem;">Kode: {{ $item->kode_barang }} • Stok Tersedia: {{ format_number($item->stok_tersedia) }} {{ $item->satuan }} • Lokasi: {{ $item->lokasi }}</small>
                                </div>

                                <div class="form-group">
                                    <label for="jumlah_{{ $item->id }}">Jumlah Pinjam ({{ $item->satuan }}) <span class="req">*</span></label>
                                    <input type="number" id="jumlah_{{ $item->id }}" name="jumlah" class="form-control" value="1" min="1" max="{{ $item->stok_tersedia }}" placeholder="Masukkan jumlah unit" required autofocus>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="tgl_pinjam_{{ $item->id }}">Tanggal Pinjam <span class="req">*</span></label>
                                        <input type="date" id="tgl_pinjam_{{ $item->id }}" name="tanggal_pinjam" class="form-control" value="{{ date('Y-m-d') }}" placeholder="Pilih tanggal pinjam" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="rencana_kembali_{{ $item->id }}">Rencana Tanggal Kembali <span class="req">*</span></label>
                                        <input type="date" id="rencana_kembali_{{ $item->id }}" name="rencana_kembali" class="form-control" value="{{ date('Y-m-d', strtotime('+2 days')) }}" placeholder="Pilih tanggal rencana kembali" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="keperluan_{{ $item->id }}">Keperluan Peminjaman <span class="req">*</span></label>
                                    <textarea id="keperluan_{{ $item->id }}" name="keperluan" class="form-control" rows="3" placeholder="Contoh: Digunakan untuk kegiatan praktikum siswa di laboratorium..." required></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Batal</button>
                                <button type="submit" class="btn btn-primary">Kirim Pengajuan Pinjam</button>
                            </div>
                        </form>
                    </dialog>
                @endforeach
            </div>
        </div>
    </div>
@endsection
