<?php $__env->startSection('title', 'Pemenang Doorprize'); ?>
<?php $__env->startSection('page-title', 'History Pemenang Doorprize'); ?>
<?php $__env->startSection('content'); ?>
<div class="flex justify-end mb-4">
    <a href="<?php echo e(route('admin.doorprizes.spin')); ?>" class="btn-primary">🎲 Kocok Doorprize</a>
</div>
<div class="card overflow-hidden p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 text-center">#</th>
                    <th class="px-4 py-3 text-left">Hadiah</th>
                    <th class="px-4 py-3 text-left">NPK</th>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">SubCo</th>
                    <th class="px-4 py-3 text-left">Waktu Menang</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php $__empty_1 = true; $__currentLoopData = $winners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $w): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 text-center">
                        <?php if($i === 0): ?> 🥇 <?php elseif($i === 1): ?> 🥈 <?php elseif($i === 2): ?> 🥉 <?php else: ?> <?php echo e($i+1); ?> <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <?php if($w->doorprize->gambar): ?>
                                <img src="<?php echo e($w->doorprize->gambar_url); ?>" class="h-10 w-10 object-contain rounded">
                            <?php else: ?>
                                <span class="text-2xl">🎁</span>
                            <?php endif; ?>
                            <span class="font-medium"><?php echo e($w->doorprize->nama_hadiah); ?></span>
                        </div>
                    </td>
                    <td class="px-4 py-3 font-mono text-blue-600"><?php echo e($w->employee->npk); ?></td>
                    <td class="px-4 py-3 font-medium"><?php echo e($w->employee->nama); ?></td>
                    <td class="px-4 py-3"><span class="badge-blue"><?php echo e($w->employee->subco); ?></span></td>
                    <td class="px-4 py-3 text-gray-500"><?php echo e($w->won_at->format('H:i:s')); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="text-center py-10 text-gray-400">Belum ada pemenang doorprize</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/AbsenKID/resources/views/admin/doorprizes/winners.blade.php ENDPATH**/ ?>