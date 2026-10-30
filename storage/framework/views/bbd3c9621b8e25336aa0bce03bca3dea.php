<?php $__env->startSection('title', 'Dashboard Admin'); ?>
<?php $__env->startSection('page_title', 'Dashboard Pengelola Inventaris'); ?>
<?php $__env->startSection('page_subtitle', 'Ringkasan data barang, kondisi aset, peminjaman, pengembalian, dan jejak audit sistem'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <span aria-current="page">Dashboard</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Total Jenis Barang</span>
                <div class="stat-icon primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                    </svg>
                </div>
            </div>
            <div class="stat-value"><?php echo e(format_number($stats['total_jenis_barang'])); ?></div>
            <div class="stat-sub">Dalam <?php echo e(format_number($stats['total_kategori'])); ?> kategori inventaris</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-label">Stok Tersedia / Unit</span>
                <div class="stat-icon info">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                </div>
            </div>
            <div class="stat-value">
                <?php echo e(format_number($stats['stok_tersedia'])); ?>

                <span style="font-size:1.05rem; color:var(--text-muted); font-weight:600;">/ <?php echo e(format_number($stats['total_unit_barang'])); ?></span>
            </div>
            <div class="stat-sub"><?php echo e(format_number($stats['total_unit_barang'] - $stats['stok_tersedia'])); ?> unit sedang dalam masa pinjam</div>
        </div>

        <div class="stat-card" style="border-left: 3px solid var(--warning);">
            <div class="stat-header">
                <span class="stat-label">Perlu Tindakan Admin</span>
                <div class="stat-icon warning">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
            </div>
            <div class="stat-value" style="color: var(--warning);">
                <?php echo e(format_number($stats['menunggu_persetujuan'] + $stats['menunggu_pengembalian'])); ?>

            </div>
            <div class="stat-sub">
                <?php echo e(format_number($stats['menunggu_persetujuan'])); ?> pengajuan pinjam • <?php echo e(format_number($stats['menunggu_pengembalian'])); ?> verifikasi kembali
            </div>
        </div>

        <div class="stat-card" style="border-left: 3px solid <?php echo e($stats['terlambat'] > 0 ? 'var(--danger)' : 'var(--success)'); ?>;">
            <div class="stat-header">
                <span class="stat-label">Peminjaman Aktif</span>
                <div class="stat-icon <?php echo e($stats['terlambat'] > 0 ? 'danger' : 'success'); ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                    </svg>
                </div>
            </div>
            <div class="stat-value"><?php echo e(format_number($stats['sedang_dipinjam'])); ?></div>
            <div class="stat-sub">
                <?php if($stats['terlambat'] > 0): ?>
                    <span style="color:var(--danger); font-weight:700;"><?php echo e(format_number($stats['terlambat'])); ?> transaksi melewati batas kembali</span>
                <?php else: ?>
                    <?php echo e(format_number($stats['total_pengembalian'])); ?> transaksi selesai dikembalikan
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Baris Verifikasi & Kondisi Fisik -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
        <!-- Permintaan Menunggu Verifikasi Admin -->
        <div class="card" style="margin-bottom:0; grid-column: span 2;">
            <div class="card-header">
                <h3>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    <span>Permintaan Menunggu Verifikasi Admin</span>
                </h3>
                <a href="<?php echo e(route('loans.index')); ?>" class="btn btn-outline btn-sm">Lihat Semua →</a>
            </div>
            <?php if($pendingRequests->isEmpty()): ?>
                <div class="card-body" style="text-align:center; padding: 2.5rem 1.5rem; color: var(--text-muted); font-size:0.88rem;">
                    Tidak ada pengajuan peminjaman atau pengembalian yang menunggu verifikasi saat ini.
                </div>
            <?php else: ?>
                <div tabindex="0" role="region" aria-labelledby="pending-table-caption" class="table-wrapper">
                    <table class="data-table" style="--label-2: 'Barang'; --label-3: 'Tanggal'; --label-4: 'Status'; --label-5: 'Aksi';">
                        <caption id="pending-table-caption">Daftar pengajuan peminjaman dan pengembalian yang menunggu verifikasi admin</caption>
                        <thead>
                            <tr>
                                <th scope="col">Peminjam</th>
                                <th scope="col">Barang & Jumlah</th>
                                <th scope="col">Jadwal Pinjam</th>
                                <th scope="col">Status</th>
                                <th scope="col">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $pendingRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <th scope="row">
                                        <div><?php echo e($req->user->name); ?></div>
                                        <small style="color:var(--text-muted); font-weight:500;"><?php echo e($req->user->user_type); ?> • <?php echo e($req->user->kelas_jabatan); ?></small>
                                    </th>
                                    <td>
                                        <div style="font-weight:600;"><?php echo e($req->item->nama_barang); ?></div>
                                        <small style="color:var(--text-muted);"><?php echo e(format_number($req->jumlah)); ?> <?php echo e($req->item->satuan); ?> (Kode: <?php echo e($req->kode_peminjaman); ?>)</small>
                                    </td>
                                    <td>
                                        <div><?php echo e(format_date($req->tanggal_pinjam)); ?></div>
                                        <small style="color:var(--text-muted);">s/d <?php echo e(format_date($req->rencana_kembali)); ?></small>
                                    </td>
                                    <td>
                                        <?php if($req->status === 'menunggu_persetujuan'): ?>
                                            <span class="badge badge-warning">
                                                <span class="badge-dot"></span>
                                                <span>Menunggu Persetujuan</span>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-info">
                                                <span class="badge-dot"></span>
                                                <span>Verifikasi Kembali</span>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="display:flex; gap:0.4rem; flex-wrap:wrap;">
                                            <?php if($req->status === 'menunggu_persetujuan'): ?>
                                                <form action="<?php echo e(route('loans.approve', $req)); ?>" method="POST">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('PATCH'); ?>
                                                    <button type="submit" class="btn btn-success btn-sm">Setujui</button>
                                                </form>
                                                <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('rejectDialog-<?php echo e($req->id); ?>').showModal()">Tolak</button>
                                            <?php elseif($req->status === 'menunggu_pengembalian'): ?>
                                                <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('returnDialog-<?php echo e($req->id); ?>').showModal()">Konfirmasi Kembali</button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Ringkasan Kondisi Fisik Barang -->
        <div class="card" style="margin-bottom:0;">
            <div class="card-header">
                <h3>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"></path>
                    </svg>
                    <span>Kondisi Fisik Barang</span>
                </h3>
                <a href="<?php echo e(route('items.index')); ?>" class="btn btn-outline btn-sm">Kelola</a>
            </div>
            <div class="card-body">
                <div style="display:grid; gap:0.65rem;">
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:0.65rem 0.85rem; border-radius:8px; background:var(--success-soft); border: 1px solid var(--success-border);">
                        <span style="font-weight:600; font-size:0.84rem; color:var(--success);">Kondisi Baik</span>
                        <strong style="font-size:0.98rem; color:var(--success);"><?php echo e(format_number($kondisiStats['baik'])); ?> Jenis</strong>
                    </div>
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:0.65rem 0.85rem; border-radius:8px; background:var(--warning-soft); border: 1px solid var(--warning-border);">
                        <span style="font-weight:600; font-size:0.84rem; color:var(--warning);">Rusak Ringan</span>
                        <strong style="font-size:0.98rem; color:var(--warning);"><?php echo e(format_number($kondisiStats['rusak_ringan'])); ?> Jenis</strong>
                    </div>
                    <div style="display:flex; align-items:center; justify-content:space-between; padding:0.65rem 0.85rem; border-radius:8px; background:var(--danger-soft); border: 1px solid var(--danger-border);">
                        <span style="font-weight:600; font-size:0.84rem; color:var(--danger);">Rusak Berat</span>
                        <strong style="font-size:0.98rem; color:var(--danger);"><?php echo e(format_number($kondisiStats['rusak_berat'])); ?> Jenis</strong>
                    </div>
                </div>

                <hr style="border:none; border-top:1px solid var(--border-color); margin: 1.15rem 0;">

                <h4 style="font-size:0.75rem; color:var(--text-muted); margin-bottom:0.65rem; text-transform:uppercase; letter-spacing:0.05em; font-weight:700;">
                    Pintasan Manajemen
                </h4>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:0.45rem;">
                    <a href="<?php echo e(route('items.index')); ?>" class="btn btn-outline btn-sm">+ Data Barang</a>
                    <a href="<?php echo e(route('categories.index')); ?>" class="btn btn-outline btn-sm">+ Kategori</a>
                    <a href="<?php echo e(route('return-logs.index')); ?>" class="btn btn-outline btn-sm">Log Pengembalian</a>
                    <a href="<?php echo e(route('activity-logs.index')); ?>" class="btn btn-outline btn-sm">Jejak Audit</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Transaksi & Jejak Audit -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.25rem;">
        <!-- Riwayat Transaksi Peminjaman Terbaru -->
        <div class="card">
            <div class="card-header">
                <h3>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>Transaksi Peminjaman Terbaru</span>
                </h3>
                <a href="<?php echo e(route('loans.index')); ?>" class="btn btn-outline btn-sm">Lihat Semua</a>
            </div>
            <div tabindex="0" role="region" aria-labelledby="recent-loans-caption" class="table-wrapper">
                <table class="data-table" style="--label-2: 'Peminjam'; --label-3: 'Barang'; --label-4: 'Batas Kembali'; --label-5: 'Status';">
                    <caption id="recent-loans-caption">Daftar aktivitas peminjaman barang terbaru</caption>
                    <thead>
                        <tr>
                            <th scope="col">Kode</th>
                            <th scope="col">Peminjam</th>
                            <th scope="col">Barang</th>
                            <th scope="col">Batas / Kembali</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentLoans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <th scope="row">
                                    <span style="font-family:monospace; font-weight:700; color:var(--primary); font-size:0.8rem;">
                                        <?php echo e($b->kode_peminjaman); ?>

                                    </span>
                                </th>
                                <td>
                                    <div><?php echo e($b->user->name); ?></div>
                                    <small style="color:var(--text-muted);"><?php echo e($b->user->user_type); ?></small>
                                </td>
                                <td>
                                    <div style="font-weight:600;"><?php echo e($b->item->nama_barang); ?></div>
                                    <small style="color:var(--text-muted);"><?php echo e(format_number($b->jumlah)); ?> <?php echo e($b->item->satuan); ?></small>
                                </td>
                                <td>
                                    <?php if($b->returnLog?->tanggal_dikembalikan): ?>
                                        <span style="color:var(--success); font-weight:600;"><?php echo e(format_date($b->returnLog->tanggal_dikembalikan)); ?></span>
                                    <?php else: ?>
                                        <?php echo e(format_date($b->rencana_kembali)); ?>

                                        <?php if($b->is_terlambat): ?>
                                            <span class="badge badge-danger">Terlambat</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($b->status === 'menunggu_persetujuan'): ?>
                                        <span class="badge badge-warning"><span class="badge-dot"></span><span><?php echo e($b->status_label); ?></span></span>
                                    <?php elseif($b->status === 'dipinjam'): ?>
                                        <span class="badge badge-primary"><span class="badge-dot"></span><span><?php echo e($b->status_label); ?></span></span>
                                    <?php elseif($b->status === 'menunggu_pengembalian'): ?>
                                        <span class="badge badge-info"><span class="badge-dot"></span><span><?php echo e($b->status_label); ?></span></span>
                                    <?php elseif($b->status === 'dikembalikan'): ?>
                                        <span class="badge badge-success"><span class="badge-dot"></span><span><?php echo e($b->status_label); ?></span></span>
                                    <?php else: ?>
                                        <span class="badge badge-danger"><span class="badge-dot"></span><span><?php echo e($b->status_label); ?></span></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" style="text-align:center; color:var(--text-muted); font-size:0.86rem;">Belum ada riwayat transaksi.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Jejak Audit Terbaru (activity_logs) -->
        <div class="card">
            <div class="card-header">
                <h3>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    <span>Jejak Audit Terbaru</span>
                </h3>
                <a href="<?php echo e(route('activity-logs.index')); ?>" class="btn btn-outline btn-sm">Semua Log →</a>
            </div>
            <div class="card-body" style="padding: 1.15rem 1.25rem;">
                <div style="display:grid; gap:0.75rem;">
                    <?php $__empty_1 = true; $__currentLoopData = $recentActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $act): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div style="padding-bottom:0.65rem; border-bottom:1px solid var(--border-color);">
                            <div style="display:flex; justify-content:space-between; align-items:center; gap:0.5rem; margin-bottom:0.25rem;">
                                <span class="badge badge-neutral"><?php echo e($act->module); ?> • <?php echo e($act->action); ?></span>
                                <small style="color:var(--text-muted); font-size:0.72rem;"><?php echo e(format_datetime($act->created_at)); ?></small>
                            </div>
                            <p style="font-size:0.83rem; color:var(--text-main); line-height:1.4;"><?php echo e($act->description); ?></p>
                            <small style="color:var(--text-muted); font-size:0.73rem;">Oleh: <?php echo e($act->nama_user); ?> (<?php echo e(strtoupper($act->role_user ?? '-')); ?>)</small>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p style="text-align:center; color:var(--text-muted); font-size:0.85rem;">Belum ada log aktivitas tercatat.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Dialogs untuk Tindakan Cepat di Dashboard -->
    <?php $__currentLoopData = $pendingRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $req): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($req->status === 'menunggu_persetujuan'): ?>
            <dialog id="rejectDialog-<?php echo e($req->id); ?>" closedby="any" aria-labelledby="rejectTitle-<?php echo e($req->id); ?>" class="modal-dialog">
                <form action="<?php echo e(route('loans.reject', $req)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <div class="modal-header">
                        <h3 id="rejectTitle-<?php echo e($req->id); ?>">Tolak Pengajuan Peminjaman</h3>
                        <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
                    </div>
                    <div class="modal-body">
                        <p style="font-size:0.86rem; color:var(--text-muted); margin-bottom:1rem;">
                            Anda akan menolak pengajuan <strong><?php echo e($req->item->nama_barang); ?></strong> oleh <strong><?php echo e($req->user->name); ?></strong>.
                        </p>
                        <div class="form-group">
                            <label for="catatan_reject_<?php echo e($req->id); ?>">Alasan Penolakan <span class="req">*</span></label>
                            <textarea id="catatan_reject_<?php echo e($req->id); ?>" name="catatan_admin" class="form-control" rows="3" placeholder="Contoh: Barang sedang digunakan untuk kegiatan ujian sekolah" required autofocus></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
                    </div>
                </form>
            </dialog>
        <?php elseif($req->status === 'menunggu_pengembalian'): ?>
            <dialog id="returnDialog-<?php echo e($req->id); ?>" closedby="any" aria-labelledby="returnTitle-<?php echo e($req->id); ?>" class="modal-dialog">
                <form action="<?php echo e(route('loans.confirm-return', $req)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <div class="modal-header">
                        <h3 id="returnTitle-<?php echo e($req->id); ?>">Konfirmasi Pengembalian Barang</h3>
                        <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
                    </div>
                    <div class="modal-body">
                        <p style="font-size:0.86rem; color:var(--text-muted); margin-bottom:1rem;">
                            Verifikasi pengembalian <strong><?php echo e(format_number($req->jumlah)); ?> <?php echo e($req->item->satuan); ?> <?php echo e($req->item->nama_barang); ?></strong> dari <strong><?php echo e($req->user->name); ?></strong>.
                        </p>
                        <div class="form-group">
                            <label for="kondisi_kembali_<?php echo e($req->id); ?>">Kondisi Barang Saat Dikembalikan <span class="req">*</span></label>
                            <select id="kondisi_kembali_<?php echo e($req->id); ?>" name="kondisi_saat_kembali" class="form-control" required autofocus>
                                <option value="baik">Kondisi Baik (Normal & Lengkap)</option>
                                <option value="rusak_ringan">Rusak Ringan</option>
                                <option value="rusak_berat">Rusak Berat</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="catatan_kembali_<?php echo e($req->id); ?>">Catatan Pengembalian (Opsional)</label>
                            <textarea id="catatan_kembali_<?php echo e($req->id); ?>" name="catatan_pengembalian" class="form-control" rows="2" placeholder="Contoh: Barang telah diterima lengkap dan disimpan di rak sarpras."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Batal</button>
                        <button type="submit" class="btn btn-success">Konfirmasi & Simpan Return Log</button>
                    </div>
                </form>
            </dialog>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\UTILITY\htdocs\SistemInventarisSekolahBryant\resources\views/dashboard/admin.blade.php ENDPATH**/ ?>