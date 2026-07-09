<!DOCTYPE html>
<html lang="id" class="{{ $appMode === 'dark' ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $event?->nama ?? 'Konvensi Improvement Dharma' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .hero-slide { transition: opacity 1s ease-in-out; }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }
        .fade-up { animation: fadeInUp 0.8s ease forwards; }
        @keyframes spin-slow { to { transform: rotate(360deg); } }
        .spin-slow { animation: spin-slow 20s linear infinite; }
    </style>
</head>
<body class="text-white font-sans overflow-x-hidden" style="background:#1a2e40" x-data="landing()">

{{-- NAVBAR --}}
<nav x-data="{ open: false }" class="fixed top-0 left-0 right-0 z-50 border-b border-white/10"
     style="background: rgba(221, 244, 246, 0.4); backdrop-filter: blur(12px);">
    <div class="max-w-7xl mx-auto px-4 py-2.5 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/dharma-group.png') }}" class="h-9 w-auto object-contain"
                 style="filter: drop-shadow(0 1px 4px rgba(255,255,255,0.7)) drop-shadow(0 0 10px rgba(255,255,255,0.35))"
                 alt="Dharma Group">
        </div>
        <div class="hidden md:flex items-center gap-6 text-sm font-medium text-white/80">
            <a href="#home" class="hover:text-white transition-colors">Home</a>
            <a href="#tentang" class="hover:text-white transition-colors">Tentang</a>
            <a href="#lokasi" class="hover:text-white transition-colors">Lokasi</a>
            <a href="{{ route('peserta.login') }}"
               class="px-4 py-1.5 rounded-full text-white font-semibold transition-colors"
               style="background:#D03F42">Portal Peserta</a>
        </div>

        {{-- Tombol hamburger (mobile) --}}
        <button @click="open = !open" class="md:hidden relative w-9 h-9 flex items-center justify-center text-white/80" aria-label="Buka menu">
            <span class="absolute block w-6 h-0.5 bg-current transition-all duration-300"
                  :class="open ? 'rotate-45' : '-translate-y-1.5'"></span>
            <span class="absolute block w-6 h-0.5 bg-current transition-all duration-300"
                  :class="open ? 'opacity-0' : 'opacity-100'"></span>
            <span class="absolute block w-6 h-0.5 bg-current transition-all duration-300"
                  :class="open ? '-rotate-45' : 'translate-y-1.5'"></span>
        </button>
    </div>

    {{-- Menu collapse (mobile) --}}
    <div x-show="open" x-cloak @click="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="md:hidden border-t border-white/10 px-4 py-3 flex flex-col gap-3 text-sm font-medium text-white/80"
         style="background: rgba(26,46,64,0.95); backdrop-filter: blur(12px);">
        <a href="#home" class="hover:text-white transition-colors">Home</a>
        <a href="#tentang" class="hover:text-white transition-colors">Tentang</a>
        <a href="#lokasi" class="hover:text-white transition-colors">Lokasi</a>
        <a href="{{ route('peserta.login') }}"
           class="px-4 py-2 rounded-full text-white font-semibold text-center transition-colors"
           style="background:#D03F42">Portal Peserta</a>
    </div>
</nav>

