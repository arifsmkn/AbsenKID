<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Check-in</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { background: #1a2e40; }
        @keyframes spin-slow { to { transform: rotate(360deg); } }
        .spin-slow { animation: spin-slow 20s linear infinite; }
        @keyframes popIn {
            0%   { opacity: 0; transform: scale(0.7); }
            60%  { transform: scale(1.08); }
            100% { opacity: 1; transform: scale(1); }
        }
        .pop-in { animation: popIn 0.5s cubic-bezier(0.34,1.56,0.64,1) both; }
        @keyframes fadeInUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
        .fade-up { animation: fadeInUp 0.6s ease 0.15s both; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center text-white px-4 relative overflow-hidden">

{{-- Dekorasi --}}
<div class="fixed inset-0 pointer-events-none overflow-hidden" style="z-index:0">
    <div class="absolute inset-0" style="background: radial-gradient(circle at 20% 20%, rgba(36,76,107,0.55), transparent 55%), radial-gradient(circle at 85% 80%, rgba(208,63,66,0.18), transparent 50%)"></div>
    <div class="w-96 h-96 rounded-full blur-3xl absolute -top-24 -left-24 spin-slow opacity-20" style="background:#244C6B"></div>
    <div class="w-96 h-96 rounded-full blur-3xl absolute -bottom-24 -right-24 spin-slow opacity-15" style="background:#D03F42; animation-direction:reverse"></div>
</div>

<div class="max-w-md w-full text-center relative z-10">

    <img src="{{ asset('images/dharma-group.png') }}" class="h-9 w-auto object-contain mx-auto mb-6"
         style="filter: drop-shadow(0 1px 4px rgba(255,255,255,0.6)) drop-shadow(0 0 10px rgba(255,255,255,0.3))" alt="Dharma Group">

    @if($status === 'success')
        <div class="mb-6 pop-in">
            <div class="w-24 h-24 mx-auto rounded-full flex items-center justify-center text-5xl mb-6"
                 style="background:rgba(34,197,94,0.15); border:4px solid #22c55e">✅</div>
            <h1 class="text-3xl font-bold text-green-400 mb-2">Selamat Datang!</h1>
            <p class="text-blue-200/70">{{ $message }}</p>
        </div>
        <div class="rounded-2xl p-6 mb-6 text-left space-y-3 fade-up"
             style="background:rgba(36,76,107,0.3); backdrop-filter:blur(12px); border:1px solid rgba(255,255,255,0.12)">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl font-bold shrink-0" style="background:#D03F42">
                    {{ strtoupper(substr($employee->nama, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="font-bold text-xl truncate">{{ $employee->nama }}</p>
                    <p class="text-blue-300/70 text-sm">{{ $employee->npk }}</p>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-3 text-sm">
                <div class="rounded-lg p-3" style="background:rgba(255,255,255,0.05)">
                    <p class="text-blue-300/60 text-xs">SubCo</p>
                    <p class="font-semibold truncate">{{ $employee->subco }}</p>
                </div>
            </div>
            <div class="rounded-lg p-3 text-sm text-green-300 text-center" style="background:rgba(34,197,94,0.1); border:1px solid rgba(34,197,94,0.25)">
                🕐 Check-in: {{ now()->timezone('Asia/Jakarta')->format('H:i:s') }} WIB
            </div>
        </div>
        <p class="text-xs text-white/30 mb-2" id="countdown-text">Kembali ke layar scan dalam 3 detik...</p>

    @elseif($status === 'duplicate')
        <div class="w-24 h-24 mx-auto rounded-full flex items-center justify-center text-5xl mb-6"
             style="background:rgba(234,179,8,0.15); border:4px solid #eab308">⚠️</div>
        <h1 class="text-2xl font-bold text-yellow-400 mb-2">Sudah Check-in</h1>
        <p class="text-blue-200/70 mb-4">{{ $message }}</p>
        @if($employee)
        <div class="rounded-2xl p-4 mb-6" style="background:rgba(36,76,107,0.3); border:1px solid rgba(255,255,255,0.12)">
            <p class="font-bold">{{ $employee->nama }}</p>
            <p class="text-blue-300/70 text-sm">{{ $employee->subco }} · {{ $employee->npk }}</p>
        </div>
        @endif

    @else
        <div class="w-24 h-24 mx-auto rounded-full flex items-center justify-center text-5xl mb-6"
             style="background:rgba(208,63,66,0.15); border:4px solid #D03F42">❌</div>
        <h1 class="text-2xl font-bold mb-2" style="color:#ef9899">QR Code Tidak Valid</h1>
        <p class="text-blue-200/70 mb-6">{{ $message }}</p>
    @endif

    <a href="{{ route('scan.index') }}" class="inline-flex items-center justify-center w-full px-5 py-3 rounded-xl font-semibold text-white transition-colors"
       style="background:#D03F42">
        ← Scan Lagi
    </a>
</div>

<script>
    // Auto kembali ke scan setelah 3 detik (hanya jika success)
    @if($status === 'success')
    let secondsLeft = 3;
    const countdownEl = document.getElementById('countdown-text');
    const timer = setInterval(() => {
        secondsLeft--;
        if (countdownEl) countdownEl.textContent = `Kembali ke layar scan dalam ${secondsLeft} detik...`;
        if (secondsLeft <= 0) {
            clearInterval(timer);
            window.location.href = '{{ route('scan.index') }}';
        }
    }, 1000);
    @endif
</script>
</body>
</html>
