<?php $__env->startSection('title', 'Pengaturan'); ?>
<?php $__env->startSection('page-title', 'Pengaturan Sistem'); ?>
<?php $__env->startSection('content'); ?>
<div class="max-w-2xl space-y-5">
    <form method="POST" action="<?php echo e(route('admin.settings.update')); ?>" class="space-y-5">
        <?php echo csrf_field(); ?>

        
        <div class="card space-y-4">
            <h3 class="font-semibold text-brand-navy dark:text-white">⚙️ Umum</h3>
            <div>
                <label class="label">Nama Aplikasi</label>
                <input type="text" name="app_name" value="<?php echo e(old('app_name', $settings['app_name'] ?? 'AbsenKID')); ?>" class="input w-full">
            </div>
            <div>
                <label class="label">Mode Default Tampilan</label>
                <select name="app_mode" class="input w-full">
                    <option value="dark"  <?php echo e(($settings['app_mode']??'dark')==='dark'?'selected':''); ?>>🌙 Dark Mode</option>
                    <option value="light" <?php echo e(($settings['app_mode']??'dark')==='light'?'selected':''); ?>>☀️ Light Mode</option>
                </select>
            </div>
        </div>

        
        <div class="card space-y-4">
            <h3 class="font-semibold text-brand-navy dark:text-white">📞 Kontak Panitia</h3>
            <p class="text-xs text-brand-slate">Tampil di portal peserta.</p>
            <div>
                <label class="label">Nama Panitia</label>
                <input type="text" name="panitia_nama" value="<?php echo e(old('panitia_nama', $settings['panitia_nama'] ?? '')); ?>" class="input w-full">
            </div>
            <div>
                <label class="label">No. WhatsApp Panitia</label>
                <input type="text" name="panitia_whatsapp" value="<?php echo e(old('panitia_whatsapp', $settings['panitia_whatsapp'] ?? '')); ?>" class="input w-full" placeholder="628123456789">
                <p class="text-xs text-brand-slate mt-1">Format internasional tanpa + (contoh: 628123456789)</p>
            </div>
            <div>
                <label class="label">Email Panitia</label>
                <input type="email" name="panitia_email" value="<?php echo e(old('panitia_email', $settings['panitia_email'] ?? '')); ?>" class="input w-full">
            </div>
        </div>

        
        <div class="card space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-brand-navy dark:text-white">💬 WhatsApp (Fonnte API)</h3>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="wa_enabled" value="0">
                    <input type="checkbox" name="wa_enabled" value="1"
                           <?php echo e(($settings['wa_enabled'] ?? '0') === '1' ? 'checked' : ''); ?>

                           class="w-4 h-4 rounded text-brand-navy">
                    <span class="text-sm font-medium">Aktif</span>
                </label>
            </div>
            <div>
                <label class="label">API Token Fonnte</label>
                <input type="text" name="wa_api_token"
                       value="<?php echo e(old('wa_api_token', $settings['wa_api_token'] ?? '')); ?>"
                       class="input w-full font-mono text-sm"
                       placeholder="TOKEN_DARI_FONNTE">
                <p class="text-xs text-brand-slate mt-1">
                    Dapatkan token di <strong>fonnte.com</strong> → Device → Token
                </p>
            </div>
            <div>
                <label class="label">URL API</label>
                <input type="text" name="wa_api_url"
                       value="<?php echo e(old('wa_api_url', $settings['wa_api_url'] ?? 'https://api.fonnte.com/send')); ?>"
                       class="input w-full font-mono text-sm">
            </div>
            <div>
                <label class="label">Template Pesan WA</label>
                <textarea name="wa_template" rows="6"
                          class="input w-full text-sm"
                          placeholder="Gunakan variabel: {nama} {event} {tanggal} {waktu} {lokasi} {url} {npk}"><?php echo e(old('wa_template', $settings['wa_template'] ?? '')); ?></textarea>
                <p class="text-xs text-brand-slate mt-1">Kosongkan untuk gunakan template default. Variabel: <code>{nama} {event} {tanggal} {waktu} {lokasi} {url} {npk}</code></p>
            </div>
        </div>

        
        <div class="card space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold text-brand-navy dark:text-white">📧 Email</h3>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="email_enabled" value="0">
                    <input type="checkbox" name="email_enabled" value="1"
                           <?php echo e(($settings['email_enabled'] ?? '0') === '1' ? 'checked' : ''); ?>

                           class="w-4 h-4 rounded text-brand-navy">
                    <span class="text-sm font-medium">Aktif</span>
                </label>
            </div>
            <p class="text-xs text-brand-slate">Konfigurasi SMTP diatur di file <code>.env</code> (MAIL_HOST, MAIL_USERNAME, dll).</p>
            <div class="grid grid-cols-2 gap-3 p-3 rounded-xl bg-brand-cream dark:bg-gray-700/40 text-xs font-mono text-brand-steel">
                <span>MAIL_HOST = <?php echo e(env('MAIL_HOST', '?')); ?></span>
                <span>MAIL_PORT = <?php echo e(env('MAIL_PORT', '?')); ?></span>
                <span>MAIL_FROM = <?php echo e(env('MAIL_FROM_ADDRESS', '?')); ?></span>
                <span>MAIL_ENCRYPT = <?php echo e(env('MAIL_ENCRYPTION', '?')); ?></span>
            </div>
        </div>

        <button class="btn-primary px-8 py-2.5">💾 Simpan Semua Pengaturan</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/AbsenKID/resources/views/admin/settings/index.blade.php ENDPATH**/ ?>