{{-- HERO SLIDER --}}
<section id="home" class="relative h-screen flex items-center justify-center overflow-hidden">
    @if($event && $event->slides->count())
        @foreach($event->slides as $i => $slide)
        <div class="absolute inset-0 hero-slide"
             :class="currentSlide === {{ $i }} ? 'opacity-100' : 'opacity-0'"
             style="z-index: 0">
            @if($slide->type === 'video')
                <video src="{{ $slide->file_url }}" autoplay muted loop playsinline class="w-full h-full object-cover"></video>
            @else
                <img src="{{ $slide->file_url }}" class="w-full h-full object-cover" alt="{{ $slide->judul }}">
            @endif
            <div class="absolute inset-0 bg-black/55"></div>
        </div>
        @endforeach
    @else
        <div class="absolute inset-0" style="background: linear-gradient(135deg, #1a2e40 0%, #244C6B 50%, #1a3850 100%)"></div>
        <div class="absolute inset-0 opacity-15">
            <div class="w-96 h-96 rounded-full blur-3xl absolute -top-20 -left-20 spin-slow" style="background:#40647E"></div>
            <div class="w-96 h-96 rounded-full blur-3xl absolute -bottom-20 -right-20 spin-slow" style="background:#D03F42"></div>
        </div>
    @endif

    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto">
        <div class="fade-up" style="animation-delay:0.2s">
            {{-- Logo KID (event logo) di tengah atas judul --}}
            @if($event?->logo)
                <img src="{{ asset('storage/'.$event->logo) }}" class="h-96 w-auto object-contain mx-auto mb-5 drop-shadow-2xl" alt="Logo KID">
            @else
                <img src="{{ asset('images/dharma-group.png') }}" class="h-16 w-auto object-contain mx-auto mb-5"
                     style="filter: drop-shadow(0 2px 6px rgba(255,255,255,0.7)) drop-shadow(0 0 20px rgba(255,255,255,0.3))"
                     alt="Dharma Group">
            @endif
            <p class="text-sm font-medium uppercase tracking-widest mb-3" style="color:#7B91A1">
                Dharma Group · {{ $event?->tahun ?? date('Y') }}
            </p>
            <h1 class="text-4xl md:text-6xl font-black leading-tight mb-4 text-white drop-shadow-lg">
                {{ $event?->nama ?? 'Konvensi Improvement Dharma ke-31' }}
            </h1>
            @if($event?->tema)
            <p class="text-xl md:text-2xl text-blue-300 italic mb-6">"{{ $event->tema }}"</p>
            @endif
            @if($event?->tanggal)
            <div class="flex items-center justify-center gap-4 text-white/70 text-sm mb-8">
                <span>📅 {{ $event->tanggal->isoFormat('dddd, D MMMM Y') }}</span>
                @if($event->waktu_mulai)
                    <span>🕗 {{ substr($event->waktu_mulai, 0, 5) }} – {{ substr($event->waktu_selesai ?? '', 0, 5) }} WIB</span>
                @endif
            </div>
            @endif
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('peserta.login') }}"
                   class="px-8 py-3 rounded-full font-semibold transition-all shadow-lg text-lg text-white"
                   style="background:#D03F42; box-shadow: 0 4px 20px rgba(208,63,66,0.35)">
                    Portal Peserta
                </a>
            </div>
        </div>
    </div>

    {{-- Slide indicators --}}
    @if($event && $event->slides->count() > 1)
    <div class="absolute bottom-8 left-0 right-0 flex justify-center gap-2 z-10">
        @foreach($event->slides as $i => $slide)
        <button @click="currentSlide={{ $i }}"
                :class="currentSlide==={{ $i }} ? 'w-8 bg-white' : 'w-2 bg-white/40'"
                class="h-2 rounded-full transition-all duration-300"></button>
        @endforeach
    </div>
    @endif
</section>

