@extends('layouts.app')

@section('title', 'Log Pengembalian Barang (Return Logs)')
@section('page_title', 'Log Pengembalian Barang (Return Logs)')
@section('page_subtitle', 'Catatan detail pemeriksaan fisik, ketepatan waktu, dan penerima setiap pengembalian barang inventaris')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span aria-current="page">Log Pengembalian</span>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <form action="{{ route('return-logs.index') }}" method="GET" class="filter-bar">
                <input type="text" name="search" class="form-control" style="width:260px;" placeholder="Cari kode pinjam, peminjam, barang..." value="{{ $search }}" autofocus>

                <select name="condition" class="form-control" style="width:180px;">
                    <option value="">Semua Kondisi Kembali</option>
                    <option value="baik" {{ $condition === 'baik' ? 'selected' : '' }}>Kondisi Baik</option>
                    <option value="rusak_ringan" {{ $condition === 'rusak_ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                    <option value="rusak_berat" {{ $condition === 'rusak_berat' ? 'selected' : '' }}>Rusak Berat</option>
                </select>

                <select name="punctuality" class="form-control" style="width:175px;">
                    <option value="">Semua Ketepatan Waktu</option>
                    <option value="on_time" {{ in_array($punctuality, ['on_time', 'tepat_waktu'], true) ? 'selected' : '' }}>Tepat Waktu</option>
                    <option value="late" {{ in_array($punctuality, ['late', 'terlambat'], true) ? 'selected' : '' }}>Terlambat Kembali</option>
                </select>

                <button type="submit" class="btn btn-outline">Filter</button>
                @if($search || $condition || $punctuality)
                    <a href="{{ route('return-logs.index') }}" class="btn btn-outline">Reset</a>
                @endif
            </form>
        </div>

        <div tabindex="0" role="region" aria-labelledby="return-logs-caption" class="table-wrapper">
            <table class="data-table" style="--label-2: 'Peminjam'; --label-3: 'Barang & Jumlah'; --label-4: 'Waktu Kembali'; --label-5: 'Ketepatan'; --label-6: 'Kondisi & Catatan'; --label-7: 'Diterima Oleh';">
                <caption id="return-logs-caption">Daftar riwayat detail pengembalian barang inventaris</caption>
                <thead>
                    <tr>
                        <th scope="col">Kode Peminjaman</th>
                        <th scope="col">Peminjam</th>
                        <th scope="col">Barang Dikembalikan</th>
                        <th scope="col">Waktu Pengembalian</th>
                        <th scope="col">Ketepatan Waktu</th>
                        <th scope="col">Kondisi & Catatan</th>
                        <th scope="col">Diverifikasi Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returnLogs as $log)
                        <tr>
                            <th scope="row">
                                <span style="font-family:monospace; font-weight:700; color:var(--primary); font-size:0.8rem;">
                                    {{ $log->loan->kode_peminjaman ?? '-' }}
                                </span>
                            </th>
                            <td>
                                <div style="font-weight:700;">{{ $log->loan->user->name ?? '-' }}</div>
                                <small style="color:var(--text-muted);">{{ $log->loan->user->user_type ?? '' }} • {{ $log->loan->user->kelas_jabatan ?? '' }}</small>
                            </td>
                            <td>
                                <div style="font-weight:600;">{{ $log->loan->item->nama_barang ?? '-' }}</div>
                                <small style="color:var(--text-muted);">{{ format_number($log->jumlah_kembali) }} {{ $log->loan->item->satuan ?? 'Unit' }} ({{ $log->loan->item->kode_barang ?? '' }})</small>
                            </td>
                            <td>
                                <div><strong>{{ format_datetime($log->tanggal_dikembalikan) }}</strong></div>
                                <small style="color:var(--text-muted);">Batas: {{ format_date($log->loan->rencana_kembali) }}</small>
                            </td>
                            <td>
                                @if($log->terlambat_hari > 0)
                                    <span class="badge badge-danger"><span class="badge-dot"></span><span>Terlambat {{ format_number($log->terlambat_hari) }} Hari</span></span>
                                @else
                                    <span class="badge badge-success"><span class="badge-dot"></span><span>Tepat Waktu</span></span>
                                @endif
                            </td>
                            <td style="max-width:250px;">
                                @if($log->kondisi_saat_kembali === 'baik')
                                    <span class="badge badge-success"><span class="badge-dot"></span><span>Baik</span></span>
                                @elseif($log->kondisi_saat_kembali === 'rusak_ringan')
                                    <span class="badge badge-warning"><span class="badge-dot"></span><span>Rusak Ringan</span></span>
                                @else
                                    <span class="badge badge-danger"><span class="badge-dot"></span><span>Rusak Berat</span></span>
                                @endif
                                @if($log->catatan_pengembalian)
                                    <div style="font-size:0.78rem; color:var(--text-muted); margin-top:0.25rem;">
                                        {{ $log->catatan_pengembalian }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div style="font-size:0.84rem; font-weight:600;">{{ $log->receiver->name ?? 'Admin' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 2.25rem; color:var(--text-muted);">
                                Belum ada catatan pengembalian barang di dalam <code>return_logs</code>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($returnLogs->hasPages())
            <div class="pagination-wrap">
                {{ $returnLogs->links() }}
            </div>
        @endif
    </div>
@endsection
