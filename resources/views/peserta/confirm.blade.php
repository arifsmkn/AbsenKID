<!DOCTYPE html>
@php
    $theme = $event?->theme_config ?? [];
    $themePrimary = $theme['primary_color'] ?? '#244C6B';
    $themeSecondary = $theme['secondary_color'] ?? '#559bcd';
@endphp
<html lang="id" style="--ev-primary: {{ $themePrimary }}; --ev-secondary: {{ $themeSecondary }};">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Kehadiran — {{ $event?->nama ?? 'Konvensi Improvement Dharma' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col font-sans"
      style="@if($event?->wallpaper_url) background: linear-gradient(135deg, rgba(26,46,64,0.85), rgba(26,56,80,0.85)), url('{{ $event->wallpaper_url }}') center/cover no-repeat fixed; @else background: linear-gradient(135deg, #1a2e40 0%, var(--ev-primary) 50%, #1a3850 100%); @endif">

{{-- Navbar --}}
<nav class="px-4 py-3 flex items-center justify-between"
     style="background: rgba(26,46,64,0.7); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(255,255,255,0.1)">
    <div class="flex items-center gap-3">
        <img src="{{ asset('images/dharma-group.png') }}" class="h-8 w-auto object-contain"
             style="filter: drop-shadow(0 1px 3px rgba(255,255,255,0.5))" alt="Dharma Group">
        @if($event?->logo)
            <img src="{{ asset('storage/'.$event->logo) }}" class="h-8 w-auto object-contain" alt="">
        @endif
    </div>
    <form method="POST" action="{{ route('peserta.logout') }}">
        @csrf
        <button class="text-xs px-3 py-1.5 rounded-lg font-medium"
                style="background:rgba(208,63,66,0.2); color:#ef9899; border:1px solid rgba(208,63,66,0.3)">
            Keluar
        </button>
    </form>
</nav>

<div class="flex-1 flex items-center justify-center p-4">
    <div class="w-full max-w-md">

        {{-- Header --}}
        <div class="text-center mb-8">
            @if($event?->logo)
                <img src="{{ asset('storage/'.$event->logo) }}" class="h-20 w-auto object-contain mx-auto mb-4 drop-shadow-2xl" alt="">
            @endif
            <h1 class="text-2xl font-bold text-white mb-1">Konfirmasi Kehadiran</h1>
            <p class="text-blue-200 text-sm">{{ $event?->nama ?? 'Konvensi Improvement Dharma' }}</p>
        </div>

        {{-- Info Peserta --}}
        <div class="mb-6 p-4 rounded-2xl text-center"
             style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15)">
            <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-2xl font-black text-white mx-auto mb-3">
                {{ strtoupper(substr($employee->nama, 0, 1)) }}
            </div>
            <p class="font-bold text-white text-lg">{{ $employee->nama }}</p>
            <p class="text-blue-200 text-sm">{{ $employee->npk }} · {{ $employee->subco }}</p>
            <p class="text-white/50 text-xs mt-0.5">{{ $employee->jabatan }}</p>
        </div>

        {{-- Pilihan --}}
        <p class="text-center text-white/70 text-sm mb-4">Apakah Anda akan hadir pada acara ini?</p>

        <div class="space-y-3">
            <form method="POST" action="{{ route('peserta.konfirmasi.post') }}">
                @csrf
                <input type="hidden" name="status" value="hadir">
                <button type="submit"
                        class="w-full py-4 rounded-2xl font-bold text-white text-lg transition-all active:scale-95 shadow-xl"
                        style="background: linear-gradient(135deg, #16a34a, #15803d); box-shadow: 0 6px 24px rgba(22,163,74,0.4)">
                    ✅ Ya, Saya Akan Hadir
                </button>
            </form>

            <form method="POST" action="{{ route('peserta.konfirmasi.post') }}">
                @csrf
                <input type="hidden" name="status" value="tidak_hadir">
                <button type="submit"
                        class="w-full py-4 rounded-2xl font-bold text-white/70 text-lg transition-all active:scale-95"
                        style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.2)">
                    ❌ Tidak, Saya Tidak Bisa Hadir
                </button>
            </form>
        </div>

        @if($event?->tanggal)
        <div class="mt-6 text-center text-white/40 text-xs space-y-1">
            <p>📅 {{ $event->tanggal->isoFormat('dddd, D MMMM Y') }}</p>
            @if($event->waktu_mulai)
                <p>🕗 {{ substr($event->waktu_mulai, 0, 5) }} – {{ substr($event->waktu_selesai ?? '', 0, 5) }} WIB</p>
            @endif
            @if($event->lokasi)
                <p>📍 {{ $event->lokasi }}</p>
            @endif
        </div>
        @endif

    </div>
</div>
@include('peserta.partials.auto-logout')
</body>
</html>
