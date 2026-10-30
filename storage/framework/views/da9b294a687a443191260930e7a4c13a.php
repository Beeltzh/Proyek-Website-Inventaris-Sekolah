<?php $__env->startSection('title', 'Kategori Barang'); ?>
<?php $__env->startSection('page_title', 'Manajemen Kategori Barang'); ?>
<?php $__env->startSection('page_subtitle', 'Kelola pengelompokan jenis barang inventaris sekolah'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <a href="<?php echo e(route('dashboard')); ?>">Dashboard</a>
    <span class="sep">/</span>
    <span aria-current="page">Kategori Barang</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header">
            <form action="<?php echo e(route('categories.index')); ?>" method="GET" class="filter-bar">
                <input type="text" name="search" class="form-control" style="width:280px;" placeholder="Cari kode atau nama kategori..." value="<?php echo e($search); ?>" autofocus>
                <button type="submit" class="btn btn-outline">Cari</button>
                <?php if($search): ?>
                    <a href="<?php echo e(route('categories.index')); ?>" class="btn btn-outline">Reset</a>
                <?php endif; ?>
            </form>

            <button type="button" class="btn btn-primary" onclick="document.getElementById('addCategoryModal').showModal()">
                + Tambah Kategori
            </button>
        </div>

        <div tabindex="0" role="region" aria-labelledby="categories-table-caption" class="table-wrapper">
            <table class="data-table" style="--label-2: 'Nama Kategori'; --label-3: 'Deskripsi'; --label-4: 'Jumlah Barang'; --label-5: 'Total Stok'; --label-6: 'Aksi';">
                <caption id="categories-table-caption">Daftar kategori barang inventaris sekolah</caption>
                <thead>
                    <tr>
                        <th scope="col">Kode Kategori</th>
                        <th scope="col">Nama Kategori</th>
                        <th scope="col">Deskripsi</th>
                        <th scope="col">Jenis Barang</th>
                        <th scope="col">Stok Tersedia / Total</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <th scope="row">
                                <span class="badge badge-primary"><?php echo e($category->kode_kategori); ?></span>
                            </th>
                            <td style="font-weight:700;"><?php echo e($category->nama_kategori); ?></td>
                            <td style="color:var(--text-muted); max-width:340px;"><?php echo e($category->deskripsi ?: '-'); ?></td>
                            <td>
                                <span class="badge badge-neutral"><?php echo e(format_number($category->items_count)); ?> Jenis</span>
                            </td>
                            <td>
                                <strong><?php echo e(format_number((int) $category->items_sum_stok_tersedia)); ?></strong> / <?php echo e(format_number((int) $category->items_sum_stok_total)); ?> Unit
                            </td>
                            <td>
                                <div style="display:flex; gap:0.4rem;">
                                    <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('editCategoryModal-<?php echo e($category->id); ?>').showModal()">
                                        Edit
                                    </button>
                                    <form action="<?php echo e(route('categories.destroy', $category)); ?>" method="POST" onsubmit="return confirm('Hapus kategori <?php echo e($category->nama_kategori); ?>?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 2rem; color:var(--text-muted);">
                                Belum ada data kategori yang ditemukan.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($categories->hasPages()): ?>
            <div class="pagination-wrap">
                <?php echo e($categories->links()); ?>

            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Tambah Kategori -->
    <dialog id="addCategoryModal" closedby="any" aria-labelledby="addCategoryTitle" class="modal-dialog">
        <form action="<?php echo e(route('categories.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-header">
                <h3 id="addCategoryTitle">Tambah Kategori Barang Baru</h3>
                <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="kode_kategori">Kode Kategori <span class="req">*</span></label>
                    <input type="text" id="kode_kategori" name="kode_kategori" class="form-control" placeholder="Contoh: KAT-ELK" required autofocus>
                </div>
                <div class="form-group">
                    <label for="nama_kategori">Nama Kategori <span class="req">*</span></label>
                    <input type="text" id="nama_kategori" name="nama_kategori" class="form-control" placeholder="Contoh: Elektronik & Multimedia" required>
                </div>
                <div class="form-group">
                    <label for="deskripsi">Deskripsi Kategori</label>
                    <textarea id="deskripsi" name="deskripsi" class="form-control" rows="3" placeholder="Penjelasan singkat jenis barang dalam kategori ini..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Kategori</button>
            </div>
        </form>
    </dialog>

    <!-- Modal Edit Kategori -->
    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <dialog id="editCategoryModal-<?php echo e($category->id); ?>" closedby="any" aria-labelledby="editCategoryTitle-<?php echo e($category->id); ?>" class="modal-dialog">
            <form action="<?php echo e(route('categories.update', $category)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header">
                    <h3 id="editCategoryTitle-<?php echo e($category->id); ?>">Edit Kategori: <?php echo e($category->nama_kategori); ?></h3>
                    <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="kode_kategori_<?php echo e($category->id); ?>">Kode Kategori <span class="req">*</span></label>
                        <input type="text" id="kode_kategori_<?php echo e($category->id); ?>" name="kode_kategori" class="form-control" value="<?php echo e($category->kode_kategori); ?>" placeholder="Contoh: KAT-ELK" required autofocus>
                    </div>
                    <div class="form-group">
                        <label for="nama_kategori_<?php echo e($category->id); ?>">Nama Kategori <span class="req">*</span></label>
                        <input type="text" id="nama_kategori_<?php echo e($category->id); ?>" name="nama_kategori" class="form-control" value="<?php echo e($category->nama_kategori); ?>" placeholder="Masukkan nama kategori" required>
                    </div>
                    <div class="form-group">
                        <label for="deskripsi_<?php echo e($category->id); ?>">Deskripsi Kategori</label>
                        <textarea id="deskripsi_<?php echo e($category->id); ?>" name="deskripsi" class="form-control" rows="3" placeholder="Masukkan deskripsi singkat kategori..."><?php echo e($category->deskripsi); ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" onclick="this.closest('dialog').close()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </dialog>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\UTILITY\htdocs\SistemInventarisSekolahBryant\resources\views/categories/index.blade.php ENDPATH**/ ?>