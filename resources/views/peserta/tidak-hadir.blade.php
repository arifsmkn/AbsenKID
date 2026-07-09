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
    <title>Ketidakhadiran Tercatat — {{ $event?->nama ?? 'Konvensi Improvement Dharma' }}</title>
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
    <div class="w-full max-w-md text-center">

        @if($event?->logo)
            <img src="{{ asset('storage/'.$event->logo) }}" class="h-20 w-auto object-contain mx-auto mb-6 drop-shadow-2xl" alt="">
        @endif

        <div class="text-6xl mb-4">🙏</div>

        <h1 class="text-2xl font-bold text-white mb-3">Terima Kasih</h1>
        <p class="text-blue-200 mb-2">Ketidakhadiran Anda telah tercatat.</p>
        <p class="text-white/50 text-sm mb-8">Semoga di kesempatan berikutnya kita bisa bertemu.</p>

        {{-- Info Peserta --}}
        <div class="mb-6 p-4 rounded-2xl"
             style="background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.15)">
            <p class="font-bold text-white">{{ $employee->nama ?? '' }}</p>
            <p class="text-blue-200 text-sm">{{ $employee->subco ?? '' }}</p>
        </div>

        {{-- Kontak panitia --}}
        @if($panitia['whatsapp'] || $panitia['email'])
        <div class="space-y-2">
            <p class="text-white/40 text-xs mb-3">Ada pertanyaan? Hubungi panitia:</p>
            @if($panitia['whatsapp'])
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $panitia['whatsapp']) }}" target="_blank"
               class="flex items-center justify-center gap-2 w-full py-3 rounded-xl font-medium text-sm"
               style="background: rgba(22,163,74,0.2); border: 1px solid rgba(22,163,74,0.3); color: #86efac">
                💬 WhatsApp Panitia
            </a>
            @endif
        </div>
        @endif

    </div>
</div>
@include('peserta.partials.auto-logout')
</body>
</html>
