<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-in Display — {{ $event?->nama ?? 'Konvensi' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Shippori+Mincho:wght@600;800&display=swap" rel="stylesheet">
    <style>
        .font-jp { font-family: 'Shippori Mincho', serif; }
        body {
            background: #f3f4f6;
            @if($event?->wallpaper_url)
            background: linear-gradient(rgba(243,244,246,0.92), rgba(243,244,246,0.92)),
                        url('{{ $event->wallpaper_url }}') center/cover no-repeat fixed;
            @endif
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(60px) scale(0.95); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }
        @keyframes fadeOut {
            from { opacity: 1; transform: scale(1); }
            to   { opacity: 0; transform: scale(0.95); }
        }
        @keyframes pulse-glow {
            0%,100% { box-shadow: 0 0 40px rgba(34,197,94,0.3); }
            50% { box-shadow: 0 0 80px rgba(34,197,94,0.6); }
        }
        .animate-in  { animation: fadeInUp 0.6s cubic-bezier(0.34,1.56,0.64,1) forwards; }
        .animate-out { animation: fadeOut 0.4s ease-in forwards; }
        .glow-green  { animation: pulse-glow 2s ease-in-out infinite; }
        @keyframes spin-slow { to { transform: rotate(360deg); } }
        .spin-slow { animation: spin-slow 20s linear infinite; }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-12px)} }
        .float { animation: float 3s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center text-gray-800 font-sans overflow-hidden" x-data="tvDisplay()">

    {{-- Header dua logo korporat --}}
    <div class="fixed top-0 left-0 right-0 flex items-center justify-between px-8 py-5 z-20">
        <img src="{{ asset('images/dharma-group.png') }}" class="h-16 w-auto object-contain" alt="Dharma Group">
        <img src="{{ asset('images/triputra-group1.png') }}" class="h-20 w-auto object-contain" alt="Triputra Group">
    </div>

    {{-- IDLE SCREEN --}}
    <div x-show="state === 'idle'" class="text-center px-8" x-cloak>
        <div class="float">
            @if($event?->logo)
                <img src="{{ asset('storage/'.$event->logo) }}" class="h-36 w-auto object-contain mx-auto mb-8" style="filter: drop-shadow(0 8px 20px rgba(0,0,0,0.12))" alt="">
            @else
                <img src="{{ asset('images/dharma-group.png') }}" class="h-40 w-auto object-contain mx-auto mb-8" style="filter: drop-shadow(0 8px 20px rgba(0,0,0,0.12))" alt="">
            @endif
        </div>
        <h1 class="text-4xl font-extrabold text-gray-800 mb-3">{{ $event?->nama ?? 'Konvensi Improvement Dharma' }}</h1>
        @if($event?->tanggal)
            <p class="text-gray-500 text-xl mb-2">📅 {{ $event->tanggal->isoFormat('dddd, D MMMM Y') }}</p>
        @endif
        @if($event?->lokasi)
            <p class="text-gray-400 text-lg">📍 {{ $event->lokasi }}</p>
        @endif
        <div class="mt-10 flex items-center justify-center gap-3 text-gray-400">
            <div class="w-2 h-2 rounded-full bg-green-500 animate-ping"></div>
            <span class="text-sm tracking-widest uppercase">Siap Scan Check-in</span>
        </div>
    </div>

    {{-- NOTIFICATION SCREEN --}}
    <div x-show="state === 'notif'" class="text-center px-8 w-full max-w-2xl" x-cloak>
        <div class="animate-in">
            {{-- Logo event --}}
            @if($event?->logo)
                <img src="{{ asset('storage/'.$event->logo) }}" class="h-44 w-auto object-contain mx-auto mb-4" style="filter: drop-shadow(0 8px 20px rgba(0,0,0,0.12))" alt="">
            @else
                <img src="{{ asset('images/dharma-group.png') }}" class="h-32 w-auto object-contain mx-auto mb-4" style="filter: drop-shadow(0 8px 20px rgba(0,0,0,0.12))" alt="">
            @endif
            <h2 class="text-3xl font-extrabold text-gray-800 mb-5">{{ $event?->nama ?? 'Konvensi Improvement Dharma' }}</h2>

            {{-- Kartu peserta --}}
            <div class="rounded-3xl p-10 shadow-lg" style="background:#FDF5EF; border:1px solid rgba(208,63,66,0.12)">
                {{-- Nama --}}
                <h1 class="font-jp text-5xl font-extrabold leading-tight break-words mb-3" style="color:#B8333A" x-text="current.nama"></h1>

                {{-- Garis pemisah --}}
                <div class="flex items-center justify-center gap-3 mb-3">
                    <span class="h-px w-10" style="background:rgba(208,63,66,0.35)"></span>
                    <p class="font-jp text-xl tracking-[0.3em] text-gray-500" x-text="current.npk"></p>
                    <span class="h-px w-10" style="background:rgba(208,63,66,0.35)"></span>
                </div>

                {{-- SubCo --}}
                <p class="text-gray-600 text-base font-semibold tracking-wide uppercase mb-6" x-text="current.subco"></p>

                {{-- Status --}}
                <div>
                    <div class="inline-flex items-center gap-2 px-5 py-2 rounded-xl" style="background:#dcfce7">
                        <span class="text-green-700 font-bold text-sm tracking-wide">✅ CHECK-IN BERHASIL</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Dekorasi background --}}
    <div class="fixed inset-0 pointer-events-none overflow-hidden" style="z-index:-1">
        <div class="w-96 h-96 rounded-full blur-3xl absolute -top-32 -left-32 spin-slow opacity-10"
             style="background:#244C6B"></div>
        <div class="w-96 h-96 rounded-full blur-3xl absolute -bottom-32 -right-32 spin-slow opacity-10"
             style="background:#D03F42; animation-direction:reverse"></div>
    </div>

<script>
function tvDisplay() {
    return {
        state: 'idle',
        current: {},
        lastId: 0,
        queue: [],
        showing: false,

        async init() {
            // Cari id terakhir yang sudah ada SEBELUM halaman ini dibuka, supaya
            // tidak replay histori scan lama — hanya scan baru yang akan tampil.
            try {
                const res  = await fetch('/scan/tv/latest');
                const data = await res.json();
                this.lastId = data.latest_id || 0;
            } catch(e) {}

            this.poll();
            setInterval(() => this.poll(), 2000);
        },

        async poll() {
            try {
                const res  = await fetch(`/scan/tv/queue?after=${this.lastId}`);
                const data = await res.json();
                if (data.notification) {
                    this.queue.push(data.notification);
                    this.lastId = data.latest_id;
                    if (!this.showing) this.showNext();
                }
            } catch(e) {}
        },

        showNext() {
            if (this.queue.length === 0) {
                this.showing = false;
                this.state = 'idle';
                return;
            }
            this.showing = true;
            this.current = this.queue.shift();
            this.state   = 'notif';

            setTimeout(() => {
                this.state = 'idle';
                setTimeout(() => this.showNext(), 500);
            }, 6000);
        }
    }
}
</script>
</body>
</html>
