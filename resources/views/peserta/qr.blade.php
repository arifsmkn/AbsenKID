<!DOCTYPE html>
@php
    $theme = $event?->theme_config ?? [];
    $themePrimary = $theme['primary_color'] ?? '#244C6B';
    $themeSecondary = $theme['secondary_color'] ?? '#40647E';
@endphp
<html lang="id" style="--ev-primary: {{ $themePrimary }}; --ev-secondary: {{ $themeSecondary }};">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Undangan — {{ $employee->nama }}</title>
    @vite(['resources/css/app.css'])
    <style>
        body {
            background: linear-gradient(135deg, #1a2e40 0%, var(--ev-primary) 50%, #1a3850 100%);
            min-height: 100vh;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            #qr-card { box-shadow: none !important; }
        }
    </style>
</head>
<body class="flex flex-col items-center justify-center min-h-screen py-8 px-4 font-sans">

    {{-- Back button --}}
    <div class="w-full max-w-sm mb-4 no-print">
        <a href="{{ route('peserta.dashboard') }}"
           class="flex items-center gap-2 text-white/60 hover:text-white text-sm transition-colors">
            ← Kembali ke Dashboard
        </a>
    </div>

    {{-- KARTU UNDANGAN --}}
    <div id="qr-card" class="w-full max-w-sm bg-white rounded-3xl shadow-2xl overflow-hidden">

        {{-- Header gradient --}}
        <div class="px-6 py-5 text-white text-center"
             style="background: linear-gradient(135deg, var(--ev-primary), var(--ev-secondary))">
            <h1 class="font-bold text-base leading-tight">{{ $event->nama }}</h1>
            @if($event->tanggal)
                <p class="text-blue-200 text-xs mt-1">{{ $event->tanggal->isoFormat('dddd, D MMMM Y') }}</p>
            @endif
            @if($event->lokasi)
                <p class="text-blue-200 text-xs">📍 {{ $event->lokasi }}</p>
            @endif
        </div>

        {{-- QR Code --}}
        <div class="px-8 pt-6 pb-2 flex flex-col items-center">
            <div class="p-3 bg-white rounded-2xl shadow-inner border border-gray-100 inline-block">
                {!! $qrSvg !!}
            </div>
            <p class="text-xs text-gray-300 mt-2 font-mono text-center break-all px-2">
                {{ $invitation->qr_code }}
            </p>
        </div>

        {{-- Info Peserta --}}
        <div class="px-6 py-4 text-center border-t border-gray-100 mt-2">
            <p class="font-bold text-gray-800 text-xl leading-tight">{{ $employee->nama }}</p>
            <p class="text-gray-500 text-sm mt-0.5">{{ $employee->npk }} · {{ $employee->subco }}</p>
            <p class="text-gray-400 text-xs mt-0.5">{{ $employee->jabatan }}</p>
            <div class="mt-3">
                @if($invitation->is_confirmed)
                    <span class="inline-flex items-center gap-1 px-4 py-1 rounded-full bg-green-100 text-green-700 text-sm font-semibold">
                        ✅ Sudah Hadir
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-4 py-1 rounded-full bg-blue-100 text-blue-700 text-sm font-semibold">
                        🎫 Undangan Resmi
                    </span>
                @endif
            </div>
        </div>

        {{-- Doorprize Win (jika ada) --}}
        @if($doorprizeWin)
        <div class="mx-4 mb-4 rounded-2xl overflow-hidden"
             style="background: linear-gradient(135deg, #f59e0b, #ef4444)">
            <div class="p-4 flex items-center gap-3">
                @if($doorprizeWin->doorprize?->gambar)
                    <img src="{{ $doorprizeWin->doorprize->gambar_url }}"
                         class="h-14 w-14 object-contain rounded-xl bg-white/20 p-1 shrink-0"
                         crossorigin="anonymous" alt="Hadiah">
                @else
                    <div class="text-4xl shrink-0">🎁</div>
                @endif
                <div>
                    <p class="text-white font-bold text-xs uppercase tracking-wide opacity-80">🏆 Menang Doorprize!</p>
                    <p class="text-white font-bold text-base leading-tight">{{ $doorprizeWin->doorprize?->nama_hadiah }}</p>
                    <p class="text-yellow-100 text-xs">Pukul {{ $doorprizeWin->won_at->format('H:i') }} WIB</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Footer --}}
        <div class="pb-4 text-center">
            <p class="text-xs text-gray-300">Scan QR ini di booth check-in</p>
        </div>
    </div>

    {{-- SATU tombol Download --}}
    <div class="w-full max-w-sm mt-5 no-print">
        <button id="btn-download"
                onclick="downloadKartu()"
                class="flex items-center justify-center gap-2 w-full py-3.5 active:scale-95 text-white font-bold rounded-2xl transition-all text-base"
                style="background:var(--ev-primary); box-shadow: 0 4px 20px rgba(36,76,107,0.5)">
            <span id="btn-icon">⬇️</span>
            <span id="btn-text">Download Kartu Undangan</span>
        </button>
    </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
async function downloadKartu() {
    const btn  = document.getElementById('btn-download');
    const icon = document.getElementById('btn-icon');
    const text = document.getElementById('btn-text');

    btn.disabled = true;
    icon.textContent = '⏳';
    text.textContent = 'Menyiapkan...';

    try {
        const card = document.getElementById('qr-card');

        // Bungkus kartu dengan background gradient agar ikut ter-download
        const cardClone = card.cloneNode(true);
        const wrapper = document.createElement('div');
        wrapper.style.cssText = 'position:fixed;top:-9999px;left:-9999px;background:linear-gradient(135deg,#1a2e40 0%,{{ $themePrimary }} 50%,#1a3850 100%);padding:28px 20px;border-radius:32px;display:inline-block;';
        wrapper.appendChild(cardClone);
        document.body.appendChild(wrapper);

        const canvas = await html2canvas(wrapper, {
            scale: 3,
            useCORS: true,
            allowTaint: true,
            backgroundColor: null,
            logging: false,
        });

        document.body.removeChild(wrapper);

        const a = document.createElement('a');
        a.download = 'undangan-{{ $employee->npk }}-{{ $event->tahun }}.png';
        a.href = canvas.toDataURL('image/png');
        a.click();

    } catch(e) {
        alert('Gagal membuat gambar. Coba screenshot manual.');
        console.error(e);
    } finally {
        btn.disabled = false;
        icon.textContent = '⬇️';
        text.textContent = 'Download Kartu Undangan';
    }
}
</script>

@include('peserta.partials.auto-logout')
</body>
</html>
