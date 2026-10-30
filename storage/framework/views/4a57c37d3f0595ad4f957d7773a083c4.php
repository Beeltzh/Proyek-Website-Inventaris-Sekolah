<?php $__env->startSection('title', auth()->user()->isAdmin() ? 'Data Peminjaman (Loans)' : 'Riwayat Peminjaman Saya'); ?>
<?php $__env->startSection('page_title', auth()->user()->isAdmin() ? 'Manajemen Transaksi Peminjaman (Loans)' : 'Peminjaman Barang Saya'); ?>
<?php $__env->startSection('page_subtitle', auth()->user()->isAdmin() ? 'Kelola pengajuan peminjaman, persetujuan, pemantauan batas waktu, serta pencatatan ke Return Logs' : 'Pantau status pengajuan dan ajukan pengembalian barang yang telah selesai digunakan'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <a href="<?php echo e(route('dashboard')); ?>">Dashboard</a>
    <span class="sep">/</span>
    <span aria-current="page"><?php echo e(auth()->user()->isAdmin() ? 'Data Peminjaman' : 'Peminjaman Saya'); ?></span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $isAdmin = auth()->user()->isAdmin(); ?>

    <!-- Filter Status Cepat (Tab Pills) -->
    <div style="display:flex; gap:0.45rem; flex-wrap:wrap; margin-bottom:1.15rem;">
        <a href="<?php echo e(route('loans.index')); ?>" class="btn btn-sm <?php echo e(! $status ? 'btn-primary' : 'btn-outline'); ?>">
            Semua Status
        </a>
        <a href="<?php echo e(route('loans.index', ['status' => 'menunggu_persetujuan'])); ?>" class="btn btn-sm <?php echo e($status === 'menunggu_persetujuan' ? 'btn-primary' : 'btn-outline'); ?>">
            Menunggu Persetujuan
        </a>
        <a href="<?php echo e(route('loans.index', ['status' => 'dipinjam'])); ?>" class="btn btn-sm <?php echo e($status === 'dipinjam' ? 'btn-primary' : 'btn-outline'); ?>">
            Sedang Dipinjam
        </a>
        <a href="<?php echo e(route('loans.index', ['status' => 'menunggu_pengembalian'])); ?>" class="btn btn-sm <?php echo e($status === 'menunggu_pengembalian' ? 'btn-primary' : 'btn-outline'); ?>">
            Verifikasi Kembali
        </a>
        <a href="<?php echo e(route('loans.index', ['status' => 'overdue'])); ?>" class="btn btn-sm <?php echo e(in_array($status, ['overdue', 'terlambat'], true) ? 'btn-danger' : 'btn-outline'); ?>">
            Terlambat
        </a>
        <a href="<?php echo e(route('loans.index', ['status' => 'dikembalikan'])); ?>" class="btn btn-sm <?php echo e($status === 'dikembalikan' ? 'btn-primary' : 'btn-outline'); ?>">
            Sudah Dikembalikan
        </a>
        <a href="<?php echo e(route('loans.index', ['status' => 'ditolak'])); ?>" class="btn btn-sm <?php echo e($status === 'ditolak' ? 'btn-primary' : 'btn-outline'); ?>">
            Ditolak
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <form action="<?php echo e(route('loans.index')); ?>" method="GET" class="filter-bar">
                <?php if($status): ?>
                    <input type="hidden" name="status" value="<?php echo e($status); ?>">
                <?php endif; ?>
                <input type="text" name="search" class="form-control" style="width:280px;" placeholder="Cari kode pinjam, nama peminjam, barang..." value="<?php echo e($search); ?>" autofocus>
                <button type="submit" class="btn btn-outline">Cari</button>
                <?php if($search || $status): ?>
                    <a href="<?php echo e(route('loans.index')); ?>" class="btn btn-outline">Reset</a>
                <?php endif; ?>
            </form>

            <button type="button" class="btn btn-primary" onclick="document.getElementById('createLoanModal').showModal()">
                + <?php echo e($isAdmin ? 'Catat Peminjaman Baru' : 'Ajukan Peminjaman Barang'); ?>

            </button>
        </div>

        <div tabindex="0" role="region" aria-labelledby="loans-table-caption" class="table-wrapper">
            <table class="data-table" style="--label-2: 'Peminjam'; --label-3: 'Barang'; --label-4: 'Jadwal'; --label-5: 'Keperluan'; --label-6: 'Status'; --label-7: 'Aksi';">
                <caption id="loans-table-caption">Daftar transaksi peminjaman barang inventaris</caption>
                <thead>
                    <tr>
                        <th scope="col">Kode Pinjam</th>
                        <th scope="col">Peminjam</th>
                        <th scope="col">Barang & Jumlah</th>
                        <th scope="col">Jadwal Pinjam & Kembali</th>
                        <th scope="col">Keperluan & Catatan</th>
                        <th scope="col">Status</th>
                        <th scope="col">Tindakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <th scope="row">
                                <span style="font-family:monospace; font-weight:700; color:var(--primary); font-size:0.8rem;"><?php echo e($b->kode_peminjaman); ?></span>
                            </th>
                            <td>
                                <div style="font-weight:700;"><?php echo e($b->user->name); ?></div>
                                <small style="color:var(--text-muted);"><?php echo e($b->user->user_type); ?> • <?php echo e($b->user->kelas_jabatan ?: $b->user->nomor_induk); ?></small>
                            </td>
                            <td>
                                <div style="font-weight:600;"><?php echo e($b->item->nama_barang); ?></div>
                                <small style="color:var(--text-muted);">
                                    <?php echo e(format_number($b->jumlah)); ?> <?php echo e($b->item->satuan); ?> • Kode: <?php echo e($b->item->kode_barang); ?>

                                </small>
                            </td>
                            <td>
                                <div><strong>Pinjam:</strong> <?php echo e(format_date($b->tanggal_pinjam)); ?></div>
                                <div><strong>Batas:</strong> <?php echo e(format_date($b->rencana_kembali)); ?></div>
                                <?php if($b->returnLog?->tanggal_dikembalikan): ?>
                                    <div style="color:var(--success); font-size:0.78rem; font-weight:600;">
                                        Kembali: <?php echo e(format_datetime($b->returnLog->tanggal_dikembalikan)); ?>

                                    </div>
                                <?php elseif($b->is_terlambat): ?>
                                    <span class="badge badge-danger" style="margin-top:0.2rem;"><span class="badge-dot"></span><span>Terlambat</span></span>
                                <?php endif; ?>
                            </td>
                            <td style="max-width:260px;">
                                <div style="font-size:0.83rem;"><?php echo e($b->keperluan); ?></div>
                                <?php if($b->catatan_admin): ?>
                                    <small style="display:block; margin-top:0.25rem; color:#475569; background:#f1f5f9; padding:0.25rem 0.5rem; border-radius:6px;">
                                        Admin: <?php echo e($b->catatan_admin); ?>

                                    </small>
                                <?php endif; ?>
                                <?php if($b->returnLog?->tanggal_dikembalikan): ?>
                                    <small style="display:block; margin-top:0.2rem; color:var(--text-muted);">
                                        Kondisi kembali: <strong><?php echo e($b->returnLog->kondisi_label); ?></strong>
                                        <?php if($b->returnLog->catatan_pengembalian): ?>
                                            (<?php echo e($b->returnLog->catatan_pengembalian); ?>)
                                        <?php endif; ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($b->status === 'menunggu_persetujuan'): ?>
                                    <span class="badge badge-warning"><span class="badge-dot"></span><span>Menunggu Persetujuan</span></span>
                                <?php elseif($b->status === 'dipinjam'): ?>
                                    <span class="badge badge-primary"><span class="badge-dot"></span><span>Sedang Dipinjam</span></span>
                                <?php elseif($b->status === 'menunggu_pengembalian'): ?>
                                    <span class="badge badge-info"><span class="badge-dot"></span><span>Verifikasi Kembali</span></span>
                                <?php elseif($b->status === 'dikembalikan'): ?>
                                    <span class="badge badge-success"><span class="badge-dot"></span><span>Sudah Dikembalikan</span></span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><span class="badge-dot"></span><span>Ditolak</span></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display:flex; gap:0.4rem; flex-wrap:wrap;">
                                    <?php if($isAdmin): ?>
                                        <?php if($b->status === 'menunggu_persetujuan'): ?>
                                            <form action="<?php echo e(route('loans.approve', $b)); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="btn btn-success btn-sm">Setujui</button>
                                            </form>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('rejectModal-<?php echo e($b->id); ?>').showModal()">
                                                Tolak
                                            </button>
                                        <?php elseif(in_array($b->status, ['dipinjam', 'menunggu_pengembalian'], true)): ?>
                                            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('confirmReturnModal-<?php echo e($b->id); ?>').showModal()">
                                                Terima Kembali
                                            </button>
                                        <?php else: ?>
                                            <form action="<?php echo e(route('loans.destroy', $b)); ?>" method="POST" onsubmit="return confirm('Hapus data peminjaman <?php echo e($b->kode_peminjaman); ?>?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-outline btn-sm" style="color:var(--danger); border-color:#fecaca;" title="Hapus Data">Hapus</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?php if($b->status === 'dipinjam'): ?>
                                            <form action="<?php echo e(route('loans.request-return', $b)); ?>" method="POST" onsubmit="return confirm('Ajukan pengembalian barang ini kepada Admin?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="btn btn-success btn-sm">Kembalikan Barang</button>
                                            </form>
                                        <?php elseif($b->status === 'menunggu_pengembalian'): ?>
                                            <span style="font-size:0.78rem; color:var(--info); font-weight:600;">Menunggu Cek Admin</span>
                                        <?php else: ?>
                                            <span style="font-size:0.78rem; color:var(--text-muted);">-</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 2.25rem; color:var(--text-muted);">
                                Belum ada data transaksi peminjaman yang sesuai dengan filter.
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

    <!-- Modal Buat Peminjaman Baru -->
    <dialog id="createLoanModal" closedby="any" aria-labelledby="createLoanTitle" class="modal-dialog">
        <form action="<?php echo e(route('loans.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-header">
                <h3 id="createLoanTitle"><?php echo e($isAdmin ? 'Catat Peminjaman Barang' : 'Form Pengajuan Peminjaman'); ?></h3>
                <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
            </div>
            <div class="modal-body">
                <?php if($isAdmin): ?>
                    <div class="form-group">
                        <label for="user_id">Peminjam (Guru / Siswa / Staf) <span class="req">*</span></label>
                        <select id="user_id" name="user_id" class="form-control" required autofocus>
                            <option value="">-- Pilih Peminjam --</option>
                            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($u->id); ?>"><?php echo e($u->name); ?> (<?php echo e($u->user_type); ?> — <?php echo e($u->kelas_jabatan ?: $u->email); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="item_id">Pilih Barang Inventaris <span class="req">*</span></label>
                    <select id="item_id" name="item_id" class="form-control" required <?php echo e(! $isAdmin ? 'autofocus' : ''); ?>>
                        <option value="">-- Pilih Barang yang Tersedia --</option>
                        <?php $__currentLoopData = $availableItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($item->id); ?>">
                                [<?php echo e($item->kode_barang); ?>] <?php echo e($item->nama_barang); ?> — Tersedia: <?php echo e(format_number($item->stok_tersedia)); ?> <?php echo e($item->satuan); ?> (<?php echo e($item->lokasi); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="jumlah">Jumlah Unit yang Dipinjam <span class="req">*</span></label>
                    <input type="number" id="jumlah" name="jumlah" class="form-control" value="1" min="1" placeholder="Masukkan jumlah unit yang dipinjam" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="tanggal_pinjam">Tanggal Pinjam <span class="req">*</span></label>
                        <input type="date" id="tanggal_pinjam" name="tanggal_pinjam" class="form-control" value="<?php echo e(date('Y-m-d')); ?>" placeholder="Pilih tanggal pinjam" required>
                    </div>
                    <div class="form-group">
                        <label for="rencana_kembali">Rencana Tanggal Kembali <span class="req">*</span></label>
                        <input type="date" id="rencana_kembali" name="rencana_kembali" class="form-control" value="<?php echo e(date('Y-m-d', strtotime('+2 days'))); ?>" placeholder="Pilih rencana tanggal kembali" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="keperluan">Keperluan Peminjaman <span class="req">*</span></label>
                    <textarea id="keperluan" name="keperluan" class="form-control" rows="3" placeholder="Contoh: Praktikum Fisika Kelas XII MIPA 1..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <?php echo e($isAdmin ? 'Simpan & Setujui Langsung' : 'Kirim Pengajuan'); ?>

                </button>
            </div>
        </form>
    </dialog>

    <!-- Modal Admin: Tolak & Konfirmasi Pengembalian -->
    <?php if($isAdmin): ?>
        <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($b->status === 'menunggu_persetujuan'): ?>
                <dialog id="rejectModal-<?php echo e($b->id); ?>" closedby="any" aria-labelledby="rejectModalTitle-<?php echo e($b->id); ?>" class="modal-dialog">
                    <form action="<?php echo e(route('loans.reject', $b)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <div class="modal-header">
                            <h3 id="rejectModalTitle-<?php echo e($b->id); ?>">Tolak Peminjaman <?php echo e($b->kode_peminjaman); ?></h3>
                            <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
                        </div>
                        <div class="modal-body">
                            <p style="font-size:0.875rem; color:var(--text-muted); margin-bottom:1rem;">
                                Berikan alasan penolakan peminjaman <strong><?php echo e($b->item->nama_barang); ?></strong> oleh <strong><?php echo e($b->user->name); ?></strong>:
                            </p>
                            <div class="form-group">
                                <label for="catatan_reject_<?php echo e($b->id); ?>">Alasan Penolakan <span class="req">*</span></label>
                                <textarea id="catatan_reject_<?php echo e($b->id); ?>" name="catatan_admin" class="form-control" rows="3" required autofocus placeholder="Contoh: Barang sedang terjadwal untuk kegiatan ujian praktik..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Batal</button>
                            <button type="submit" class="btn btn-danger">Tolak Pengajuan</button>
                        </div>
                    </form>
                </dialog>
            <?php endif; ?>

            <?php if(in_array($b->status, ['dipinjam', 'menunggu_pengembalian'], true)): ?>
                <dialog id="confirmReturnModal-<?php echo e($b->id); ?>" closedby="any" aria-labelledby="confirmReturnTitle-<?php echo e($b->id); ?>" class="modal-dialog">
                    <form action="<?php echo e(route('loans.confirm-return', $b)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <div class="modal-header">
                            <h3 id="confirmReturnTitle-<?php echo e($b->id); ?>">Verifikasi Pengembalian (Return Log)</h3>
                            <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
                        </div>
                        <div class="modal-body">
                            <div style="padding:0.75rem 1rem; background:#f8fafc; border:1px solid var(--border-color); border-radius:8px; margin-bottom:1rem; font-size:0.85rem;">
                                <div><strong>Kode Pinjam:</strong> <?php echo e($b->kode_peminjaman); ?></div>
                                <div><strong>Peminjam:</strong> <?php echo e($b->user->name); ?> (<?php echo e($b->user->user_type); ?>)</div>
                                <div><strong>Barang:</strong> <?php echo e(format_number($b->jumlah)); ?> <?php echo e($b->item->satuan); ?> <?php echo e($b->item->nama_barang); ?></div>
                            </div>

                            <div class="form-group">
                                <label for="kondisi_kembali_<?php echo e($b->id); ?>">Kondisi Fisik Saat Dikembalikan <span class="req">*</span></label>
                                <select id="kondisi_kembali_<?php echo e($b->id); ?>" name="kondisi_saat_kembali" class="form-control" required autofocus>
                                    <option value="baik">✅ Baik (Lengkap & Berfungsi Normal)</option>
                                    <option value="rusak_ringan">⚠️ Rusak Ringan</option>
                                    <option value="rusak_berat">🚫 Rusak Berat</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="catatan_kembali_<?php echo e($b->id); ?>">Catatan Pemeriksaan Pengembalian (Opsional)</label>
                                <textarea id="catatan_kembali_<?php echo e($b->id); ?>" name="catatan_pengembalian" class="form-control" rows="2" placeholder="Catatan penerimaan barang kembali untuk disimpan di return_logs..."></textarea>
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
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\UTILITY\htdocs\SistemInventarisSekolahBryant\resources\views/loans/index.blade.php ENDPATH**/ ?>