{{-- TENTANG --}}
<section id="tentang" class="py-24" style="background:#1d3549">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-14">
            <p class="text-sm font-semibold uppercase tracking-widest mb-2" style="color:#7B91A1">Tentang Event</p>
            <h2 class="text-3xl md:text-4xl font-bold text-white">{{ $event?->nama ?? 'Konvensi Improvement Dharma' }}</h2>
        </div>
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <p class="text-gray-300 text-lg leading-relaxed">
                    {{ $event?->deskripsi ?? 'Konvensi Improvement Dharma merupakan ajang tahunan bergengsi untuk berbagi inovasi, improvement, dan pencapaian dari seluruh unit bisnis Dharma Group.' }}
                </p>
                @if($event?->tema)
                <div class="mt-6 p-4 rounded-xl border" style="background:rgba(36,76,107,0.4); border-color:rgba(64,100,126,0.4)">
                    <p class="font-semibold" style="color:#7B91A1">Tema {{ $event->tahun }}</p>
                    <p class="text-white italic text-lg mt-1">"{{ $event->tema }}"</p>
                </div>
                @endif
            </div>
            <div class="grid grid-cols-2 gap-4">
                @php
                    $info = [
                        ['icon'=>'📅','label'=>'Tanggal','val'=>$event?->tanggal?->isoFormat('D MMMM Y') ?? '-'],
                        ['icon'=>'🕗','label'=>'Waktu','val'=>($event?->waktu_mulai ? substr($event->waktu_mulai,0,5).' WIB' : '-')],
                        ['icon'=>'📍','label'=>'Lokasi','val'=>$event?->lokasi ?? '-'],
                        ['icon'=>'🏆','label'=>'Edisi','val'=>'ke-'.($event?->tahun ? ($event->tahun - 1995) : '31')],
                    ];
                @endphp
                @foreach($info as $item)
                <div class="p-4 rounded-xl border" style="background:rgba(36,76,107,0.5); border-color:rgba(64,100,126,0.3)">
                    <p class="text-2xl mb-2">{{ $item['icon'] }}</p>
                    <p class="text-gray-400 text-xs uppercase tracking-wide">{{ $item['label'] }}</p>
                    <p class="text-white font-semibold mt-0.5">{{ $item['val'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- LOKASI --}}
@if($event?->maps_embed || $event?->maps_url || $event?->lokasi)
<section id="lokasi" class="py-24" style="background:#1a2e40">
    <div class="max-w-6xl mx-auto px-4">
        <div class="text-center mb-10">
            <p class="text-sm font-semibold uppercase tracking-widest mb-2" style="color:#7B91A1">Lokasi Acara</p>
            <h2 class="text-3xl font-bold text-white">{{ $event->lokasi ?? 'Venue' }}</h2>
        </div>
        @if($event->maps_embed && $event->maps_url)
        <a href="{{ $event->maps_url }}" target="_blank" rel="noopener"
           class="block rounded-2xl overflow-hidden border border-gray-700 shadow-2xl relative group" style="height:400px">
            <div class="absolute inset-0 pointer-events-none">{!! $event->maps_embed !!}</div>
            <div class="absolute inset-0 flex items-end justify-center pb-6 bg-black/0 group-hover:bg-black/20 transition-colors">
                <span class="px-5 py-2.5 rounded-full text-white font-semibold text-sm shadow-lg opacity-90 group-hover:opacity-100 transition-opacity"
                      style="background:#D03F42">🗺️ Buka di Google Maps</span>
            </div>
        </a>
        @elseif($event->maps_embed)
        <div class="rounded-2xl overflow-hidden border border-gray-700 shadow-2xl" style="height:400px">
            {!! $event->maps_embed !!}
        </div>
        @elseif($event->maps_url)
        <a href="{{ $event->maps_url }}" target="_blank" rel="noopener"
           class="flex items-center justify-center gap-2 px-8 py-4 rounded-2xl text-white font-semibold transition-all hover:opacity-90"
           style="background:#D03F42">
            🗺️ Buka di Google Maps
        </a>
        @endif
    </div>
</section>
@endif

{{-- FOOTER --}}
<footer class="py-8 text-center text-sm border-t" style="background:#111c26; border-color:rgba(64,100,126,0.3); color:#7B91A1">
    <p>© {{ date('Y') }} Dharma Group — {{ $event?->nama ?? 'Konvensi Improvement Dharma' }}</p>
    <p class="mt-1 text-xs">Powered by Sankei Dharma Indonesia</p>
</footer>

<script>
function landing() {
    return {
        currentSlide: 0,
        totalSlides: {{ $event?->slides->count() ?? 0 }},
        init() {
            if (this.totalSlides > 1) {
                setInterval(() => {
                    this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
                }, 5000);
            }
        }
    }
}
</script>
</body>
</html>
