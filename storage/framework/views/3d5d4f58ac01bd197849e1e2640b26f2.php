<?php $__env->startSection('title', 'Events'); ?>
<?php $__env->startSection('page-title', 'Manajemen Event'); ?>
<?php $__env->startSection('content'); ?>
<div class="flex justify-end mb-4">
    <a href="<?php echo e(route('admin.events.create')); ?>" class="btn-primary">+ Buat Event Baru</a>
</div>
<div class="space-y-4">
    <?php $__empty_1 = true; $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="card flex flex-col sm:flex-row gap-4 items-start">
        <?php if($event->logo): ?>
            <img src="<?php echo e(asset('storage/'.$event->logo)); ?>" class="h-20 w-20 object-contain rounded-xl bg-gray-100 dark:bg-gray-700 p-2 shrink-0">
        <?php else: ?>
            <div class="h-20 w-20 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-3xl shrink-0">📅</div>
        <?php endif; ?>
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <h3 class="font-bold text-lg"><?php echo e($event->nama); ?></h3>
            f    <?php if($event->is_active): ?>
                    <span class="badge-green text-xs">● AKTIF</span>
                <?php endif; ?>
            </div>
            <p class="text-sm text-gray-500"><?php echo e($event->tahun); ?> · <?php echo e($event->lokasi ?? 'Lokasi belum diset'); ?></p>
            <p class="text-sm text-gray-500"><?php echo e($event->tanggal?->format('d M Y')); ?></p>
            <?php if($event->tema): ?>
            <p class="text-sm text-gray-400 italic mt-1">"<?php echo e($event->tema); ?>"</p>
            <?php endif; ?>
        </div>
        <div class="flex gap-2 flex-wrap shrink-0">
            <?php if(!$event->is_active): ?>
            <form method="POST" action="<?php echo e(route('admin.events.activate', $event)); ?>" onsubmit="return confirm('Aktifkan event ini?')">
                <?php echo csrf_field(); ?>
                <button class="btn-secondary text-xs">✅ Aktifkan</button>
            </form>
            <?php endif; ?>
            <a href="<?php echo e(route('admin.events.slides.index', $event)); ?>" class="btn-secondary text-xs">🖼️ Slides</a>
            <a href="<?php echo e(route('admin.events.edit', $event)); ?>" class="btn-secondary text-xs">Edit</a>
            <form method="POST" action="<?php echo e(route('admin.events.destroy', $event)); ?>" onsubmit="return confirm('Hapus event ini?')">
                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                <button class="btn-danger text-xs">Hapus</button>
            </form>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="card text-center py-10 text-gray-400">Belum ada event. <a href="<?php echo e(route('admin.events.create')); ?>" class="text-blue-500">Buat sekarang</a></div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/AbsenKID/resources/views/admin/events/index.blade.php ENDPATH**/ ?>