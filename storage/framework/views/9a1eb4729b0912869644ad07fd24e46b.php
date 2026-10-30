<?php $__env->startSection('title', 'Laporan Inventaris & Peminjaman'); ?>
<?php $__env->startSection('page_title', 'Laporan Inventaris & Peminjaman Sekolah'); ?>
<?php $__env->startSection('page_subtitle', 'Rekapitulasi data peminjaman, pengembalian, serta posisi stok inventaris untuk pelaporan sekolah'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <a href="<?php echo e(route('dashboard')); ?>">Dashboard</a>
    <span class="sep">/</span>
    <span aria-current="page">Laporan & Export</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Filter & Tombol Cetak / Export -->
    <div class="card">
        <div class="card-header">
            <h3>Filter Periode & Jenis Laporan</h3>
            <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                <a href="<?php echo e(route('reports.print', ['start_date' => $startDate, 'end_date' => $endDate, 'status' => $status, 'category_id' => $categoryId, 'type' => 'loans'])); ?>" target="_blank" class="btn btn-primary btn-sm">
                    Cetak Laporan Peminjaman
                </a>
                <a href="<?php echo e(route('reports.print', ['category_id' => $categoryId, 'type' => 'items'])); ?>" target="_blank" class="btn btn-outline btn-sm">
                    Cetak Daftar Barang
                </a>
                <a href="<?php echo e(route('reports.export-csv', ['start_date' => $startDate, 'end_date' => $endDate, 'status' => $status, 'category_id' => $categoryId, 'type' => 'loans'])); ?>" class="btn btn-success btn-sm">
                    Export CSV Peminjaman
                </a>
                <a href="<?php echo e(route('reports.export-csv', ['category_id' => $categoryId, 'type' => 'items'])); ?>" class="btn btn-outline btn-sm">
                    Export CSV Data Barang
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('reports.index')); ?>" method="GET" class="filter-bar">
                <div>
                    <label for="start_date" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); display:block;">Dari Tanggal Pinjam</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo e($startDate); ?>" placeholder="Pilih tanggal awal" autofocus>
                </div>
                <div>
                    <label for="end_date" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); display:block;">Sampai Tanggal</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo e($endDate); ?>" placeholder="Pilih tanggal akhir">
                </div>
                <div>
                    <label for="report_category_id" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); display:block;">Kategori Barang</label>
                    <select id="report_category_id" name="category_id" class="form-control">
                        <option value="">Semua Kategori</option>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cat->id); ?>" <?php echo e((string) $categoryId === (string) $cat->id ? 'selected' : ''); ?>>
                                <?php echo e($cat->nama_kategori); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div>
                    <label for="report_status" style="font-size:0.75rem; font-weight:600; color:var(--text-muted); display:block;">Status Peminjaman</label>
                    <select id="report_status" name="status" class="form-control">
                        <option value="">Semua Status</option>
                        <option value="menunggu_persetujuan" <?php echo e($status === 'menunggu_persetujuan' ? 'selected' : ''); ?>>Menunggu Persetujuan</option>
                        <option value="dipinjam" <?php echo e($status === 'dipinjam' ? 'selected' : ''); ?>>Sedang Dipinjam</option>
                        <option value="menunggu_pengembalian" <?php echo e($status === 'menunggu_pengembalian' ? 'selected' : ''); ?>>Verifikasi Kembali</option>
                        <option value="dikembalikan" <?php echo e($status === 'dikembalikan' ? 'selected' : ''); ?>>Sudah Dikembalikan</option>
                        <option value="ditolak" <?php echo e($status === 'ditolak' ? 'selected' : ''); ?>>Ditolak</option>
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
            <h3>Rekapitulasi Peminjaman & Pengembalian (<?php echo e(format_number($loans->total())); ?> Transaksi)</h3>
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
                    <?php $__empty_1 = true; $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <th scope="row"><?php echo e($b->kode_peminjaman); ?></th>
                            <td>
                                <strong><?php echo e($b->user->name ?? '-'); ?></strong>
                                <div style="font-size:0.75rem; color:var(--text-muted);"><?php echo e($b->user->user_type ?? ''); ?> • <?php echo e($b->user->kelas_jabatan ?? ''); ?></div>
                            </td>
                            <td>
                                <div><?php echo e($b->item->nama_barang ?? '-'); ?></div>
                                <small style="color:var(--text-muted);"><?php echo e($b->item->kode_barang ?? ''); ?> (<?php echo e($b->item->category->nama_kategori ?? '-'); ?>)</small>
                            </td>
                            <td><?php echo e(format_number($b->jumlah)); ?> <?php echo e($b->item->satuan ?? 'Unit'); ?></td>
                            <td><?php echo e(format_date($b->tanggal_pinjam)); ?></td>
                            <td>
                                <?php if($b->returnLog?->tanggal_dikembalikan): ?>
                                    <div><?php echo e(format_datetime($b->returnLog->tanggal_dikembalikan)); ?></div>
                                    <small style="color:var(--text-muted);">
                                        Kondisi: <?php echo e($b->returnLog->kondisi_label); ?>

                                        <?php if($b->returnLog->terlambat_hari > 0): ?>
                                            • Terlambat <?php echo e(format_number($b->returnLog->terlambat_hari)); ?> hr
                                        <?php endif; ?>
                                    </small>
                                <?php else: ?>
                                    Batas: <?php echo e(format_date($b->rencana_kembali)); ?>

                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge <?php echo e($b->status === 'dikembalikan' ? 'badge-success' : ($b->status === 'ditolak' ? 'badge-danger' : 'badge-primary')); ?>">
                                    <?php echo e($b->status_label); ?>

                                </span>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding:1.75rem; color:var(--text-muted);">
                                Tidak ada data peminjaman pada rentang tanggal ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if($loans->hasPages()): ?>
            <div class="pagination-wrap">
                <?php echo e($loans->links()); ?>

            </div>
        <?php endif; ?>
    </div>

    <!-- Tabel 2: Rekap Posisi Stok & Kondisi Barang -->
    <div class="card">
        <div class="card-header">
            <h3>📦 Rekapitulasi Posisi Stok & Kondisi Barang (<?php echo e(format_number($itemsSummary->total())); ?> Jenis Barang)</h3>
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
                    <?php $__currentLoopData = $itemsSummary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <th scope="row"><?php echo e($item->kode_barang); ?></th>
                            <td><strong><?php echo e($item->nama_barang); ?></strong> (<?php echo e($item->merk ?: '-'); ?>)</td>
                            <td><?php echo e($item->category->nama_kategori ?? '-'); ?></td>
                            <td><?php echo e($item->lokasi); ?></td>
                            <td><?php echo e(format_number($item->stok_total)); ?> <?php echo e($item->satuan); ?></td>
                            <td><?php echo e(format_number($item->stok_tersedia)); ?> Tersedia / <?php echo e(format_number($item->sedang_dipinjam)); ?> Dipinjam</td>
                            <td><?php echo e($item->kondisi_label); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <?php if($itemsSummary->hasPages()): ?>
            <div class="pagination-wrap">
                <?php echo e($itemsSummary->links()); ?>

            </div>
        <?php endif; ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\UTILITY\htdocs\SistemInventarisSekolahBryant\resources\views/reports/index.blade.php ENDPATH**/ ?>