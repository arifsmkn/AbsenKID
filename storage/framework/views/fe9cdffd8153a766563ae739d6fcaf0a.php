<!DOCTYPE html>
<?php
    $theme = $event?->theme_config ?? [];
    $themePrimary = $theme['primary_color'] ?? '#244C6B';
    $themeSecondary = $theme['secondary_color'] ?? '#40647E';
?>
<html lang="id" style="--ev-primary: <?php echo e($themePrimary); ?>; --ev-secondary: <?php echo e($themeSecondary); ?>;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Peserta — <?php echo e($event?->nama ?? 'Konvensi'); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            #qr-card { box-shadow: none !important; }
        }
    </style>
</head>
<body class="min-h-screen font-sans"
      style="<?php if($event?->wallpaper_url): ?> background: linear-gradient(rgba(237,242,247,0.88), rgba(237,242,247,0.88)), url('<?php echo e($event->wallpaper_url); ?>') center/cover fixed no-repeat; <?php else: ?> background:#EDF2F7; <?php endif; ?>">


<nav class="px-4 py-3 flex items-center justify-between sticky top-0 z-10 no-print"
     style="background:var(--ev-primary); box-shadow:0 2px 12px rgba(36,76,107,0.3)">
    <div class="flex items-center gap-3">
       <img src="<?php echo e(asset('images/dharma-group.png')); ?>" class="h-8 w-auto object-contain"
             style="filter: drop-shadow(0 1px 3px rgba(36,76,107,0.5))" alt="Dharma Group"> 
        <?php if($event?->logo): ?>
            <img src="<?php echo e(asset('storage/'.$event->logo)); ?>" class="h-8 w-auto object-contain" alt="">
        <?php endif; ?>
        <div>
            <p class="font-semibold text-sm text-white leading-tight"><?php echo e($event?->nama ?? 'Konvensi Improvement Dharma'); ?></p>
            <p class="text-xs" style="color:rgba(123,145,161,0.8)">Portal Peserta</p>
        </div>
    </div>
    <form method="POST" action="<?php echo e(route('peserta.logout')); ?>" class="no-print">
        <?php echo csrf_field(); ?>
        <button class="text-xs flex items-center gap-1 px-3 py-1.5 rounded-lg transition-all font-medium"
                style="background:rgba(208,63,66,0.2); color:#ef9899; border:1px solid rgba(208,63,66,0.3)">
            🚪 Keluar
        </button>
    </form>
</nav>

