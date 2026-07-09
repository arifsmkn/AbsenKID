<!DOCTYPE html>
<?php
    $theme = $event?->theme_config ?? [];
    $themePrimary = $theme['primary_color'] ?? '#244C6B';
    $themeSecondary = $theme['secondary_color'] ?? '#559bcd';
?>
<html lang="id" style="--ev-primary: <?php echo e($themePrimary); ?>; --ev-secondary: <?php echo e($themeSecondary); ?>;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Peserta — <?php echo e($event?->nama ?? 'Konvensi Improvement Dharma'); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body class="min-h-screen flex items-center justify-center p-4 pt-20 font-sans"
      style="<?php if($event?->wallpaper_url): ?> background: linear-gradient(135deg, rgba(26,46,64,0.75), rgba(26,56,80,0.75)), url('<?php echo e($event->wallpaper_url); ?>') center/cover no-repeat; <?php else: ?> background: linear-gradient(135deg, #1a2e40 0%, var(--ev-primary) 50%, #1a3850 100%); <?php endif; ?>">


<nav x-data="{ open: false }" class="fixed top-0 left-0 right-0 z-50 border-b border-white/10"
     style="background: rgba(221, 244, 246, 0.4); backdrop-filter: blur(12px);">
    <div class="max-w-7xl mx-auto px-4 py-2.5 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="<?php echo e(asset('images/dharma-group.png')); ?>" class="h-9 w-auto object-contain"
                 style="filter: drop-shadow(0 1px 4px rgba(255,255,255,0.7)) drop-shadow(0 0 10px rgba(255,255,255,0.35))"
                 alt="Dharma Group">
        </div>
        <div class="hidden md:flex items-center gap-6 text-sm font-medium text-white/80">
            <a href="<?php echo e(route('home')); ?>" class="hover:text-white transition-colors">← Beranda</a>
        </div>

        
        <button @click="open = !open" class="md:hidden relative w-9 h-9 flex items-center justify-center text-white/80" aria-label="Buka menu">
            <span class="absolute block w-6 h-0.5 bg-current transition-all duration-300"
                  :class="open ? 'rotate-45' : '-translate-y-1.5'"></span>
            <span class="absolute block w-6 h-0.5 bg-current transition-all duration-300"
                  :class="open ? 'opacity-0' : 'opacity-100'"></span>
            <span class="absolute block w-6 h-0.5 bg-current transition-all duration-300"
                  :class="open ? '-rotate-45' : 'translate-y-1.5'"></span>
        </button>
    </div>

    
    <div x-show="open" x-cloak @click="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden border-t border-white/10 px-4 py-3 flex flex-col gap-3 text-sm font-medium text-white/80"
         style="background: rgba(26,46,64,0.95); backdrop-filter: blur(12px);">
        <a href="<?php echo e(route('home')); ?>" class="hover:text-white transition-colors">← Beranda</a>
    </div>
</nav>

<div class="w-full max-w-md">

    
    <div class="text-center mb-6">
        <?php if($event?->logo): ?>
            <img src="<?php echo e(asset('storage/'.$event->logo)); ?>"
                 class="h-[4.55rem] w-auto object-contain mx-auto mb-3"
                 alt="Logo Event">
        <?php endif; ?>
        <p class="text-blue-300 text-sm mt-1">Portal Peserta</p>
    </div>

    
    <div class="bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 p-8 shadow-2xl">
        <h2 class="text-white font-semibold text-lg mb-6 text-center">Masuk sebagai Peserta</h2>

        <?php if($errors->any()): ?>
        <div class="mb-4 p-3 rounded-xl bg-red-500/20 border border-red-500/40 text-red-200 text-sm">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> <p>⚠ <?php echo e($e); ?></p> <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('peserta.login.post')); ?>" class="space-y-5">
            <?php echo csrf_field(); ?>

            <div>
                <label class="block text-sm font-medium text-white/80 mb-1.5">Singkatan Perusahaan</label>
                <div class="relative">
                    <select name="singkatan"
                            class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-blue-400 appearance-none"
                            required>
                        <option value="" class="text-gray-900">-- Pilih Perusahaan --</option>
                        <?php $__currentLoopData = $subcos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($s->singkatan): ?>
                            <option value="<?php echo e($s->singkatan); ?>" class="text-gray-900"
                                    <?php echo e(old('singkatan') == $s->singkatan ? 'selected' : ''); ?>>
                                <?php echo e($s->singkatan); ?> — <?php echo e($s->nama); ?>

                            </option>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <span class="absolute right-3 top-3.5 text-white/50 pointer-events-none">▼</span>
                </div>
                <?php if($subcos->where('singkatan', '!=', null)->isEmpty()): ?>
                    <p class="text-yellow-300 text-xs mt-1">⚠ Singkatan belum diisi di master SubCo</p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-white/80 mb-1.5">NPK (Nomor Pokok Karyawan)</label>
                <input type="text"
                       name="npk"
                       value="<?php echo e(old('npk')); ?>"
                       placeholder="Masukkan NPK Anda"
                       class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-blue-400"
                       required autocomplete="off">
                <p class="text-white/40 text-xs mt-1">NPK digunakan sebagai password</p>
            </div>

            <button type="submit"
                    class="w-full py-3 text-white font-semibold rounded-xl transition-all text-base"
                    style="background:#D03F42; box-shadow: 0 4px 16px rgba(208,63,66,0.3)">
                Masuk →
            </button>
        </form>

        
        <div class="mt-5 pt-4 border-t border-white/10">
            <a href="<?php echo e(route('peserta.cek-doorprize')); ?>"
               class="flex items-center justify-center gap-2 w-full py-2.5 font-bold rounded-xl transition-all text-sm"
               style="background:linear-gradient(135deg,#f59e0b,#ef4444); color:#fff; box-shadow:0 4px 14px rgba(239,68,68,0.45);">
                🎁 Cek Hasil Doorprize
            </a>
            <p class="text-white/30 text-xs text-center mt-2">Cek apakah kamu menang doorprize tanpa login</p>
        </div>

        <div class="mt-3 text-center">
            <p class="text-white/40 text-xs">Masalah login? Hubungi panitia</p>
        </div>
    </div>

</div>

</body>
</html>
<?php /**PATH /var/www/AbsenKID/resources/views/peserta/login.blade.php ENDPATH**/ ?>