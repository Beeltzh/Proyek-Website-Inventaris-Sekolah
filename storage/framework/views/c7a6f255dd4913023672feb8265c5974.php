<?php $__env->startSection('title', 'Jejak Audit Sistem (Activity Logs)'); ?>
<?php $__env->startSection('page_title', 'Jejak Audit Aktivitas Sistem (Activity Logs)'); ?>
<?php $__env->startSection('page_subtitle', 'Rekam jejak otentikasi, perubahan data CRUD, serta transaksi peminjaman & pengembalian oleh Admin maupun User'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <a href="<?php echo e(route('dashboard')); ?>">Dashboard</a>
    <span class="sep">/</span>
    <span aria-current="page">Log Aktivitas (Audit)</span>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="card">
        <div class="card-header">
            <form action="<?php echo e(route('activity-logs.index')); ?>" method="GET" class="filter-bar">
                <input type="text" name="search" class="form-control" style="width:240px;" placeholder="Cari deskripsi, nama pelaku, IP..." value="<?php echo e($search); ?>" autofocus>

                <select name="module" class="form-control" style="width:155px;">
                    <option value="">Semua Modul</option>
                    <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($m); ?>" <?php echo e($module === $m ? 'selected' : ''); ?>><?php echo e($m); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <select name="action" class="form-control" style="width:175px;">
                    <option value="">Semua Jenis Aksi</option>
                    <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($a); ?>" <?php echo e($action === $a ? 'selected' : ''); ?>><?php echo e($a); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>

                <input type="date" name="date" class="form-control" style="width:155px;" value="<?php echo e($date); ?>" placeholder="Filter tanggal aktivitas">

                <button type="submit" class="btn btn-outline">Filter</button>
                <?php if($search || $module || $action || $date): ?>
                    <a href="<?php echo e(route('activity-logs.index')); ?>" class="btn btn-outline">Reset</a>
                <?php endif; ?>
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
                    <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <th scope="row">
                                <div style="font-size:0.82rem;"><?php echo e(format_datetime($log->created_at, true)); ?></div>
                                <small style="color:var(--text-muted); font-weight:500;"><?php echo e($log->created_at->diffForHumans()); ?></small>
                            </th>
                            <td>
                                <div style="font-weight:700;"><?php echo e($log->nama_user ?: 'Tamu / Sistem'); ?></div>
                                <span class="badge <?php echo e($log->role_user === 'admin' ? 'badge-primary' : 'badge-neutral'); ?>">
                                    <?php echo e(strtoupper($log->role_user ?: 'GUEST')); ?>

                                </span>
                            </td>
                            <td>
                                <span class="badge badge-neutral"><?php echo e($log->module); ?></span>
                                <?php
                                    $actionBadge = match (true) {
                                        in_array($log->action, ['CREATE', 'REGISTER', 'LOAN_APPROVE', 'RETURN_CONFIRM'], true) => 'badge-success',
                                        in_array($log->action, ['DELETE', 'LOGIN_FAILED', 'LOAN_REJECT'], true) => 'badge-danger',
                                        in_array($log->action, ['UPDATE', 'LOAN_REQUEST', 'RETURN_REQUEST'], true) => 'badge-warning',
                                        default => 'badge-info',
                                    };
                                ?>
                                <span class="badge <?php echo e($actionBadge); ?>"><?php echo e($log->action); ?></span>
                            </td>
                            <td style="max-width:380px;"><?php echo e($log->description); ?></td>
                            <td style="font-family:monospace; font-size:0.8rem; color:var(--text-muted);">
                                <?php echo e($log->ip_address ?: '-'); ?>

                            </td>
                            <td>
                                <?php if(! empty($log->properties)): ?>
                                    <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('auditDetailModal-<?php echo e($log->id); ?>').showModal()">
                                        Detail Payload
                                    </button>
                                <?php else: ?>
                                    <span style="color:var(--text-muted); font-size:0.78rem;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 2.25rem; color:var(--text-muted);">
                                Tidak ada data jejak audit yang sesuai dengan filter.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if($logs->hasPages()): ?>
            <div class="pagination-wrap">
                <?php echo e($logs->links()); ?>

            </div>
        <?php endif; ?>
    </div>

    <!-- Modal Detail Payload Audit -->
    <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php if(! empty($log->properties)): ?>
            <dialog id="auditDetailModal-<?php echo e($log->id); ?>" closedby="any" aria-labelledby="auditDetailTitle-<?php echo e($log->id); ?>" class="modal-dialog">
                <div class="modal-header">
                    <h3 id="auditDetailTitle-<?php echo e($log->id); ?>">Detail Jejak Audit #<?php echo e(format_number($log->id)); ?></h3>
                    <button type="button" class="btn btn-outline btn-sm" onclick="this.closest('dialog').close()">✕</button>
                </div>
                <div class="modal-body">
                    <div style="font-size:0.84rem; margin-bottom:0.85rem; display:grid; gap:0.3rem;">
                        <div><strong>Waktu:</strong> <?php echo e(format_datetime($log->created_at, true)); ?></div>
                        <div><strong>Pelaku:</strong> <?php echo e($log->nama_user); ?> (<?php echo e(strtoupper($log->role_user ?? '-')); ?>)</div>
                        <div><strong>Modul / Aksi:</strong> <?php echo e($log->module); ?> / <?php echo e($log->action); ?></div>
                        <div><strong>IP & Agen:</strong> <?php echo e($log->ip_address ?: '-'); ?> — <span style="color:var(--text-muted);"><?php echo e($log->user_agent ?: '-'); ?></span></div>
                    </div>
                    <label style="font-size:0.78rem; font-weight:700; color:var(--text-muted); display:block; margin-bottom:0.35rem;">
                        Perubahan Data / Properties (JSON):
                    </label>
                    <pre style="background:#0f172a; color:#e2e8f0; padding:1rem; border-radius:10px; font-size:0.8rem; overflow-x:auto; line-height:1.45;"><?php echo e(json_encode($log->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="this.closest('dialog').close()">Tutup</button>
                </div>
            </dialog>
        <?php endif; ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\UTILITY\htdocs\SistemInventarisSekolahBryant\resources\views/activity_logs/index.blade.php ENDPATH**/ ?>