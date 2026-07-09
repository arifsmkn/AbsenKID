<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard Live Monitoring'); ?>

<?php $__env->startSection('content'); ?>
<?php if(!$event): ?>
    <div class="text-center py-20">
        <p class="text-4xl mb-4">📅</p>
        <h2 class="text-xl font-semibold mb-2">Belum ada event aktif</h2>
        <a href="<?php echo e(route('admin.events.create')); ?>" class="btn-primary">Buat Event Sekarang</a>
    </div>
<?php else: ?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <div class="card-compact">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Undangan</p>
                <p class="text-xl font-bold text-blue-600 mt-0.5"><?php echo e(number_format($totalInvited)); ?></p>
            </div>
            <span class="text-2xl opacity-20">🎫</span>
        </div>
    </div>
    <div class="card-compact">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 uppercase tracking-wide">Sudah Hadir</p>
                <p class="text-xl font-bold text-green-600 mt-0.5" id="total-hadir"><?php echo e(number_format($totalAttended)); ?></p>
            </div>
            <span class="text-2xl opacity-20">✅</span>
        </div>
    </div>
    <div class="card-compact">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 uppercase tracking-wide">Belum Hadir</p>
                <p class="text-xl font-bold text-orange-500 mt-0.5" id="total-belum"><?php echo e(number_format($totalInvited - $totalAttended)); ?></p>
            </div>
            <span class="text-2xl opacity-20">⏳</span>
        </div>
    </div>
    <div class="card-compact">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 uppercase tracking-wide">Persentase</p>
                <p class="text-xl font-bold text-purple-600 mt-0.5" id="percentage"><?php echo e($percentage); ?>%</p>
            </div>
            <span class="text-2xl opacity-20">📊</span>
        </div>
    </div>
</div>


<div class="grid grid-cols-2 gap-3 mb-6">
    <div class="card-compact">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 uppercase tracking-wide">Konfirmasi Akan Hadir</p>
                <p class="text-xl font-bold text-emerald-500 mt-0.5"><?php echo e(number_format($totalConfirmedHadir)); ?></p>
            </div>
            <span class="text-2xl opacity-20">📝</span>
        </div>
    </div>
    <div class="card-compact">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 uppercase tracking-wide">Konfirmasi Tidak Hadir</p>
                <p class="text-xl font-bold text-red-400 mt-0.5"><?php echo e(number_format($totalConfirmedTidak)); ?></p>
            </div>
            <span class="text-2xl opacity-20">🚫</span>
        </div>
    </div>
    <p class="col-span-2 text-xs text-gray-400 -mt-2">ℹ️ Berdasarkan konfirmasi peserta di portal undangan — bukan kehadiran fisik (lihat "Sudah Hadir" di atas untuk data scan aktual).</p>
</div>


<div class="card mb-6">
    <div class="flex items-center justify-between mb-2">
        <h3 class="font-semibold">Progress Kehadiran — <?php echo e($event->nama); ?></h3>
        <span class="text-sm text-gray-500"><?php echo e($event->tanggal?->format('d M Y')); ?></span>
    </div>
    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-4 rounded-full transition-all duration-1000"
             id="progress-bar" style="width: <?php echo e($percentage); ?>%"></div>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1"><?php echo e($totalAttended); ?> dari <?php echo e($totalInvited); ?> tamu hadir (<?php echo e($percentage); ?>%)</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    
    <div class="card">
        <h3 class="font-semibold mb-4">Kehadiran per SubCo</h3>
        <div class="space-y-3 max-h-80 overflow-y-auto" id="subco-list">
            <?php $__currentLoopData = $subcoStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $inv = $invitationBySubco[$stat->subco] ?? 1; $pct = round(($stat->hadir/$inv)*100); ?>
            <div>
                <div class="flex justify-between items-center text-sm mb-1 gap-2">
                    <span class="font-medium truncate max-w-[160px] flex items-center gap-1.5">
                        <?php if($pct < 100): ?>
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500 shrink-0" title="Belum 100% hadir"></span>
                        <?php endif; ?>
                        <?php echo e($stat->subco); ?>

                    </span>
                    <span class="flex items-center gap-2 shrink-0">
                        <span class="text-gray-500"><?php echo e($stat->hadir); ?>/<?php echo e($inv); ?> (<?php echo e($pct); ?>%)</span>
                        <?php if($pct < 100): ?>
                            <a href="<?php echo e(route('admin.invitations.index', ['subco' => $stat->subco, 'filter' => 'belum_hadir'])); ?>"
                               class="text-xs px-2 py-0.5 rounded-full font-medium transition-colors"
                               style="background:rgba(249,115,22,0.12); color:#ea580c"
                               title="Lihat peserta <?php echo e($stat->subco); ?> yang belum hadir">
                                Lihat →
                            </a>
                        <?php endif; ?>
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="h-2 rounded-full <?php echo e($pct < 100 ? 'bg-orange-400' : 'bg-green-500'); ?>" style="width: <?php echo e($pct); ?>%"></div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if($subcoStats->isEmpty()): ?>
                <p class="text-center text-gray-400 py-6 text-sm">Belum ada data kehadiran</p>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold">Scan Terbaru</h3>
            <span class="flex items-center gap-1 text-xs text-green-500">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Live
            </span>
        </div>
        <div class="space-y-2 max-h-80 overflow-y-auto" id="recent-list">
            <?php $__currentLoopData = $recentAttendances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center gap-3 p-2 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-sm font-bold text-blue-700 dark:text-blue-300 shrink-0">
                    <?php echo e(strtoupper(substr($att->employee->nama, 0, 1))); ?>

                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate"><?php echo e($att->employee->nama); ?></p>
                    <p class="text-xs text-gray-500 truncate"><?php echo e($att->employee->subco); ?> · <?php echo e($att->employee->npk); ?></p>
                </div>
                <span class="text-xs text-gray-400 shrink-0"><?php echo e($att->scanned_at->format('H:i')); ?></span>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Poll every 10 seconds for live updates
function refreshDashboard() {
    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text()).then(() => {}).catch(() => {});
}
// Simple auto-refresh setiap 15 detik
setTimeout(() => location.reload(), 15000);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/AbsenKID/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>