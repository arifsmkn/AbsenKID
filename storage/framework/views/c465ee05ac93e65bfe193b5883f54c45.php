<?php $__env->startSection('title', 'History Pengiriman'); ?>
<?php $__env->startSection('page-title', 'History Pengiriman Undangan'); ?>
<?php $__env->startSection('content'); ?>


<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
    <div class="card p-4 text-center">
        <p class="text-3xl font-black text-green-500"><?php echo e($stats['wa_sent']); ?></p>
        <p class="text-xs text-brand-slate mt-1">💬 WA Berhasil</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-3xl font-black text-brand-red"><?php echo e($stats['wa_failed']); ?></p>
        <p class="text-xs text-brand-slate mt-1">💬 WA Gagal</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-3xl font-black text-green-500"><?php echo e($stats['email_sent']); ?></p>
        <p class="text-xs text-brand-slate mt-1">📧 Email Berhasil</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-3xl font-black text-brand-red"><?php echo e($stats['email_failed']); ?></p>
        <p class="text-xs text-brand-slate mt-1">📧 Email Gagal</p>
    </div>
</div>


<form method="GET" class="flex gap-2 mb-4 flex-wrap">
    <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Cari nama, NPK, no WA, email..." class="input flex-1 min-w-40">
    <select name="channel" class="input">
        <option value="all"       <?php echo e(request('channel','all')==='all'?'selected':''); ?>>Semua Channel</option>
        <option value="whatsapp"  <?php echo e(request('channel')==='whatsapp'?'selected':''); ?>>💬 WhatsApp</option>
        <option value="email"     <?php echo e(request('channel')==='email'?'selected':''); ?>>📧 Email</option>
    </select>
    <select name="status" class="input">
        <option value="all"    <?php echo e(request('status','all')==='all'?'selected':''); ?>>Semua Status</option>
        <option value="sent"   <?php echo e(request('status')==='sent'?'selected':''); ?>>✅ Berhasil</option>
        <option value="failed" <?php echo e(request('status')==='failed'?'selected':''); ?>>❌ Gagal</option>
        <option value="pending"<?php echo e(request('status')==='pending'?'selected':''); ?>>⏳ Pending</option>
    </select>
    <button class="btn-primary">Filter</button>
    <a href="<?php echo e(route('admin.invitations.sendHistory')); ?>" class="btn-secondary">Reset</a>
    <a href="<?php echo e(route('admin.invitations.index')); ?>" class="btn-secondary ml-auto">← Kembali</a>
</form>

<div class="card overflow-hidden p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-xs uppercase tracking-wide" style="background:rgba(36,76,107,0.06)">
                <tr>
                    <th class="px-4 py-3 text-left text-brand-slate">Peserta</th>
                    <th class="px-4 py-3 text-left text-brand-slate">Channel</th>
                    <th class="px-4 py-3 text-left text-brand-slate">Tujuan</th>
                    <th class="px-4 py-3 text-center text-brand-slate">Status</th>
                    <th class="px-4 py-3 text-left text-brand-slate">Waktu</th>
                    <th class="px-4 py-3 text-left text-brand-slate">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-slate/10 dark:divide-gray-700">
                <?php $__empty_1 = true; $__currentLoopData = $sends; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-brand-cream/50 dark:hover:bg-gray-700/30">
                    <td class="px-4 py-3">
                        <p class="font-semibold text-sm"><?php echo e($s->invitation?->employee?->nama ?? '—'); ?></p>
                        <p class="font-mono text-xs text-brand-slate"><?php echo e($s->employee_npk); ?></p>
                    </td>
                    <td class="px-4 py-3">
                        <?php if($s->channel === 'whatsapp'): ?>
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-full bg-green-100 text-green-700">💬 WhatsApp</span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-full bg-blue-100 text-blue-700">📧 Email</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 font-mono text-xs text-brand-steel"><?php echo e($s->target); ?></td>
                    <td class="px-4 py-3 text-center">
                        <?php if($s->status === 'sent'): ?>
                            <span class="badge-green">✅ Berhasil</span>
                        <?php elseif($s->status === 'failed'): ?>
                            <span class="badge-red">❌ Gagal</span>
                        <?php else: ?>
                            <span class="badge-gray">⏳ Pending</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 text-xs text-brand-slate whitespace-nowrap">
                        <?php echo e($s->sent_at?->format('d/m H:i') ?? $s->updated_at->format('d/m H:i')); ?>

                    </td>
                    <td class="px-4 py-3 text-xs text-brand-red max-w-xs truncate" title="<?php echo e($s->error_message); ?>">
                        <?php echo e($s->error_message ?? '—'); ?>

                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="text-center py-12 text-brand-slate">
                        <p class="text-4xl mb-3">📭</p>
                        <p>Belum ada history pengiriman.</p>
                        <p class="text-xs mt-1">Kirim undangan dari halaman <a href="<?php echo e(route('admin.invitations.index')); ?>" class="text-brand-navy underline">Undangan</a>.</p>
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-brand-slate/10"><?php echo e($sends->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/AbsenKID/resources/views/admin/invitations/send-history.blade.php ENDPATH**/ ?>