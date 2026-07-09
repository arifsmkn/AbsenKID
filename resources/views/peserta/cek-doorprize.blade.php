<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Doorprize — {{ $event?->nama ?? 'Konvensi Improvement Dharma' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex items-center justify-center p-4 font-sans"
      style="@if($event?->wallpaper_url) background: linear-gradient(135deg, rgba(26,46,64,0.75), rgba(26,56,80,0.75)), url('{{ $event->wallpaper_url }}') center/cover no-repeat fixed; @else background: linear-gradient(135deg, #1a2e40 0%, #244C6B 50%, #1a3850 100%); @endif">

<div class="w-full max-w-md">

    {{-- Logo --}}
    <div class="text-center mb-6">
        @if($event?->logo_url)
            <img src="{{ $event->logo_url }}" class="h-28 w-auto object-contain mx-auto mb-3 drop-shadow-xl" alt="{{ $event->nama }}">
        @else
            <img src="{{ asset('images/dharma-group.png') }}" class="h-20 w-auto object-contain mx-auto mb-3 drop-shadow-xl" alt="Dharma Group">
        @endif
        <h1 class="text-xl font-bold text-white">Cek Hasil Doorprize</h1>
        <p class="text-blue-300 text-sm mt-1">{{ $event?->nama ?? 'Konvensi Improvement Dharma' }}</p>
    </div>

    {{-- Form cek --}}
    <div class="bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 p-6 shadow-2xl mb-4">
        <form method="GET" action="{{ route('peserta.cek-doorprize') }}" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-white/80 mb-1.5">Masukkan NPK Anda</label>
                <input type="text"
                       name="npk"
                       value="{{ request('npk') }}"
                       placeholder="Contoh: 1234567"
                       class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-yellow-400"
                       required autocomplete="off">
            </div>
            <button type="submit"
                    class="w-full py-3 bg-yellow-500 hover:bg-yellow-400 text-gray-900 font-bold rounded-xl transition-all shadow-lg text-base">
                🔍 Cek Doorprize
            </button>
        </form>
    </div>

    {{-- Hasil --}}
    @if(request('npk'))
        @if($winner)
        <div class="rounded-2xl overflow-hidden shadow-2xl">
            <div class="p-5 text-white text-center" style="background: linear-gradient(135deg, #f59e0b, #ef4444)">
                <p class="text-lg font-black">🎉 Selamat!</p>
                <p class="text-sm opacity-80 mt-0.5">Anda memenangkan doorprize</p>
            </div>
            @php
                $cekMeta = [
                    'doorprize'       => ['emoji' => '🎁', 'label' => 'Doorprize', 'badge' => 'bg-blue-100 text-blue-700'],
                    'doorprize_utama' => ['emoji' => '🥈', 'label' => 'Doorprize Utama', 'badge' => 'bg-slate-200 text-slate-700'],
                    'grand_prize'     => ['emoji' => '🏆', 'label' => 'Grand Prize', 'badge' => 'bg-yellow-100 text-yellow-700'],
                ];
                $cekType = $cekMeta[$winner->doorprize?->type] ?? $cekMeta['doorprize'];
            @endphp
            <div class="bg-white p-5">
                <div class="flex items-center gap-4">
                    @if($winner->doorprize?->type !== 'doorprize' && $winner->doorprize?->gambar)
                        <img src="{{ $winner->doorprize->gambar_url }}"
                             class="h-20 w-20 object-contain rounded-xl border border-gray-100"
                             alt="Hadiah">
                    @else
                        <div class="text-5xl">{{ $cekType['emoji'] }}</div>
                    @endif
                    <div>
                        <p class="font-black text-gray-800 text-xl">{{ $employee?->nama }}</p>
                        <p class="text-gray-500 text-sm">NPK: {{ $employee?->npk }}</p>
                        <p class="text-gray-500 text-sm">{{ $employee?->subco }}</p>
                        <div class="mt-2 px-3 py-1.5 rounded-xl inline-block {{ $cekType['badge'] }}">
                            <p class="font-bold text-sm">
                                {{ $cekType['emoji'] }} {{ $cekType['label'] }}:
                                {{ $winner->doorprize?->nama_hadiah ?? 'Hadiah' }}
                            </p>
                        </div>
                        <p class="text-gray-400 text-xs mt-1">Pukul {{ $winner->won_at->format('H:i') }} WIB</p>
                    </div>
                </div>
                <p class="text-center text-gray-400 text-xs mt-4">Tunjukkan halaman ini kepada panitia untuk mengambil hadiah.</p>
            </div>
        </div>

        @elseif($employee)
        <div class="bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 p-5 text-center">
            <p class="text-4xl mb-3">😔</p>
            <p class="text-white font-semibold">Belum menang doorprize</p>
            <p class="text-blue-300 text-sm mt-1">{{ $employee->nama }} ({{ $employee->npk }})</p>
            <p class="text-white/50 text-xs mt-2">Tetap semangat! Doorprize masih berlangsung.</p>
        </div>

        @else
        <div class="bg-red-500/20 border border-red-500/40 rounded-2xl p-5 text-center">
            <p class="text-red-200 font-semibold">NPK tidak ditemukan</p>
            <p class="text-red-300/70 text-sm mt-1">Pastikan NPK yang dimasukkan benar.</p>
        </div>
        @endif
    @endif

    <div class="text-center mt-5 space-y-2">
        <a href="{{ route('peserta.login') }}" class="block text-white/50 text-xs hover:text-white/80 transition-colors">← Kembali ke Portal Peserta</a>
        <a href="{{ route('home') }}" class="block text-white/30 text-xs hover:text-white/60 transition-colors">Halaman Utama</a>
    </div>
</div>

</body>
</html>
