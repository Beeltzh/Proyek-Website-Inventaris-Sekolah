<?php $__env->startSection('title', 'Log Pengembalian Barang (Return Logs)'); ?>
<?php $__env->startSection('page_title', 'Log Pengembalian Barang (Return Logs)'); ?>
<?php $__env->startSection('page_subtitle', 'Catatan detail pemeriksaan fisik, ketepatan waktu, dan penerima setiap pengembalian barang inventaris'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <a href="<?php echo e(route('dashboard')); ?>">Dashboard</a>
    <span class="sep">/</span>
    <span aria-current="page">Log Pengembalian</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header">
            <form action="<?php echo e(route('return-logs.index')); ?>" method="GET" class="filter-bar">
                <input type="text" name="search" class="form-control" style="width:260px;" placeholder="Cari kode pinjam, peminjam, barang..." value="<?php echo e($search); ?>" autofocus>

                <select name="condition" class="form-control" style="width:180px;">
                    <option value="">Semua Kondisi Kembali</option>
                    <option value="baik" <?php echo e($condition === 'baik' ? 'selected' : ''); ?>>Kondisi Baik</option>
                    <option value="rusak_ringan" <?php echo e($condition === 'rusak_ringan' ? 'selected' : ''); ?>>Rusak Ringan</option>
                    <option value="rusak_berat" <?php echo e($condition === 'rusak_berat' ? 'selected' : ''); ?>>Rusak Berat</option>
                </select>

                <select name="punctuality" class="form-control" style="width:175px;">
                    <option value="">Semua Ketepatan Waktu</option>
                    <option value="on_time" <?php echo e(in_array($punctuality, ['on_time', 'tepat_waktu'], true) ? 'selected' : ''); ?>>Tepat Waktu</option>
                    <option value="late" <?php echo e(in_array($punctuality, ['late', 'terlambat'], true) ? 'selected' : ''); ?>>Terlambat Kembali</option>
                </select>

                <button type="submit" class="btn btn-outline">Filter</button>
                <?php if($search || $condition || $punctuality): ?>
                    <a href="<?php echo e(route('return-logs.index')); ?>" class="btn btn-outline">Reset</a>
                <?php endif; ?>
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
                    <?php $__empty_1 = true; $__currentLoopData = $returnLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <th scope="row">
                                <span style="font-family:monospace; font-weight:700; color:var(--primary); font-size:0.8rem;">
                                    <?php echo e($log->loan->kode_peminjaman ?? '-'); ?>

                                </span>
                            </th>
                            <td>
                                <div style="font-weight:700;"><?php echo e($log->loan->user->name ?? '-'); ?></div>
                                <small style="color:var(--text-muted);"><?php echo e($log->loan->user->user_type ?? ''); ?> • <?php echo e($log->loan->user->kelas_jabatan ?? ''); ?></small>
                            </td>
                            <td>
                                <div style="font-weight:600;"><?php echo e($log->loan->item->nama_barang ?? '-'); ?></div>
                                <small style="color:var(--text-muted);"><?php echo e(format_number($log->jumlah_kembali)); ?> <?php echo e($log->loan->item->satuan ?? 'Unit'); ?> (<?php echo e($log->loan->item->kode_barang ?? ''); ?>)</small>
                            </td>
                            <td>
                                <div><strong><?php echo e(format_datetime($log->tanggal_dikembalikan)); ?></strong></div>
                                <small style="color:var(--text-muted);">Batas: <?php echo e(format_date($log->loan->rencana_kembali)); ?></small>
                            </td>
                            <td>
                                <?php if($log->terlambat_hari > 0): ?>
                                    <span class="badge badge-danger"><span class="badge-dot"></span><span>Terlambat <?php echo e(format_number($log->terlambat_hari)); ?> Hari</span></span>
                                <?php else: ?>
                                    <span class="badge badge-success"><span class="badge-dot"></span><span>Tepat Waktu</span></span>
                                <?php endif; ?>
                            </td>
                            <td style="max-width:250px;">
                                <?php if($log->kondisi_saat_kembali === 'baik'): ?>
                                    <span class="badge badge-success"><span class="badge-dot"></span><span>Baik</span></span>
                                <?php elseif($log->kondisi_saat_kembali === 'rusak_ringan'): ?>
                                    <span class="badge badge-warning"><span class="badge-dot"></span><span>Rusak Ringan</span></span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><span class="badge-dot"></span><span>Rusak Berat</span></span>
                                <?php endif; ?>
                                <?php if($log->catatan_pengembalian): ?>
                                    <div style="font-size:0.78rem; color:var(--text-muted); margin-top:0.25rem;">
                                        <?php echo e($log->catatan_pengembalian); ?>

                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-size:0.84rem; font-weight:600;"><?php echo e($log->receiver->name ?? 'Admin'); ?></div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 2.25rem; color:var(--text-muted);">
                                Belum ada catatan pengembalian barang di dalam <code>return_logs</code>.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($returnLogs->hasPages()): ?>
            <div class="pagination-wrap">
                <?php echo e($returnLogs->links()); ?>

            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\UTILITY\htdocs\SistemInventarisSekolahBryant\resources\views/return_logs/index.blade.php ENDPATH**/ ?>