@extends('layouts.app')

@section('title', 'Laporan Inventaris & Peminjaman')
@section('page_title', 'Laporan Inventaris & Peminjaman Sekolah')
@section('page_subtitle', 'Rekapitulasi data peminjaman, pengembalian, serta posisi stok inventaris untuk pelaporan sekolah')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span aria-current="page">Laporan & Export</span>
@endsection

@section('content')
    <!-- Filter & Tombol Cetak / Export -->
    <div class="card">
        <div class="card-header">
            <h3>Filter Periode & Jenis Laporan</h3>
            <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                <a href="{{ route('reports.print', ['start_date' => $startDate, 'end_date' => $endDate, 'status' => $status, 'category_id' => $categoryId, 'type' => 'loans']) }}" target="_blank" class="btn btn-primary btn-sm">
                    Cetak Laporan Peminjaman
                </a>
                <a href="{{ route('reports.print', ['category_id' => $categoryId, 'type' => 'items']) }}" target="_blank" class="btn btn-outline btn-sm">
                    Cetak Daftar Barang
                </a>
                <a href="{{ route('reports.export-csv', ['start_date' => $startDate, 'end_date' => $endDate, 'status' => $status, 'category_id' => $categoryId, 'type' => 'loans']) }}" class="btn btn-success btn-sm">
                    Export CSV Peminjaman
                </a>
                <a href="{{ route('reports.export-csv', ['category_id' => $categoryId, 'type' => 'items']) }}" class="btn btn-outline btn-sm">
                    Export CSV Data Barang
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('reports.index') }}" method="GET" class="filter-bar">
                <div>
                    <label for="start_date" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); display:block;">Dari Tanggal Pinjam</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="{{ $startDate }}" placeholder="Pilih tanggal awal" autofocus>
                </div>
                <div>
                    <label for="end_date" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); display:block;">Sampai Tanggal</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="{{ $endDate }}" placeholder="Pilih tanggal akhir">
                </div>
                <div>
                    <label for="report_category_id" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); display:block;">Kategori Barang</label>
                    <select id="report_category_id" name="category_id" class="form-control">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ (string) $categoryId === (string) $cat->id ? 'selected' : '' }}>
                                {{ $cat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="report_status" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); display:block;">Status Peminjaman</label>
                    <select id="report_status" name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="menunggu_persetujuan" {{ $status === 'menunggu_persetujuan' ? 'selected' : '' }}>Menunggu Persetujuan</option>
                        <option value="dipinjam" {{ $status === 'dipinjam' ? 'selected' : '' }}>Sedang Dipinjam</option>
                        <option value="menunggu_pengembalian" {{ $status === 'menunggu_pengembalian' ? 'selected' : '' }}>Verifikasi Kembali</option>
                        <option value="dikembalikan" {{ $status === 'dikembalikan' ? 'selected' : '' }}>Sudah Dikembalikan</option>
                        <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div style="padding-top:1.15rem;">
                    <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabel 1: Rekap Transaksi Peminjaman -->
    <div class="card">
        <div class="card-header">
            <h3>Rekapitulasi Peminjaman & Pengembalian ({{ format_number($loans->total()) }} Transaksi)</h3>
        </div>
        <div tabindex="0" role="region" aria-labelledby="report-loans-caption" class="table-wrapper">
            <table class="data-table" style="--label-2: 'Peminjam'; --label-3: 'Barang'; --label-4: 'Jumlah'; --label-5: 'Tanggal Pinjam'; --label-6: 'Detail Kembali'; --label-7: 'Status';">
                <caption id="report-loans-caption">Laporan transaksi peminjaman dan pengembalian</caption>
                <thead>
                    <tr>
                        <th scope="col">Kode Pinjam</th>
                        <th scope="col">Peminjam</th>
                        <th scope="col">Barang</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Tgl Pinjam</th>
                        <th scope="col">Tgl & Kondisi Kembali</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $b)
                        <tr>
                            <th scope="row">{{ $b->kode_peminjaman }}</th>
                            <td>
                                <strong>{{ $b->user->name ?? '-' }}</strong>
                                <div style="font-size:0.75rem; color:var(--text-muted);">{{ $b->user->user_type ?? '' }} • {{ $b->user->kelas_jabatan ?? '' }}</div>
                            </td>
                            <td>
                                <div>{{ $b->item->nama_barang ?? '-' }}</div>
                                <small style="color:var(--text-muted);">{{ $b->item->kode_barang ?? '' }} ({{ $b->item->category->nama_kategori ?? '-' }})</small>
                            </td>
                            <td>{{ format_number($b->jumlah) }} {{ $b->item->satuan ?? 'Unit' }}</td>
                            <td>{{ format_date($b->tanggal_pinjam) }}</td>
                            <td>
                                @if($b->returnLog?->tanggal_dikembalikan)
                                    <div>{{ format_datetime($b->returnLog->tanggal_dikembalikan) }}</div>
                                    <small style="color:var(--text-muted);">
                                        Kondisi: {{ $b->returnLog->kondisi_label }}
                                        @if($b->returnLog->terlambat_hari > 0)
                                            • Terlambat {{ format_number($b->returnLog->terlambat_hari) }} hr
                                        @endif
                                    </small>
                                @else
                                    Batas: {{ format_date($b->rencana_kembali) }}
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $b->status === 'dikembalikan' ? 'badge-success' : ($b->status === 'ditolak' ? 'badge-danger' : 'badge-primary') }}">
                                    {{ $b->status_label }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding:1.75rem; color:var(--text-muted);">
                                Tidak ada data peminjaman pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($loans->hasPages())
            <div class="pagination-wrap">
                {{ $loans->links() }}
            </div>
        @endif
    </div>

    <!-- Tabel 2: Rekap Posisi Stok & Kondisi Barang -->
    <div class="card">
        <div class="card-header">
            <h3>📦 Rekapitulasi Posisi Stok & Kondisi Barang ({{ format_number($itemsSummary->total()) }} Jenis Barang)</h3>
        </div>
        <div tabindex="0" role="region" aria-labelledby="report-items-caption" class="table-wrapper">
            <table class="data-table" style="--label-2: 'Nama Barang'; --label-3: 'Kategori'; --label-4: 'Lokasi'; --label-5: 'Stok Total'; --label-6: 'Tersedia'; --label-7: 'Kondisi';">
                <caption id="report-items-caption">Laporan posisi stok dan kondisi barang inventaris</caption>
                <thead>
                    <tr>
                        <th scope="col">Kode Barang</th>
                        <th scope="col">Nama Barang</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Lokasi</th>
                        <th scope="col">Stok Total</th>
                        <th scope="col">Tersedia / Dipinjam</th>
                        <th scope="col">Kondisi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($itemsSummary as $item)
                        <tr>
                            <th scope="row">{{ $item->kode_barang }}</th>
                            <td><strong>{{ $item->nama_barang }}</strong> ({{ $item->merk ?: '-' }})</td>
                            <td>{{ $item->category->nama_kategori ?? '-' }}</td>
                            <td>{{ $item->lokasi }}</td>
                            <td>{{ format_number($item->stok_total) }} {{ $item->satuan }}</td>
                            <td>{{ format_number($item->stok_tersedia) }} Tersedia / {{ format_number($item->sedang_dipinjam) }} Dipinjam</td>
                            <td>{{ $item->kondisi_label }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($itemsSummary->hasPages())
            <div class="pagination-wrap">
                {{ $itemsSummary->links() }}
            </div>
        @endif
    </div>
@endsection
