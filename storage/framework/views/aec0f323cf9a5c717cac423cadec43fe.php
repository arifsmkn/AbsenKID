<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-in — <?php echo e($event?->nama ?? 'Konvensi Improvement Dharma'); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <style>
        body { background: #0f172a; }
        @keyframes pulse-ring { 0%,100%{transform:scale(1);opacity:.3} 50%{transform:scale(1.15);opacity:.6} }
        .pulse-ring { animation: pulse-ring 2s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center text-white px-4" x-data="scanKiosk()">

    
    <div class="fixed top-0 left-0 right-0 bg-gray-900/80 backdrop-blur-sm border-b border-white/10 px-6 py-3 flex items-center justify-between z-10">
        <div class="flex items-center gap-3">
            <?php if($event?->logo): ?>
                <img src="<?php echo e(asset('storage/'.$event->logo)); ?>" class="h-8 w-auto object-contain" alt="">
            <?php endif; ?>
            <span class="font-semibold text-sm"><?php echo e($event?->nama ?? 'Check-in'); ?></span>
        </div>
        <a href="<?php echo e(route('home')); ?>" class="text-xs text-white/50 hover:text-white/80">← Kembali</a>
    </div>

    <div class="text-center mt-20 mb-8">
        <h1 class="text-3xl font-bold mb-2">Scan QR Code Anda</h1>
        <p class="text-gray-400">Arahkan kamera ke QR code pada undangan Anda</p>
    </div>

    
    <div class="relative mb-8" x-show="!result">
        <div class="relative w-72 h-72 mx-auto" x-show="!cameraError">
            <div class="absolute inset-0 rounded-2xl border-2 border-blue-500/50 pulse-ring"></div>
            <div id="qr-reader" class="w-72 h-72 rounded-2xl overflow-hidden bg-gray-800"></div>
            
            <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-blue-500 rounded-tl-xl"></div>
            <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-blue-500 rounded-tr-xl"></div>
            <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-blue-500 rounded-bl-xl"></div>
            <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-blue-500 rounded-br-xl"></div>
        </div>
        
        <div x-show="cameraError" x-cloak class="w-72 mx-auto rounded-2xl p-5 text-center"
             style="background:rgba(208,63,66,0.1); border:1px solid rgba(208,63,66,0.3)">
            <p class="text-3xl mb-2">📵</p>
            <p class="text-sm font-semibold mb-1" style="color:#ef9899">Kamera tidak bisa diakses</p>
            <p class="text-xs text-gray-400" x-text="cameraError"></p>
        </div>
    </div>

    
    <div x-show="result" x-cloak class="text-center">
        <div x-show="result === 'loading'" class="text-blue-400">
            <div class="w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
            <p>Memproses...</p>
        </div>
    </div>

    
    <div class="mt-4 w-full max-w-xs" x-show="!result">
        <p class="text-gray-500 text-xs text-center mb-2">QR tidak bisa di-scan kamera? Masukkan NPK peserta:</p>
        <form method="POST" action="<?php echo e(route('scan.npk')); ?>" class="flex gap-2">
            <?php echo csrf_field(); ?>
            <input type="text" name="npk" placeholder="Masukkan NPK..." class="input flex-1 bg-gray-800 border-gray-700 text-white text-sm" required>
            <button class="btn-primary">Go</button>
        </form>
    </div>

    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
    function scanKiosk() {
        return {
            result: null,
            cameraError: null,
            init() {
                if (!window.isSecureContext) {
                    this.cameraError = 'Halaman ini dibuka lewat HTTP biasa. Browser (terutama di iPhone/iOS) memblokir akses kamera kecuali lewat HTTPS. Buka https://' + window.location.host + ' lalu coba lagi, atau pakai input manual di bawah.';
                    return;
                }
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    this.cameraError = 'Browser ini tidak mendukung akses kamera. Pakai input manual di bawah.';
                    return;
                }
                const html5QrCode = new Html5Qrcode("qr-reader");
                html5QrCode.start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: { width: 250, height: 250 } },
                    (decodedText) => {
                        html5QrCode.stop();
                        this.result = 'loading';
                        // Extract qr code from URL if full URL scanned
                        let code = decodedText;
                        const match = decodedText.match(/\/scan\/qr\/([^\/\s]+)/);
                        if (match) code = match[1];
                        window.location.href = '/scan/qr/' + code;
                    },
                    () => {}
                ).catch(err => {
                    console.log('Camera error:', err);
                    this.cameraError = 'Izin kamera ditolak atau tidak ada kamera tersedia. Pakai input manual di bawah, atau cek izin kamera untuk browser ini di pengaturan device.';
                });
            }
        }
    }
    </script>
</body>
</html>
<?php /**PATH /var/www/AbsenKID/resources/views/public/scan.blade.php ENDPATH**/ ?>