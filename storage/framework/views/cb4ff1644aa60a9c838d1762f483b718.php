<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <title><?php echo e(config('app.name', 'AbsenKID')); ?></title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
        <style>
            @keyframes spin-slow { to { transform: rotate(360deg); } }
            .spin-slow { animation: spin-slow 24s linear infinite; }
            @keyframes fadeInUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
            .fade-up { animation: fadeInUp 0.6s ease forwards; }
            .auth-input { color: #ffffff !important; }
            .auth-input::placeholder { color: rgba(255,255,255,0.35); }
            .auth-input:-webkit-autofill,
            .auth-input:-webkit-autofill:hover,
            .auth-input:-webkit-autofill:focus {
                -webkit-text-fill-color: #ffffff;
                -webkit-box-shadow: 0 0 0 1000px rgba(36,76,107,0.6) inset;
                transition: background-color 9999s ease-in-out 0s;
            }
        </style>
    </head>
    <body class="font-sans text-white antialiased" style="background:#1a2e40">
        <div class="min-h-screen flex flex-col justify-center items-center px-4 py-10 relative overflow-hidden">
            <div class="fixed inset-0 pointer-events-none overflow-hidden" style="z-index:0">
                <div class="absolute inset-0" style="background: radial-gradient(circle at 20% 20%, rgba(36,76,107,0.55), transparent 55%), radial-gradient(circle at 85% 80%, rgba(208,63,66,0.18), transparent 50%)"></div>
                <div class="w-96 h-96 rounded-full blur-3xl absolute -top-24 -left-24 spin-slow opacity-20" style="background:#244C6B"></div>
                <div class="w-96 h-96 rounded-full blur-3xl absolute -bottom-24 -right-24 spin-slow opacity-15" style="background:#D03F42; animation-direction:reverse"></div>
            </div>
            <div class="relative z-10 mb-8 fade-up">
                <a href="/" class="flex flex-col items-center gap-3">
                    <img src="<?php echo e(asset('images/dharma-group.png')); ?>" class="h-11 w-auto object-contain"
                         style="filter: drop-shadow(0 1px 4px rgba(255,255,255,0.6)) drop-shadow(0 0 10px rgba(255,255,255,0.3))" alt="Dharma Group">
                    <div class="text-center">
                        <p class="font-extrabold text-white tracking-tight text-lg leading-none">AbsenKID</p>
                        <p class="text-xs text-blue-300/80 mt-1">Admin Panel</p>
                    </div>
                </a>
            </div>
            <div class="relative z-10 w-full sm:max-w-md px-7 py-8 rounded-3xl fade-up"
                 style="background:rgba(36,76,107,0.25); backdrop-filter:blur(16px); border:1px solid rgba(255,255,255,0.12); box-shadow:0 20px 60px rgba(0,0,0,0.35)">
                <?php echo e($slot); ?>

            </div>
            <p class="relative z-10 mt-8 text-xs text-white/30 fade-up">© <?php echo e(date('Y')); ?> Dharma Group — AbsenKID</p>
        </div>
    </body>
</html>
<?php /**PATH /var/www/AbsenKID/resources/views/layouts/guest.blade.php ENDPATH**/ ?>