<?php $__env->startSection('title', 'Master SubCo'); ?>
<?php $__env->startSection('page-title', 'Master Data SubCo'); ?>

<?php $__env->startSection('content'); ?>
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    
    <div class="lg:col-span-1">
        <div class="card">
            <h3 class="font-semibold mb-4">Tambah SubCo Baru</h3>
            <form method="POST" action="<?php echo e(route('admin.subcos.store')); ?>" class="space-y-3">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="label">Nama SubCo <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="<?php echo e(old('nama')); ?>"
                           placeholder="contoh: PT Dharma Electrindo"
                           class="input w-full" required>
                    <?php $__errorArgs = ['nama'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div>
                    <label class="label">Singkatan</label>
                    <input type="text" name="singkatan" value="<?php echo e(old('singkatan')); ?>"
                           placeholder="contoh: DEM"
                           class="input w-full" maxlength="20">
                </div>
                <button class="btn-primary w-full justify-center">+ Tambah SubCo</button>
            </form>
        </div>
    </div>

    
    <div class="lg:col-span-2">
        <div class="card overflow-hidden p-0">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-semibold">Daftar SubCo (<?php echo e($subcos->count()); ?>)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama SubCo</th>
                            <th class="px-4 py-3 text-center">Singkatan</th>
                            <th class="px-4 py-3 text-center">Karyawan</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <?php $__empty_1 = true; $__currentLoopData = $subcos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subco): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3 font-medium"><?php echo e($subco->nama); ?></td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge-gray"><?php echo e($subco->singkatan ?? '-'); ?></span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-semibold text-blue-600"><?php echo e($subco->jumlah_karyawan); ?></span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <?php if($subco->is_active): ?>
                                    <span class="badge-green">Aktif</span>
                                <?php else: ?>
                                    <span class="badge-gray">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?php echo e(route('admin.subcos.edit', $subco)); ?>"
                                       class="text-blue-500 hover:underline text-xs">Edit</a>
                                    <form method="POST" action="<?php echo e(route('admin.subcos.destroy', $subco)); ?>"
                                          onsubmit="return confirm('Hapus SubCo <?php echo e($subco->nama); ?>?')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="text-red-500 hover:underline text-xs">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-400">
                                Belum ada SubCo. Tambahkan di form kiri.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/AbsenKID/resources/views/admin/subcos/index.blade.php ENDPATH**/ ?>