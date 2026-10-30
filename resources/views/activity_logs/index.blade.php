@extends('layouts.app')

@section('title', 'Jejak Audit Sistem (Activity Logs)')
@section('page_title', 'Jejak Audit Aktivitas Sistem (Activity Logs)')
@section('page_subtitle', 'Rekam jejak otentikasi, perubahan data CRUD, serta transaksi peminjaman & pengembalian oleh Admin maupun User')

@section('breadcrumbs')
    <a href="{{ route('dashboard') }}">Dashboard</a>
    <span class="sep">/</span>
    <span aria-current="page">Log Aktivitas (Audit)</span>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <form action="{{ route('activity-logs.index') }}" method="GET" class="filter-bar">
                <input type="text" name="search" class="form-control" style="width:240px;" placeholder="Cari deskripsi, nama pelaku, IP..." value="{{ $search }}" autofocus>

                <select name="module" class="form-control" style="width:155px;">
                    <option value="">Semua Modul</option>
                    @foreach($modules as $m)
                        <option value="{{ $m }}" {{ $module === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>

                <select name="action" class="form-control" style="width:175px;">
                    <option value="">Semua Jenis Aksi</option>
                    @foreach($actions as $a)
                        <option value="{{ $a }}" {{ $action === $a ? 'selected' : '' }}>{{ $a }}</option>
                    @endforeach
                </select>

                <input type="date" name="date" class="form-control" style="width:155px;" value="{{ $date }}" placeholder="Filter tanggal aktivitas">

                <button type="submit" class="btn btn-outline">Filter</button>
                @if($search || $module || $action || $date)
                    <a href="{{ route('activity-logs.index') }}" class="btn btn-outline">Reset</a>
                @endif
            </form>
        </div>

        <div tabindex="0" role="region" aria-labelledby="activity-logs-caption" class="table-wrapper">
            <table class="data-table" style="--label-2: 'Pelaku'; --label-3: 'Modul & Aksi'; --label-4: 'Deskripsi Aktivitas'; --label-5: 'Alamat IP'; --label-6: 'Detail Data';">
                <caption id="activity-logs-caption">Daftar jejak audit aktivitas pengguna di dalam sistem inventaris</caption>
                <thead>
                    <tr>
                        <th scope="col">Waktu Kejadian</th>
                        <th scope="col">Pelaku & Role</th>
                        <th scope="col">Modul & Aksi</th>
                        <th scope="col">Deskripsi Aktivitas</th>
                        <th scope="col">IP Address</th>
                        <th scope="col">Payload</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <th scope="row">
                                <div style="font-size:0.82rem;">{{ format_datetime($log->created_at, true) }}</div>
                                <small style="color:var(--text-muted); font-weight:500;">{{ $log->created_at->diffForHumans() }}</small>
                            </th>
                            <td>
                                <div style="font-weight:700;">{{ $log->nama_user ?: 'Tamu / Sistem' }}</div>
                                <span class="badge {{ $log->role_user === 'admin' ? 'badge-primary' : 'badge-neutral' }}">
                                    {{ strtoupper($log->role_user ?: 'GUEST') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-neutral">{{ $log->module }}</span>
                                @php
                                    $actionBadge = match (true) {
                                        in_array($log->action, ['CREATE', 'REGISTER', 'LOAN_APPROVE', 'RETURN_CONFIRM'], true) => 'badge-success',
                                        in_array($log->action, ['DELETE', 'LOGIN_FAILED', 'LOAN_REJECT'], true) => 'badge-danger',
                                        in_array($log->action, ['UPDATE', 'LOAN_REQUEST', 'RETURN_REQUEST'], true) => 'badge-warning',
                                        default => 'badge-info',
                                    };
                                @endphp
                                <span class="badge {{ $actionBadge }}">{{ $log->action }}</span>
                            </td>
                            <td style="max-width:380px;">{{ $log->description }}</td>
                            <td style="font-family:monospace; font-size:0.8rem; color:var(--text-muted);">
                                {{ $log->ip_address ?: '-' }}
                            </td>
                            <td>
                                @if(! empty($log->properties))
                                    <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('auditDetailModal-{{ $log->id }}').showModal()">
                                        Detail Payload
                                    </button>
                                @else
                                    <span style="color:var(--text-muted); font-size:0.78rem;">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 2.25rem; color:var(--text-muted);">
                                Tidak ada data jejak audit yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="pagination-wrap">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Detail Payload Audit -->
    @foreach($logs as $log)
        @if(! empty($log->properties))
            <dialog id="auditDetailModal-{{ $log->id }}" closedby="any" aria-labelledby="auditDetailTitle-{{ $log->id }}" class="modal-dialog">
                <div class="modal-header">
                    <h3 id="auditDetailTitle-{{ $log->id }}">Detail Jejak Audit #{{ format_number($log->id) }}</h3>
                    <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
                </div>
                <div class="modal-body">
                    <div style="font-size:0.84rem; margin-bottom:0.85rem; display:grid; gap:0.3rem;">
                        <div><strong>Waktu:</strong> {{ format_datetime($log->created_at, true) }}</div>
                        <div><strong>Pelaku:</strong> {{ $log->nama_user }} ({{ strtoupper($log->role_user ?? '-') }})</div>
                        <div><strong>Modul / Aksi:</strong> {{ $log->module }} / {{ $log->action }}</div>
                        <div><strong>IP & Agen:</strong> {{ $log->ip_address ?: '-' }} — <span style="color:var(--text-muted);">{{ $log->user_agent ?: '-' }}</span></div>
                    </div>
                    <label style="font-size:0.78rem; font-weight:700; color:var(--text-muted); display:block; margin-bottom:0.35rem;">
                        Perubahan Data / Properties (JSON):
                    </label>
                    <pre style="background:#0f172a; color:#e2e8f0; padding:1rem; border-radius:10px; font-size:0.8rem; overflow-x:auto; line-height:1.45;">{{ json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="this.closest('dialog').close()">Tutup</button>
                </div>
            </dialog>
        @endif
    @endforeach
@endsection