<div class="max-w-lg mx-auto px-4 py-6 space-y-4">

    
    <?php if(session('success')): ?>
    <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-800 text-sm flex items-center gap-2 no-print">
        ✅ <?php echo e(session('success')); ?>

    </div>
    <?php endif; ?>

    
    <?php if($doorprizeWin): ?>
    <div class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-2xl p-5 text-white shadow-xl relative overflow-hidden no-print">
        <div class="absolute top-0 right-0 text-8xl opacity-10 leading-none">🎁</div>
        <div class="relative">
            <p class="text-xs font-bold uppercase tracking-widest mb-1 opacity-80">🎉 Selamat!</p>
            <p class="text-xl font-black mb-3">Anda Menang Doorprize!</p>
            <div class="flex items-center gap-4 bg-white/20 rounded-xl p-3">
                <?php if($doorprizeWin->doorprize?->gambar): ?>
                    <img src="<?php echo e($doorprizeWin->doorprize->gambar_url); ?>"
                         class="h-20 w-20 object-contain rounded-lg bg-white/10 p-1" alt="Hadiah">
                <?php else: ?>
                    <div class="text-5xl">🎁</div>
                <?php endif; ?>
                <div>
                    <p class="font-bold text-lg"><?php echo e($doorprizeWin->doorprize?->nama_hadiah ?? 'Hadiah Doorprize'); ?></p>
                    <p class="text-sm opacity-80">Pukul <?php echo e($doorprizeWin->won_at->format('H:i')); ?> WIB</p>
                </div>
            </div>
            <p class="text-xs mt-3 opacity-70">Tunjukkan halaman ini kepada panitia untuk mengambil hadiah Anda.</p>
        </div>
    </div>
    <?php endif; ?>

    
    <?php if($invitation): ?>
    <div id="qr-card" class="bg-white rounded-3xl shadow-2xl overflow-hidden">

        
        <div class="px-6 py-5 text-white text-center"
             style="background: linear-gradient(135deg, var(--ev-primary), var(--ev-secondary))">
           <!-- <img src="<?php echo e(asset('images/dharma-group.png')); ?>" class="h-7 w-auto object-contain mx-auto mb-2" -->
                 <!-- style="filter: drop-shadow(0 1px 4px rgba(255,255,255,0.6))" alt="Dharma Group" crossorigin="anonymous">  -->
            <!-- <?php if($event->logo): ?> -->
                <img src="<?php echo e(asset('storage/'.$event->logo)); ?>" class="h-16 w-auto object-contain mx-auto mb-2" alt="" crossorigin="anonymous">
            <?php endif; ?>
            <h1 class="font-bold text-base leading-tight"><?php echo e($event->nama); ?></h1>
            <?php if($event->tanggal): ?>
                <p class="text-blue-200 text-xs mt-1"><?php echo e($event->tanggal->isoFormat('dddd, D MMMM Y')); ?></p>
            <?php endif; ?>
            <?php if($event->lokasi): ?>
                <p class="text-blue-200 text-xs">📍 <?php echo e($event->lokasi); ?></p>
            <?php endif; ?>
        </div>

        
        <div class="px-8 pt-6 pb-2 flex flex-col items-center">
            <div class="p-3 bg-white rounded-2xl shadow-inner border border-gray-100 inline-block">
                <?php
                    $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                        ->size(260)->margin(1)->errorCorrection('H')
                        ->generate(route('scan.qr', $invitation->qr_code));
                ?>
                <?php echo $qrSvg; ?>

            </div>
            <p class="text-xs text-gray-300 mt-2 font-mono text-center break-all px-2">
                <?php echo e($invitation->qr_code); ?>

            </p>
        </div>

        
        <div class="px-6 py-4 text-center border-t border-gray-100 mt-2">
            <p class="font-bold text-gray-800 text-xl leading-tight"><?php echo e($employee->nama); ?></p>
            <p class="text-gray-500 text-sm mt-0.5"><?php echo e($employee->npk); ?> · <?php echo e($employee->subco); ?></p>
            <p class="text-gray-400 text-xs mt-0.5"><?php echo e($employee->jabatan); ?></p>
            <div class="mt-3">
                <span class="inline-flex items-center gap-1 px-4 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold">
                    🎫 Undangan Resmi
                </span>
            </div>
        </div>

        
        <div class="pb-4 text-center">
            <p class="text-xs text-gray-300">Scan QR ini di booth check-in saat tiba di venue</p>
        </div>
    </div>

    
    <div class="no-print">
        <button id="btn-download" onclick="downloadKartu()"
                class="flex items-center justify-center gap-2 w-full py-3.5 active:scale-95 text-white font-bold rounded-2xl transition-all text-base"
                style="background:var(--ev-primary); box-shadow: 0 4px 20px rgba(36,76,107,0.4)">
            <span id="btn-icon">⬇️</span>
            <span id="btn-text">Download Kartu Undangan</span>
        </button>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-2xl p-5 shadow-sm text-center" style="border:1px solid rgba(36,76,107,0.1)">
        <p class="text-gray-500 text-sm">Undangan belum tersedia. Hubungi panitia.</p>
    </div>
    <?php endif; ?>

    
    <?php
        $hasFisik = \App\Models\Attendance::where('event_id', $event->id)
            ->where('employee_npk', $employee->npk)->exists();
    ?>
    <?php if($hasFisik): ?>
    <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-center no-print">
        <p class="text-green-700 font-semibold text-sm">✅ Check-in fisik sudah tercatat</p>
        <p class="text-green-500 text-xs mt-0.5">Anda sudah masuk ke dalam pool doorprize</p>
    </div>
    <?php else: ?>
    <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-center no-print">
        <p class="text-blue-700 text-sm font-medium">📌 Scan QR di booth check-in saat tiba di venue</p>
        <p class="text-blue-400 text-xs mt-0.5">Check-in fisik diperlukan untuk masuk ke pool doorprize</p>
    </div>
    <?php endif; ?>

    
    <?php if($event?->tema): ?>
    <div class="bg-white rounded-2xl shadow-sm p-5 no-print" style="border:1px solid rgba(36,76,107,0.1)">
        <h3 class="font-semibold text-gray-800 mb-2">Tema Acara</h3>
        <p class="text-blue-600 italic font-medium">"<?php echo e($event->tema); ?>"</p>
        <?php if($event->deskripsi): ?>
            <p class="text-gray-500 text-sm mt-2"><?php echo e($event->deskripsi); ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    
    <div class="bg-white rounded-2xl shadow-sm p-5 no-print" style="border:1px solid rgba(36,76,107,0.1)">
        <h3 class="font-semibold text-gray-800 mb-3">Hubungi Panitia</h3>
        <div class="space-y-2">
            <?php if($panitia['whatsapp']): ?>
            <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $panitia['whatsapp'])); ?>" target="_blank"
               class="flex items-center gap-3 p-3 rounded-xl bg-green-50 border border-green-200 hover:bg-green-100 transition-colors">
                <span class="text-2xl">💬</span>
                <div>
                    <p class="font-semibold text-green-700 text-sm">WhatsApp</p>
                    <p class="text-green-600 text-xs"><?php echo e($panitia['whatsapp']); ?></p>
                </div>
                <span class="ml-auto text-green-400">→</span>
            </a>
            <?php endif; ?>
            <?php if($panitia['email']): ?>
            <a href="mailto:<?php echo e($panitia['email']); ?>"
               class="flex items-center gap-3 p-3 rounded-xl bg-blue-50 border border-blue-200 hover:bg-blue-100 transition-colors">
                <span class="text-2xl">📧</span>
                <div>
                    <p class="font-semibold text-blue-700 text-sm">Email</p>
                    <p class="text-blue-600 text-xs"><?php echo e($panitia['email']); ?></p>
                </div>
                <span class="ml-auto text-blue-400">→</span>
            </a>
            <?php endif; ?>
            <?php if(!$panitia['whatsapp'] && !$panitia['email']): ?>
            <p class="text-gray-400 text-sm text-center py-2">Informasi kontak panitia belum diset.</p>
            <?php endif; ?>
        </div>
    </div>

