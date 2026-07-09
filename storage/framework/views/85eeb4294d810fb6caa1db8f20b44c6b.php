<?php $__env->startSection('title', 'Tambah Hadiah'); ?>
<?php $__env->startSection('page-title', 'Tambah Hadiah Doorprize'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-lg">
    <form method="POST" action="<?php echo e(route('admin.doorprizes.store')); ?>" enctype="multipart/form-data" class="card space-y-4">
        <?php echo csrf_field(); ?>

        
        <div>
            <label class="label">Tipe Hadiah <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-3 gap-3 mt-1">
                <label class="relative flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all
                              <?php echo e(old('type', 'doorprize') === 'doorprize' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600'); ?>">
                    <input type="radio" name="type" value="doorprize" class="sr-only" <?php echo e(old('type', 'doorprize') === 'doorprize' ? 'checked' : ''); ?>>
                    <span class="text-2xl">🎁</span>
                    <div>
                        <p class="font-semibold text-sm">Doorprize</p>
                        <p class="text-xs text-gray-500">Hadiah kecil, bisa banyak</p>
                    </div>
                </label>
                <label class="relative flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all
                              <?php echo e(old('type') === 'doorprize_utama' ? 'border-slate-400 bg-slate-100 dark:bg-slate-700/30' : 'border-gray-200 dark:border-gray-600'); ?>">
                    <input type="radio" name="type" value="doorprize_utama" class="sr-only" <?php echo e(old('type') === 'doorprize_utama' ? 'checked' : ''); ?>>
                    <span class="text-2xl">🥈</span>
                    <div>
                        <p class="font-semibold text-sm">Doorprize Utama</p>
                        <p class="text-xs text-gray-500">Hadiah menengah, lebih istimewa</p>
                    </div>
                </label>
                <label class="relative flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all
                              <?php echo e(old('type') === 'grand_prize' ? 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20' : 'border-gray-200 dark:border-gray-600'); ?>">
                    <input type="radio" name="type" value="grand_prize" class="sr-only" <?php echo e(old('type') === 'grand_prize' ? 'checked' : ''); ?>>
                    <span class="text-2xl">🏆</span>
                    <div>
                        <p class="font-semibold text-sm">Grand Prize</p>
                        <p class="text-xs text-gray-500">Hadiah utama, tampilkan foto</p>
                    </div>
                </label>
            </div>
        </div>

        <div>
            <label class="label">Nama Hadiah <span class="text-red-500">*</span></label>
            <input type="text" name="nama_hadiah" value="<?php echo e(old('nama_hadiah')); ?>" class="input w-full" required>
        </div>

        <div>
            <label class="label">Gambar Hadiah <span class="text-xs text-gray-400">(wajib untuk Grand Prize)</span></label>
            <input type="file" name="gambar" accept="image/*" class="input w-full">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Jumlah / Qty</label>
                <input type="number" name="jumlah" value="<?php echo e(old('jumlah', 1)); ?>" min="1" class="input w-full">
                <p class="text-xs text-gray-400 mt-1">Jumlah pemenang yang akan di-spin</p>
            </div>
            <div>
                <label class="label">Urutan</label>
                <input type="number" name="urutan" value="<?php echo e(old('urutan', 0)); ?>" class="input w-full">
            </div>
        </div>

        <div class="flex gap-2 pt-2">
            <button class="btn-primary">Simpan</button>
            <a href="<?php echo e(route('admin.doorprizes.index')); ?>" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('input[name="type"]').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('label[class*="cursor-pointer"]').forEach(label => {
            const input = label.querySelector('input[type="radio"]');
            const colors = {
                doorprize: ['border-blue-500', 'bg-blue-50'],
                doorprize_utama: ['border-slate-400', 'bg-slate-100'],
                grand_prize: ['border-yellow-500', 'bg-yellow-50'],
            };
            if (input.checked) {
                label.classList.add(...colors[input.value]);
                label.classList.remove('border-gray-200');
            } else {
                Object.values(colors).flat().forEach(c => label.classList.remove(c));
                label.classList.remove('dark:bg-yellow-900/20', 'dark:bg-blue-900/20', 'dark:bg-slate-700/30');
                label.classList.add('border-gray-200');
            }
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/AbsenKID/resources/views/admin/doorprizes/create.blade.php ENDPATH**/ ?>