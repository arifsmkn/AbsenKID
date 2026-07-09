<?php $__env->startSection('title', 'Kelola Hadiah'); ?>
<?php $__env->startSection('page-title', 'Kelola Hadiah Doorprize'); ?>
<?php $__env->startSection('content'); ?>
<div class="flex justify-between mb-4">
    <div class="flex gap-2">
        <a href="<?php echo e(route('admin.doorprizes.spin')); ?>" class="btn-primary">🎲 Mulai Spin</a>
        <a href="<?php echo e(route('admin.doorprizes.winners')); ?>" class="btn-secondary">🏆 History Pemenang</a>
    </div>
    <a href="<?php echo e(route('admin.doorprizes.create')); ?>" class="btn-secondary">+ Tambah Hadiah</a>
</div>


<?php
    $regular = $doorprizes->where('type', 'doorprize');
    $utama   = $doorprizes->where('type', 'doorprize_utama');
    $grands  = $doorprizes->where('type', 'grand_prize');
?>

<?php if($grands->count()): ?>
<h3 class="font-semibold text-sm text-yellow-600 dark:text-yellow-400 uppercase tracking-widest mb-3 mt-2">🏆 Grand Prize</h3>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
    <?php $__currentLoopData = $grands; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card p-0 overflow-hidden ring-2 ring-yellow-400/60">
        <?php if($dp->gambar): ?>
            <img src="<?php echo e($dp->gambar_url); ?>" class="w-full h-40 object-cover">
        <?php else: ?>
            <div class="w-full h-40 bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center text-5xl">🏆</div>
        <?php endif; ?>
        <div class="p-4">
            <span class="text-xs font-bold text-yellow-600 dark:text-yellow-400 uppercase">Grand Prize</span>
            <p class="font-semibold mt-0.5"><?php echo e($dp->nama_hadiah); ?></p>
            <p class="text-sm text-gray-500">Qty: <?php echo e($dp->jumlah); ?></p>
            <div class="flex gap-2 mt-3">
                <a href="<?php echo e(route('admin.doorprizes.edit', $dp)); ?>" class="btn-secondary text-xs py-1 px-2">Edit</a>
                <form method="POST" action="<?php echo e(route('admin.doorprizes.destroy', $dp)); ?>" onsubmit="return confirm('Hapus hadiah ini?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="btn-danger text-xs py-1 px-2">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>

<?php if($utama->count()): ?>
<h3 class="font-semibold text-sm text-slate-500 dark:text-slate-300 uppercase tracking-widest mb-3 mt-2">🥈 Doorprize Utama</h3>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
    <?php $__currentLoopData = $utama; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="card p-0 overflow-hidden ring-2 ring-slate-400/50">
        <?php if($dp->gambar): ?>
            <img src="<?php echo e($dp->gambar_url); ?>" class="w-full h-40 object-cover">
        <?php else: ?>
            <div class="w-full h-40 bg-slate-100 dark:bg-slate-700/30 flex items-center justify-center text-5xl">🥈</div>
        <?php endif; ?>
        <div class="p-4">
            <span class="text-xs font-bold text-slate-500 dark:text-slate-300 uppercase">Doorprize Utama</span>
            <p class="font-semibold mt-0.5"><?php echo e($dp->nama_hadiah); ?></p>
            <p class="text-sm text-gray-500">Qty: <?php echo e($dp->jumlah); ?></p>
            <div class="flex gap-2 mt-3">
                <a href="<?php echo e(route('admin.doorprizes.edit', $dp)); ?>" class="btn-secondary text-xs py-1 px-2">Edit</a>
                <form method="POST" action="<?php echo e(route('admin.doorprizes.destroy', $dp)); ?>" onsubmit="return confirm('Hapus hadiah ini?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="btn-danger text-xs py-1 px-2">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php endif; ?>

<h3 class="font-semibold text-sm text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-3">🎁 Doorprize</h3>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
    <?php $__empty_1 = true; $__currentLoopData = $regular; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="card p-0 overflow-hidden">
        <div class="w-full h-24 bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-4xl">🎁</div>
        <div class="p-4">
            <p class="font-semibold"><?php echo e($dp->nama_hadiah); ?></p>
            <p class="text-sm text-gray-500">Qty: <?php echo e($dp->jumlah); ?></p>
            <div class="flex gap-2 mt-3">
                <a href="<?php echo e(route('admin.doorprizes.edit', $dp)); ?>" class="btn-secondary text-xs py-1 px-2">Edit</a>
                <form method="POST" action="<?php echo e(route('admin.doorprizes.destroy', $dp)); ?>" onsubmit="return confirm('Hapus hadiah ini?')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="btn-danger text-xs py-1 px-2">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <p class="col-span-4 text-center text-gray-400 py-10">Belum ada hadiah. <a href="<?php echo e(route('admin.doorprizes.create')); ?>" class="text-blue-500">Tambah hadiah</a></p>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/AbsenKID/resources/views/admin/doorprizes/index.blade.php ENDPATH**/ ?>