</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
async function downloadKartu() {
    const btn  = document.getElementById('btn-download');
    const icon = document.getElementById('btn-icon');
    const text = document.getElementById('btn-text');
    btn.disabled = true; icon.textContent = '⏳'; text.textContent = 'Menyiapkan...';
    try {
        const card    = document.getElementById('qr-card');
        const clone   = card.cloneNode(true);
        const wrapper = document.createElement('div');
        wrapper.style.cssText = 'position:fixed;top:-9999px;left:-9999px;background:linear-gradient(135deg,#1a2e40 0%,<?php echo e($themePrimary); ?> 50%,#1a3850 100%);padding:28px 20px;border-radius:32px;display:inline-block;';
        wrapper.appendChild(clone);
        document.body.appendChild(wrapper);
        const canvas = await html2canvas(wrapper, { scale: 3, useCORS: true, allowTaint: true, backgroundColor: null });
        document.body.removeChild(wrapper);
        const a = document.createElement('a');
        a.download = 'undangan-<?php echo e($employee->npk); ?>-<?php echo e($event->tahun ?? date("Y")); ?>.png';
        a.href = canvas.toDataURL('image/png');
        a.click();
    } catch(e) {
        alert('Gagal membuat gambar. Coba screenshot manual.');
    } finally {
        btn.disabled = false; icon.textContent = '⬇️'; text.textContent = 'Download Kartu Undangan';
    }
}
</script>
<?php echo $__env->make('peserta.partials.auto-logout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>
</html>
<?php /**PATH /var/www/AbsenKID/resources/views/peserta/dashboard.blade.php ENDPATH**/ ?>