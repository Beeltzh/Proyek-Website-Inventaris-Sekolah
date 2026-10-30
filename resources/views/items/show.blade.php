@extends('layouts.app')

@section('title', 'Detail Barang — ' . $item->nama_barang)
@section('page_title', 'Detail Barang Inventaris')
@section('page_subtitle', 'Informasi spesifikasi, lokasi penyimpanan, dan riwayat peminjaman barang')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <a href="{{ route('items.index') }}">{{ auth()->user()->isAdmin() ? 'Data Barang' : 'Katalog Barang' }}</a>
    <span class="sep">/</span>
    <span aria-current="page">{{ $item->kode_barang }}</span>
@endsection

@section('content')
    <div style="margin-bottom:1.25rem;">
        <a href="{{ route('items.index') }}" class="btn btn-outline btn-sm">← Kembali ke Daftar Barang</a>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        <div class="card" style="margin-bottom:0;">
            <div class="card-body" style="text-align:center;">
                @if($item->foto)
                    <img src="{{ asset($item->foto) }}" alt="{{ $item->nama_barang }}" style="width:100%; max-height:240px; object-fit:cover; border-radius:10px; margin-bottom:1rem; border:1px solid var(--border-color);">
                @else
                    <div style="width:100%; height:160px; border-radius:10px; background:#eff6ff; border:1px solid var(--primary-border); display:flex; align-items:center; justify-content:center; color:#2563eb; margin-bottom:1rem;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                            <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                            <line x1="12" y1="22.08" x2="12" y2="12"></line>
                        </svg>
                    </div>
                @endif

                <span class="badge badge-primary" style="margin-bottom:0.5rem;"><span class="badge-dot"></span><span>{{ $item->kode_barang }}</span></span>
                <h3 style="font-size:1.15rem; font-weight:700; margin-bottom:0.35rem; color:var(--text-main);">{{ $item->nama_barang }}</h3>
                <p style="font-size:0.83rem; color:var(--text-muted); margin-bottom:1.15rem;">
                    Kategori: <strong>{{ $item->category->nama_kategori ?? '-' }}</strong>
                </p>

                @if($item->stok_tersedia > 0 && $item->kondisi !== 'rusak_berat')
                    <button type="button" class="btn btn-primary" style="width:100%;" onclick="document.getElementById('borrowDetailModal').showModal()">
                        Ajukan Peminjaman Barang Ini
                    </button>
                @else
                    <button type="button" class="btn btn-outline" style="width:100%;" disabled>
                        Barang Sedang Tidak Tersedia
                    </button>
                @endif
            </div>
        </div>

        <div class="card" style="margin-bottom:0;">
            <div class="card-header">
                <h3>Spesifikasi & Ketersediaan Barang</h3>
            </div>
            <div class="card-body">
                <div style="display:grid; grid-template-columns: repeat(2, 1fr); gap:1.15rem; margin-bottom:1.25rem;">
                    <div>
                        <small style="color:var(--text-muted); display:block; font-size:0.78rem;">Merk / Pabrikan</small>
                        <strong>{{ $item->merk ?: '-' }}</strong>
                    </div>
                    <div>
                        <small style="color:var(--text-muted); display:block; font-size:0.78rem;">Lokasi Penyimpanan</small>
                        <strong>{{ $item->lokasi }}</strong>
                    </div>
                    <div>
                        <small style="color:var(--text-muted); display:block; font-size:0.78rem;">Stok Tersedia / Total</small>
                        <strong style="font-size:1.05rem; color:{{ $item->stok_tersedia > 0 ? 'var(--success)' : 'var(--danger)' }};">
                            {{ format_number($item->stok_tersedia) }}
                        </strong>
                        <span style="font-size:0.84rem; color:var(--text-muted);">dari {{ format_number($item->stok_total) }} {{ $item->satuan }} ({{ format_number($item->sedang_dipinjam) }} dipinjam)</span>
                    </div>
                    <div>
                        <small style="color:var(--text-muted); display:block; font-size:0.78rem;">Kondisi Fisik</small>
                        @if($item->kondisi === 'baik')
                            <span class="badge badge-success"><span class="badge-dot"></span><span>Baik</span></span>
                        @elseif($item->kondisi === 'rusak_ringan')
                            <span class="badge badge-warning"><span class="badge-dot"></span><span>Rusak Ringan</span></span>
                        @else
                            <span class="badge badge-danger"><span class="badge-dot"></span><span>Rusak Berat</span></span>
                        @endif
                    </div>
                </div>

                <div style="padding:0.9rem 1rem; background:#f8fafc; border-radius:8px; border:1px solid var(--border-color);">
                    <small style="color:var(--text-muted); display:block; margin-bottom:0.25rem; font-weight:600; font-size:0.78rem;">Deskripsi / Kelengkapan:</small>
                    <p style="font-size:0.86rem; line-height:1.5;">{{ $item->deskripsi ?: 'Tidak ada catatan spesifikasi tambahan.' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Peminjaman Barang Ini -->
    <div class="card">
        <div class="card-header">
            <h3>Riwayat Peminjaman Barang Ini</h3>
        </div>
        <div tabindex="0" role="region" aria-labelledby="item-history-caption" class="table-wrapper">
            <table class="data-table" style="--label-2: 'Peminjam'; --label-3: 'Jumlah'; --label-4: 'Tanggal Pinjam'; --label-5: 'Tanggal Kembali'; --label-6: 'Status';">
                <caption id="item-history-caption">Riwayat peminjaman untuk {{ $item->nama_barang }}</caption>
                <thead>
                    <tr>
                        <th scope="col">Kode Peminjaman</th>
                        <th scope="col">Peminjam</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Tgl Pinjam</th>
                        <th scope="col">Tgl Kembali</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($item->loans as $b)
                        <tr>
                            <th scope="row">
                                <span style="font-family:monospace; font-weight:700; color:var(--primary); font-size:0.8rem;">
                                    {{ $b->kode_peminjaman }}
                                </span>
                            </th>
                            <td>{{ $b->user->name }} ({{ $b->user->user_type }})</td>
                            <td>{{ format_number($b->jumlah) }} {{ $item->satuan }}</td>
                            <td>{{ format_date($b->tanggal_pinjam) }}</td>
                            <td>{{ $b->returnLog?->tanggal_dikembalikan ? format_datetime($b->returnLog->tanggal_dikembalikan) : 'Batas: ' . format_date($b->rencana_kembali) }}</td>
                            <td>
                                @if($b->status === 'dikembalikan')
                                    <span class="badge badge-success"><span class="badge-dot"></span><span>{{ $b->status_label }}</span></span>
                                @elseif($b->status === 'dipinjam')
                                    <span class="badge badge-primary"><span class="badge-dot"></span><span>{{ $b->status_label }}</span></span>
                                @elseif($b->status === 'menunggu_persetujuan')
                                    <span class="badge badge-warning"><span class="badge-dot"></span><span>{{ $b->status_label }}</span></span>
                                @elseif($b->status === 'menunggu_pengembalian')
                                    <span class="badge badge-info"><span class="badge-dot"></span><span>{{ $b->status_label }}</span></span>
                                @else
                                    <span class="badge badge-danger"><span class="badge-dot"></span><span>{{ $b->status_label }}</span></span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:1.75rem; color:var(--text-muted);">
                                Barang ini belum pernah dipinjam.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($item->stok_tersedia > 0 && $item->kondisi !== 'rusak_berat')
        <dialog id="borrowDetailModal" closedby="any" aria-labelledby="borrowDetailTitle" class="modal-dialog">
            <form action="{{ route('loans.store') }}" method="POST">
                @csrf
                <input type="hidden" name="item_id" value="{{ $item->id }}">
                @if(auth()->user()->isAdmin())
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                @endif
                <div class="modal-header">
                    <h3 id="borrowDetailTitle">Ajukan Peminjaman: {{ $item->nama_barang }}</h3>
                    <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="jumlah">Jumlah Pinjam (Maks: {{ format_number($item->stok_tersedia) }} {{ $item->satuan }}) <span class="req">*</span></label>
                        <input type="number" id="jumlah" name="jumlah" class="form-control" value="1" min="1" max="{{ $item->stok_tersedia }}" placeholder="Masukkan jumlah unit" required autofocus>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tanggal_pinjam">Tanggal Pinjam <span class="req">*</span></label>
                            <input type="date" id="tanggal_pinjam" name="tanggal_pinjam" class="form-control" value="{{ date('Y-m-d') }}" placeholder="Pilih tanggal pinjam" required>
                        </div>
                        <div class="form-group">
                            <label for="rencana_kembali">Rencana Tanggal Kembali <span class="req">*</span></label>
                            <input type="date" id="rencana_kembali" name="rencana_kembali" class="form-control" value="{{ date('Y-m-d', strtotime('+2 days')) }}" placeholder="Pilih tanggal rencana kembali" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="keperluan">Keperluan Peminjaman <span class="req">*</span></label>
                        <textarea id="keperluan" name="keperluan" class="form-control" rows="3" placeholder="Tuliskan keperluan peminjaman..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Pengajuan</button>
                </div>
            </form>
        </dialog>
    @endif
@endsection
