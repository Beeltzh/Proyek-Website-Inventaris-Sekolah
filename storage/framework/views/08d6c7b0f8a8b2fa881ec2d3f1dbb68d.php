<?php $__env->startSection('title', auth()->user()->isAdmin() ? 'Data Barang Inventaris' : 'Katalog Barang Inventaris'); ?>
<?php $__env->startSection('page_title', auth()->user()->isAdmin() ? 'Data Barang Inventaris Sekolah' : 'Katalog Barang Inventaris Sekolah'); ?>
<?php $__env->startSection('page_subtitle', auth()->user()->isAdmin() ? 'Kelola pencatatan barang, lokasi penyimpanan, stok, dan kondisi fisik barang' : 'Cari dan ajukan peminjaman barang inventaris untuk kegiatan belajar mengajar'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <a href="<?php echo e(route('dashboard')); ?>">Dashboard</a>
    <span class="sep">/</span>
    <span aria-current="page"><?php echo e(auth()->user()->isAdmin() ? 'Data Barang' : 'Katalog Barang'); ?></span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $isAdmin = auth()->user()->isAdmin(); ?>

    <div class="card">
        <div class="card-header">
            <form action="<?php echo e(route('items.index')); ?>" method="GET" class="filter-bar">
                <input type="text" name="search" class="form-control" style="width:230px;" placeholder="Cari nama, kode, merk, lokasi..." value="<?php echo e($search); ?>" autofocus>

                <select name="category_id" class="form-control" style="width:190px;">
                    <option value="">Semua Kategori</option>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cat->id); ?>" <?php echo e((string) $categoryId === (string) $cat->id ? 'selected' : ''); ?>>
                            <?php echo e($cat->nama_kategori); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <select name="condition" class="form-control" style="width:160px;">
                    <option value="">Semua Kondisi</option>
                    <option value="baik" <?php echo e($condition === 'baik' ? 'selected' : ''); ?>>Kondisi Baik</option>
                    <option value="rusak_ringan" <?php echo e($condition === 'rusak_ringan' ? 'selected' : ''); ?>>Rusak Ringan</option>
                    <option value="rusak_berat" <?php echo e($condition === 'rusak_berat' ? 'selected' : ''); ?>>Rusak Berat</option>
                </select>

                <select name="availability" class="form-control" style="width:165px;">
                    <option value="">Semua Stok</option>
                    <option value="available" <?php echo e(in_array($availability, ['available', 'tersedia'], true) ? 'selected' : ''); ?>>Siap Dipinjam</option>
                    <option value="empty" <?php echo e(in_array($availability, ['empty', 'habis'], true) ? 'selected' : ''); ?>>Stok Sedang Habis</option>
                </select>

                <button type="submit" class="btn btn-outline">Filter</button>
                <?php if($search || $categoryId || $condition || $availability): ?>
                    <a href="<?php echo e(route('items.index')); ?>" class="btn btn-outline">Reset</a>
                <?php endif; ?>
            </form>

            <?php if($isAdmin): ?>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('addItemModal').showModal()">
                    + Tambah Data Barang
                </button>
            <?php endif; ?>
        </div>

        <div tabindex="0" role="region" aria-labelledby="items-table-caption" class="table-wrapper">
            <table class="data-table" style="--label-2: 'Nama Barang'; --label-3: 'Kategori'; --label-4: 'Lokasi'; --label-5: 'Stok'; --label-6: 'Kondisi'; --label-7: 'Aksi';">
                <caption id="items-table-caption">Daftar barang inventaris sekolah</caption>
                <thead>
                    <tr>
                        <th scope="col">Kode Barang</th>
                        <th scope="col">Nama Barang & Merk</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Lokasi Penyimpanan</th>
                        <th scope="col">Stok Tersedia / Total</th>
                        <th scope="col">Kondisi</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <th scope="row">
                                <span style="font-family:monospace; font-weight:700; color:var(--primary); font-size:0.8rem;"><?php echo e($item->kode_barang); ?></span>
                            </th>
                            <td>
                                <div style="display:flex; align-items:center; gap:0.75rem;">
                                    <?php if($item->foto): ?>
                                        <img src="<?php echo e(asset($item->foto)); ?>" alt="<?php echo e($item->nama_barang); ?>" style="width:40px; height:40px; object-fit:cover; border-radius:6px; border:1px solid var(--border-color);">
                                    <?php else: ?>
                                        <div style="width:40px; height:40px; border-radius:6px; background:#eff6ff; color:#2563eb; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <a href="<?php echo e(route('items.show', $item)); ?>" style="font-weight:700; color:var(--text-main); text-decoration:none;">
                                            <?php echo e($item->nama_barang); ?>

                                        </a>
                                        <div style="font-size:0.76rem; color:var(--text-muted);">
                                            Merk: <?php echo e($item->merk ?: '-'); ?>

                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-neutral"><?php echo e($item->category->nama_kategori ?? '-'); ?></span>
                            </td>
                            <td><?php echo e($item->lokasi); ?></td>
                            <td>
                                <?php if($item->stok_tersedia > 0): ?>
                                    <span style="font-weight:700; color:var(--success);"><?php echo e(format_number($item->stok_tersedia)); ?></span>
                                <?php else: ?>
                                    <span style="font-weight:700; color:var(--danger);">0</span>
                                <?php endif; ?>
                                <span style="color:var(--text-muted);">/ <?php echo e(format_number($item->stok_total)); ?> <?php echo e($item->satuan); ?></span>
                            </td>
                            <td>
                                <?php if($item->kondisi === 'baik'): ?>
                                    <span class="badge badge-success"><span class="badge-dot"></span><span>Baik</span></span>
                                <?php elseif($item->kondisi === 'rusak_ringan'): ?>
                                    <span class="badge badge-warning"><span class="badge-dot"></span><span>Rusak Ringan</span></span>
                                <?php else: ?>
                                    <span class="badge badge-danger"><span class="badge-dot"></span><span>Rusak Berat</span></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display:flex; gap:0.4rem; flex-wrap:wrap;">
                                    <a href="<?php echo e(route('items.show', $item)); ?>" class="btn btn-outline btn-sm">Detail</a>

                                    <?php if($item->stok_tersedia > 0 && $item->kondisi !== 'rusak_berat'): ?>
                                        <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('borrowItemModal-<?php echo e($item->id); ?>').showModal()">
                                            Pinjam
                                        </button>
                                    <?php endif; ?>

                                    <?php if($isAdmin): ?>
                                        <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('editItemModal-<?php echo e($item->id); ?>').showModal()">
                                            Edit
                                        </button>
                                        <form action="<?php echo e(route('items.destroy', $item)); ?>" method="POST" onsubmit="return confirm('Hapus barang <?php echo e($item->nama_barang); ?>?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus Barang">Hapus</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 2.25rem; color:var(--text-muted);">
                                Tidak ada data barang yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($items->hasPages()): ?>
            <div class="pagination-wrap">
                <?php echo e($items->links()); ?>

            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Tambah Barang (Khusus Admin) -->
    <?php if($isAdmin): ?>
        <dialog id="addItemModal" closedby="any" aria-labelledby="addItemTitle" class="modal-dialog">
            <form action="<?php echo e(route('items.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h3 id="addItemTitle">Tambah Data Barang Inventaris</h3>
                    <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
                </div>
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="kode_barang">Kode Barang <span class="req">*</span></label>
                            <input type="text" id="kode_barang" name="kode_barang" class="form-control" placeholder="Contoh: INV-ELK-005" required autofocus>
                        </div>
                        <div class="form-group">
                            <label for="category_id">Kategori Barang <span class="req">*</span></label>
                            <select id="category_id" name="category_id" class="form-control" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($cat->id); ?>"><?php echo e($cat->nama_kategori); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nama_barang">Nama Barang <span class="req">*</span></label>
                        <input type="text" id="nama_barang" name="nama_barang" class="form-control" placeholder="Contoh: Proyektor Epson EB-X500" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="merk">Merk / Brand</label>
                            <input type="text" id="merk" name="merk" class="form-control" placeholder="Contoh: Epson">
                        </div>
                        <div class="form-group">
                            <label for="lokasi">Lokasi Penyimpanan <span class="req">*</span></label>
                            <input type="text" id="lokasi" name="lokasi" class="form-control" placeholder="Contoh: Ruang Sarpras Lt. 1" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="stok_total">Jumlah Stok Total <span class="req">*</span></label>
                            <input type="number" id="stok_total" name="stok_total" class="form-control" value="1" min="0" placeholder="Masukkan jumlah stok" required>
                        </div>
                        <div class="form-group">
                            <label for="satuan">Satuan <span class="req">*</span></label>
                            <select id="satuan" name="satuan" class="form-control" required>
                                <option value="Unit">Unit</option>
                                <option value="Buah">Buah</option>
                                <option value="Set">Set</option>
                                <option value="Pasang">Pasang</option>
                                <option value="Pcs">Pcs</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="kondisi">Kondisi Fisik <span class="req">*</span></label>
                            <select id="kondisi" name="kondisi" class="form-control" required>
                                <option value="baik">Baik</option>
                                <option value="rusak_ringan">Rusak Ringan</option>
                                <option value="rusak_berat">Rusak Berat</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="foto">Foto Barang (Opsional, Maks 2MB)</label>
                        <input type="file" id="foto" name="foto" class="form-control" accept="image/*" placeholder="Pilih file gambar barang">
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Spesifikasi / Keterangan Tambahan</label>
                        <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3" placeholder="Kelengkapan barang, nomor seri, atau keterangan lainnya..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Barang</button>
                </div>
            </form>
        </dialog>
    <?php endif; ?>

    <!-- Modal Pinjam & Modal Edit untuk Setiap Barang -->
    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if($item->stok_tersedia > 0 && $item->kondisi !== 'rusak_berat'): ?>
            <dialog id="borrowItemModal-<?php echo e($item->id); ?>" closedby="any" aria-labelledby="borrowItemTitle-<?php echo e($item->id); ?>" class="modal-dialog">
                <form action="<?php echo e(route('loans.store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="item_id" value="<?php echo e($item->id); ?>">
                    <?php if($isAdmin): ?>
                        <input type="hidden" name="user_id" value="<?php echo e(auth()->id()); ?>">
                    <?php endif; ?>
                    <div class="modal-header">
                        <h3 id="borrowItemTitle-<?php echo e($item->id); ?>">Pinjam: <?php echo e($item->nama_barang); ?></h3>
                        <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
                    </div>
                    <div class="modal-body">
                        <div style="padding:0.75rem 1rem; background:var(--primary-soft); border-radius:8px; margin-bottom:1rem;">
                            <strong><?php echo e($item->nama_barang); ?></strong> (<?php echo e($item->kode_barang); ?>)<br>
                            <small>Stok tersedia: <strong><?php echo e(format_number($item->stok_tersedia)); ?> <?php echo e($item->satuan); ?></strong> • Lokasi: <?php echo e($item->lokasi); ?></small>
                        </div>

                        <div class="form-group">
                            <label for="jumlah_pinjam_<?php echo e($item->id); ?>">Jumlah (<?php echo e($item->satuan); ?>) <span class="req">*</span></label>
                            <input type="number" id="jumlah_pinjam_<?php echo e($item->id); ?>" name="jumlah" class="form-control" value="1" min="1" max="<?php echo e($item->stok_tersedia); ?>" placeholder="Masukkan jumlah unit" required autofocus>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="tgl_pinjam_<?php echo e($item->id); ?>">Tanggal Pinjam <span class="req">*</span></label>
                                <input type="date" id="tgl_pinjam_<?php echo e($item->id); ?>" name="tanggal_pinjam" class="form-control" value="<?php echo e(date('Y-m-d')); ?>" placeholder="Pilih tanggal pinjam" required>
                            </div>
                            <div class="form-group">
                                <label for="rencana_kembali_<?php echo e($item->id); ?>">Rencana Tanggal Kembali <span class="req">*</span></label>
                                <input type="date" id="rencana_kembali_<?php echo e($item->id); ?>" name="rencana_kembali" class="form-control" value="<?php echo e(date('Y-m-d', strtotime('+2 days'))); ?>" placeholder="Pilih tanggal rencana kembali" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="keperluan_<?php echo e($item->id); ?>">Keperluan Peminjaman <span class="req">*</span></label>
                            <textarea id="keperluan_<?php echo e($item->id); ?>" name="keperluan" class="form-control" rows="3" placeholder="Tuliskan keperluan peminjaman beserta kelas/kegiatan..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <?php echo e($isAdmin ? 'Catat Peminjaman' : 'Ajukan Peminjaman'); ?>

                        </button>
                    </div>
                </form>
            </dialog>
        <?php endif; ?>

        <?php if($isAdmin): ?>
            <dialog id="editItemModal-<?php echo e($item->id); ?>" closedby="any" aria-labelledby="editItemTitle-<?php echo e($item->id); ?>" class="modal-dialog">
                <form action="<?php echo e(route('items.update', $item)); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="modal-header">
                        <h3 id="editItemTitle-<?php echo e($item->id); ?>">Edit Barang: <?php echo e($item->nama_barang); ?></h3>
                        <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="kode_barang_<?php echo e($item->id); ?>">Kode Barang <span class="req">*</span></label>
                                <input type="text" id="kode_barang_<?php echo e($item->id); ?>" name="kode_barang" class="form-control" value="<?php echo e($item->kode_barang); ?>" placeholder="Contoh: INV-ELK-001" required autofocus>
                            </div>
                            <div class="form-group">
                                <label for="category_id_<?php echo e($item->id); ?>">Kategori Barang <span class="req">*</span></label>
                                <select id="category_id_<?php echo e($item->id); ?>" name="category_id" class="form-control" required>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($cat->id); ?>" <?php echo e($item->category_id === $cat->id ? 'selected' : ''); ?>>
                                            <?php echo e($cat->nama_kategori); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="nama_barang_<?php echo e($item->id); ?>">Nama Barang <span class="req">*</span></label>
                            <input type="text" id="nama_barang_<?php echo e($item->id); ?>" name="nama_barang" class="form-control" value="<?php echo e($item->nama_barang); ?>" placeholder="Masukkan nama barang" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="merk_<?php echo e($item->id); ?>">Merk / Brand</label>
                                <input type="text" id="merk_<?php echo e($item->id); ?>" name="merk" class="form-control" value="<?php echo e($item->merk); ?>" placeholder="Masukkan merk barang">
                            </div>
                            <div class="form-group">
                                <label for="lokasi_<?php echo e($item->id); ?>">Lokasi Penyimpanan <span class="req">*</span></label>
                                <input type="text" id="lokasi_<?php echo e($item->id); ?>" name="lokasi" class="form-control" value="<?php echo e($item->lokasi); ?>" placeholder="Masukkan lokasi penyimpanan" required>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="stok_total_<?php echo e($item->id); ?>">Stok Total (Dipinjam: <?php echo e(format_number($item->sedang_dipinjam)); ?>) <span class="req">*</span></label>
                                <input type="number" id="stok_total_<?php echo e($item->id); ?>" name="stok_total" class="form-control" value="<?php echo e($item->stok_total); ?>" min="<?php echo e($item->sedang_dipinjam); ?>" placeholder="Masukkan stok total" required>
                            </div>
                            <div class="form-group">
                                <label for="satuan_<?php echo e($item->id); ?>">Satuan <span class="req">*</span></label>
                                <input type="text" id="satuan_<?php echo e($item->id); ?>" name="satuan" class="form-control" value="<?php echo e($item->satuan); ?>" placeholder="Contoh: Unit / Buah" required>
                            </div>
                            <div class="form-group">
                                <label for="kondisi_<?php echo e($item->id); ?>">Kondisi <span class="req">*</span></label>
                                <select id="kondisi_<?php echo e($item->id); ?>" name="kondisi" class="form-control" required>
                                    <option value="baik" <?php echo e($item->kondisi === 'baik' ? 'selected' : ''); ?>>Baik</option>
                                    <option value="rusak_ringan" <?php echo e($item->kondisi === 'rusak_ringan' ? 'selected' : ''); ?>>Rusak Ringan</option>
                                    <option value="rusak_berat" <?php echo e($item->kondisi === 'rusak_berat' ? 'selected' : ''); ?>>Rusak Berat</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="foto_<?php echo e($item->id); ?>">Ganti Foto Barang (Opsional)</label>
                            <input type="file" id="foto_<?php echo e($item->id); ?>" name="foto" class="form-control" accept="image/*" placeholder="Pilih foto baru">
                        </div>

                        <div class="form-group">
                            <label for="deskripsi_<?php echo e($item->id); ?>">Spesifikasi / Keterangan</label>
                            <textarea id="deskripsi_<?php echo e($item->id); ?>" name="deskripsi" class="form-control" rows="3" placeholder="Tuliskan deskripsi atau kelengkapan barang..."><?php echo e($item->deskripsi); ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </dialog>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\UTILITY\htdocs\SistemInventarisSekolahBryant\resources\views/items/index.blade.php ENDPATH**/ ?>