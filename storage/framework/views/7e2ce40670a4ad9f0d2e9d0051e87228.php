<?php $__env->startSection('title', 'Edit Event'); ?>
<?php $__env->startSection('page-title', 'Edit Event — '.$event->nama); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-2xl">
    <form method="POST" action="<?php echo e(route('admin.events.update', $event)); ?>" enctype="multipart/form-data" class="card space-y-4">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <?php if($event->logo): ?>
        <div class="flex items-center gap-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
            <img src="<?php echo e(asset('storage/'.$event->logo)); ?>" class="h-16 w-auto object-contain rounded">
            <p class="text-sm text-gray-500">Logo saat ini</p>
        </div>
        <?php endif; ?>
        <?php if($event->wallpaper): ?>
        <div class="flex items-center gap-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
            <img src="<?php echo e($event->wallpaper_url); ?>" class="h-16 w-auto object-cover rounded">
            <p class="text-sm text-gray-500">Wallpaper saat ini</p>
        </div>
        <?php endif; ?>
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="label">Nama Event</label>
                <input type="text" name="nama" value="<?php echo e(old('nama', $event->nama)); ?>" class="input w-full" required>
            </div>
            <div>
                <label class="label">Tahun</label>
                <input type="number" name="tahun" value="<?php echo e(old('tahun', $event->tahun)); ?>" class="input w-full" required>
            </div>
            <div>
                <label class="label">Tanggal</label>
                <input type="date" name="tanggal" value="<?php echo e(old('tanggal', $event->tanggal?->format('Y-m-d'))); ?>" class="input w-full">
            </div>
            <div>
                <label class="label">Waktu Mulai</label>
                <input type="time" name="waktu_mulai" value="<?php echo e(old('waktu_mulai', substr($event->waktu_mulai??'',0,5))); ?>" class="input w-full">
            </div>
            <div>
                <label class="label">Waktu Selesai</label>
                <input type="time" name="waktu_selesai" value="<?php echo e(old('waktu_selesai', substr($event->waktu_selesai??'',0,5))); ?>" class="input w-full">
            </div>
        </div>
        <div>
            <label class="label">Tema</label>
            <input type="text" name="tema" value="<?php echo e(old('tema', $event->tema)); ?>" class="input w-full">
        </div>
        <div>
            <label class="label">Deskripsi</label>
            <textarea name="deskripsi" rows="3" class="input w-full"><?php echo e(old('deskripsi', $event->deskripsi)); ?></textarea>
        </div>
        <div>
            <label class="label">Lokasi</label>
            <input type="text" name="lokasi" value="<?php echo e(old('lokasi', $event->lokasi)); ?>" class="input w-full">
        </div>
        <div>
            <label class="label">Maps Embed (tampilan peta interaktif di halaman depan)</label>
            <textarea name="maps_embed" rows="2" class="input w-full text-xs" placeholder='&lt;iframe src="..."&gt;&lt;/iframe&gt;'><?php echo e(old('maps_embed', $event->maps_embed)); ?></textarea>
        </div>
        <div>
            <label class="label">Link Google Maps (untuk tombol "Buka di Google Maps")</label>
            <input type="url" name="maps_url" value="<?php echo e(old('maps_url', $event->maps_url)); ?>" class="input w-full text-sm" placeholder="https://maps.app.goo.gl/xxxxx">
            <p class="text-xs text-gray-400 mt-1">Cara dapat link: buka Google Maps → cari lokasi venue → tombol <strong>Share/Bagikan</strong> → <strong>Copy link</strong> → tempel di sini.</p>
        </div>
        <div>
            <label class="label">Ganti Logo</label>
            <input type="file" name="logo" accept="image/*" class="input w-full">
        </div>
        <div>
            <label class="label">Ganti Wallpaper Halaman Depan</label>
            <input type="file" name="wallpaper" accept="image/*" class="input w-full">
            <p class="text-xs text-gray-500 mt-1">Background untuk halaman login &amp; dashboard peserta. Maks 5MB.</p>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="label">Warna Primer</label>
                <input type="color" name="primary_color" value="<?php echo e(old('primary_color', $event->theme_config['primary_color'] ?? '#1e40af')); ?>" class="input w-full h-10 p-1">
            </div>
            <div>
                <label class="label">Warna Sekunder</label>
                <input type="color" name="secondary_color" value="<?php echo e(old('secondary_color', $event->theme_config['secondary_color'] ?? '#7c3aed')); ?>" class="input w-full h-10 p-1">
            </div>
            <div>
                <label class="label">Mode Tampilan</label>
                <select name="mode" class="input w-full">
                    <option value="dark" <?php echo e(($event->theme_config['mode']??'dark')==='dark'?'selected':''); ?>>Dark</option>
                    <option value="light" <?php echo e(($event->theme_config['mode']??'dark')==='light'?'selected':''); ?>>Light</option>
                </select>
            </div>
        </div>
        <div class="flex gap-2 pt-2">
            <button class="btn-primary">Update Event</button>
            <a href="<?php echo e(route('admin.events.index')); ?>" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/AbsenKID/resources/views/admin/events/edit.blade.php ENDPATH**/ ?>