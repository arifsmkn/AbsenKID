<?php $__env->startSection('title', 'Tambah Karyawan'); ?>
<?php $__env->startSection('page-title', 'Tambah Karyawan'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-xl">
    <form method="POST" action="<?php echo e(route('admin.employees.store')); ?>" class="card space-y-4">
        <?php echo csrf_field(); ?>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">NPK <span class="text-red-500">*</span></label>
                <input type="text" name="npk" value="<?php echo e(old('npk')); ?>" class="input w-full" required>
                <?php $__errorArgs = ['npk'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="label">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="<?php echo e(old('nama')); ?>" class="input w-full" required>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">SubCo <span class="text-red-500">*</span></label>
                <select name="subco" class="input w-full" required>
                    <option value="">-- Pilih SubCo --</option>
                    <?php $__currentLoopData = $subcos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($s); ?>" <?php echo e(old('subco')==$s?'selected':''); ?>><?php echo e($s); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php if($subcos->isEmpty()): ?>
                    <p class="text-orange-500 text-xs mt-1">⚠️ Belum ada SubCo. <a href="<?php echo e(route('admin.subcos.index')); ?>" class="underline">Tambah SubCo dulu</a></p>
                <?php endif; ?>
            </div>
            <div>
                <label class="label">Jabatan <span class="text-red-500">*</span></label>
                <input type="text" name="jabatan" value="<?php echo e(old('jabatan')); ?>" class="input w-full" required>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="label">Ukuran Baju</label>
                <select name="ukuran_baju" class="input w-full">
                    <option value="">-</option>
                    <?php $__currentLoopData = ['XS','S','M','L','XL','XXL','XXXL']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $size): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($size); ?>" <?php echo e(old('ukuran_baju')==$size?'selected':''); ?>><?php echo e($size); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-span-2">
                <label class="label">Email</label>
                <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="input w-full">
            </div>
        </div>
        <div>
            <label class="label">No. Telepon</label>
            <input type="text" name="no_telpon" value="<?php echo e(old('no_telpon')); ?>" class="input w-full">
        </div>
        <div class="flex gap-2 pt-2">
            <button class="btn-primary">Simpan</button>
            <a href="<?php echo e(route('admin.employees.index')); ?>" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/AbsenKID/resources/views/admin/employees/create.blade.php ENDPATH**/ ?>