<?php if($paginator->hasPages()): ?>
    <nav role="navigation" aria-label="Navigasi Halaman" class="custom-pagination">
        <div class="pagination-info">
            Menampilkan <strong><?php echo e(format_number($paginator->firstItem())); ?></strong>
            sampai <strong><?php echo e(format_number($paginator->lastItem())); ?></strong>
            dari total <strong><?php echo e(format_number($paginator->total())); ?></strong> data
        </div>

        <ul class="pagination-list">
            
            <?php if($paginator->onFirstPage()): ?>
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">‹ Sebelumnya</span>
                </li>
            <?php else: ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev">‹ Sebelumnya</a>
                </li>
            <?php endif; ?>

            
            <?php $__currentLoopData = $elements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(is_string($element)): ?>
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link"><?php echo e($element); ?></span></li>
                <?php endif; ?>

                <?php if(is_array($element)): ?>
                    <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($page == $paginator->currentPage()): ?>
                            <li class="page-item active" aria-current="page">
                                <span class="page-link"><?php echo e(format_number($page)); ?></span>
                            </li>
                        <?php else: ?>
                            <li class="page-item">
                                <a class="page-link" href="<?php echo e($url); ?>"><?php echo e(format_number($page)); ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            
            <?php if($paginator->hasMorePages()): ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next">Berikutnya ›</a>
                </li>
            <?php else: ?>
                <li class="page-item disabled" aria-disabled="true">
                    <span class="page-link">Berikutnya ›</span>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php /**PATH D:\UTILITY\htdocs\SistemInventarisSekolahBryant\resources\views/vendor/pagination/custom.blade.php ENDPATH**/ ?>