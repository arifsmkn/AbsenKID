<?php $__env->startSection('title', 'Master Data Karyawan'); ?>
<?php $__env->startSection('page-title', 'Master Data Karyawan'); ?>

<?php $__env->startSection('content'); ?>
<div class="flex flex-col sm:flex-row gap-3 mb-4">
    <form method="GET" class="flex gap-2 flex-1">
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari NPK, Nama, SubCo..." class="input flex-1">
        <select name="subco" class="input w-40">
            <option value="">Semua SubCo</option>
            <?php $__currentLoopData = $subcos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s); ?>" <?php echo e(request('subco')==$s?'selected':''); ?>><?php echo e($s); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button class="btn-primary">Cari</button>
    </form>
    <div class="flex gap-2">
        <a href="<?php echo e(route('admin.employees.create')); ?>" class="btn-primary">+ Tambah</a>
        <button onclick="document.getElementById('importModal').classList.remove('hidden')" class="btn-secondary">📥 Import</button>
        <a href="<?php echo e(route('admin.employees.export')); ?>" class="btn-secondary">📤 Export</a>
    </div>
</div>

<div class="card overflow-hidden p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 text-left">NPK</th>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">SubCo</th>
                    <th class="px-4 py-3 text-left">Jabatan</th>
                    <th class="px-4 py-3 text-left">Baju</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Telp</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-4 py-3 font-mono font-medium text-blue-600"><?php echo e($emp->npk); ?></td>
                    <td class="px-4 py-3 font-medium"><?php echo e($emp->nama); ?></td>
                    <td class="px-4 py-3"><span class="badge-blue"><?php echo e($emp->subco); ?></span></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400"><?php echo e($emp->jabatan); ?></td>
                    <td class="px-4 py-3 text-center"><span class="badge-gray"><?php echo e($emp->ukuran_baju ?? '-'); ?></span></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs"><?php echo e($emp->email ?? '-'); ?></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs"><?php echo e($emp->no_telpon ?? '-'); ?></td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="<?php echo e(route('admin.employees.edit', $emp)); ?>" class="text-blue-500 hover:underline text-xs">Edit</a>
                            <form method="POST" action="<?php echo e(route('admin.employees.destroy', $emp)); ?>" onsubmit="return confirm('Hapus karyawan ini?')">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button class="text-red-500 hover:underline text-xs">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="8" class="text-center py-10 text-gray-400">Belum ada data karyawan</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="p-4"><?php echo e($employees->links()); ?></div>
</div>


<div id="importModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md">
        <h3 class="font-semibold mb-4">Import Data Karyawan</h3>
        <p class="text-sm text-gray-500 mb-3">Format kolom: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">npk, nama, subco, jabatan, ukuran_baju, email, no_telpon</code></p>
        <a href="<?php echo e(route('admin.employees.template')); ?>" class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-800 mb-4">
            ⬇️ Download Template Excel
        </a>
        <form method="POST" action="<?php echo e(route('admin.employees.import')); ?>" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <input type="file" name="file" accept=".xlsx,.xls,.csv" class="input w-full mb-4" required>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="btn-secondary">Batal</button>
                <button class="btn-primary">Import</button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/AbsenKID/resources/views/admin/employees/index.blade.php ENDPATH**/ ?>