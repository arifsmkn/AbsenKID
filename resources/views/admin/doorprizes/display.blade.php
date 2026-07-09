<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doorprize Display — {{ $event?->nama ?? 'Konvensi Improvement Dharma' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; overflow: hidden; }
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: #0d1e2d;
            color: #fff;
        }

        /* ── Background states ── */
        .bg-idle    { background: radial-gradient(ellipse at 50% 30%, #1a2e40 0%, #0d1e2d 70%); }
        .bg-spinning{ background: radial-gradient(ellipse at 50% 30%, #1a3655 0%, #0d1e2d 70%); }
        .bg-winner  { background: radial-gradient(ellipse at 50% 30%, #1e3a28 0%, #0d1e2d 70%); }
        .bg-winner-utama{ background: radial-gradient(ellipse at 50% 30%, #2e3744 0%, #0d1e2d 70%); }
        .bg-winner-gp{ background: radial-gradient(ellipse at 50% 30%, #3a2010 0%, #0d1e2d 70%); }

        /* ── Scanline ── */
        body::after {
            content: '';
            position: fixed; inset: 0;
            background: repeating-linear-gradient(0deg, transparent, transparent 2px, rgba(0,0,0,0.04) 2px, rgba(0,0,0,0.04) 4px);
            pointer-events: none; z-index: 999;
        }

        /* ── NPK Drum ── */
        .drum-text {
            font-family: 'Courier New', monospace;
            font-weight: 900;
            letter-spacing: 0.2em;
            line-height: 1;
            font-size: clamp(3.5rem, 9vw, 9rem);
            white-space: nowrap;
            display: block;
            text-align: center;
            width: 100%;
        }
        @keyframes glowPulse {
            0%,100% { text-shadow: 0 0 20px currentColor; }
            50%     { text-shadow: 0 0 50px currentColor, 0 0 10px #fff; }
        }
        .drum-glow { animation: glowPulse 0.25s ease-in-out infinite; }

        @keyframes tickSlide {
            0%   { transform: translateY(20px); opacity: 0; }
            20%  { transform: translateY(0);    opacity: 1; }
            80%  { transform: translateY(0);    opacity: 1; }
            100% { transform: translateY(-20px); opacity: 0; }
        }
        .drum-tick { animation: tickSlide 0.14s ease-in-out forwards; }

        /* ── Winner reveal ── */
        @keyframes winPop {
            0%   { opacity: 0; transform: scale(0.75) translateY(30px); }
            65%  { transform: scale(1.04) translateY(-3px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }
        .win-pop { animation: winPop 0.55s cubic-bezier(0.34,1.56,0.64,1) both; }

        /* ── Prize image entrance ── */
        @keyframes imgPop {
            0%  { opacity:0; transform: scale(0.8); }
            100%{ opacity:1; transform: scale(1); }
        }
        .img-appear { animation: imgPop 0.4s ease-out both; }

        /* ── Multi-Spin grid ── */
        .multi-grid {
            display: grid;
            gap: clamp(6px, 1vw, 14px);
            align-content: center;
            align-items: stretch;
            height: 100%;
        }
        .multi-slot {
            background: #05111b;
            border: 2px solid rgba(36,76,107,0.5);
            min-height: clamp(80px, 18vh, 160px);
            transition: border-color 0.2s, background 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .multi-slot-spinning { border-color: rgba(167,139,250,0.6); background: rgba(124,58,237,0.06); }
        .multi-slot-winner   { border-color: rgba(34,197,94,0.6); background: rgba(34,197,94,0.06); }
        .multi-drum {
            letter-spacing: 0.08em;
            font-size: clamp(0.85rem, 1.6vw, 1.6rem);
            white-space: nowrap;
        }

        /* ── Per-slot mini-odometer ── */
        .slot-odo-wrap {
            display: flex;
            gap: clamp(2px, 0.25vw, 4px);
            justify-content: center;
            align-items: center;
            margin: 2px 0;
        }
        .slot-odo-col {
            position: relative;
            overflow: hidden;
            width:  clamp(13px, 1.9vw, 26px);
            height: clamp(17px, 2.5vw, 32px);
            border-radius: 3px;
            background: #030c14;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: inset 0 2px 6px rgba(0,0,0,0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: border-color 0.3s;
        }
        .slot-odo-col::before, .slot-odo-col::after {
            content: ''; position: absolute; left: 0; right: 0; height: 25%; z-index: 2; pointer-events: none;
        }
        .slot-odo-col::before { top: 0;    background: linear-gradient(to bottom, rgba(3,12,20,0.9), transparent); }
        .slot-odo-col::after  { bottom: 0; background: linear-gradient(to top,   rgba(3,12,20,0.9), transparent); }
        .slot-odo-char {
            font-family: 'Courier New', monospace;
            font-weight: 900;
            font-size: clamp(0.6rem, 1.15vw, 1.05rem);
            line-height: 1;
            display: block;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .slot-odo-rolling { animation: odo-roll-tv 0.09s ease-out forwards; }
        .slot-odo-landing { animation: odo-land-tv 0.52s cubic-bezier(0.34,1.56,0.64,1) forwards; }

        /* ── Confetti ── */
        .confetti-wrap { position:fixed; inset:0; pointer-events:none; overflow:hidden; z-index: 50; }
        @keyframes fall {
            0%   { transform: translateY(-80px) rotate(0deg); opacity:1; }
            100% { transform: translateY(110vh) rotate(540deg); opacity:0; }
        }

        /* ── Odometer / KM Counter (TV size) ── */
        .odo-wrap-tv {
            display: flex;
            gap: clamp(4px, 0.8vw, 10px);
            justify-content: center;
            align-items: center;
        }
        .odo-col-tv {
            position: relative;
            overflow: hidden;
            width: clamp(68px, 10.5vw, 128px);
            height: clamp(84px, 13vw, 156px);
            border-radius: 12px;
            background: #05111b;
            border: 2px solid rgba(255,255,255,0.08);
            box-shadow: inset 0 6px 16px rgba(0,0,0,0.85);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: border-color 0.25s;
        }
        .odo-col-tv::before, .odo-col-tv::after {
            content: '';
            position: absolute;
            left: 0; right: 0;
            height: 30%;
            z-index: 2;
            pointer-events: none;
        }
        .odo-col-tv::before { top: 0;    background: linear-gradient(to bottom, rgba(5,17,27,0.95), transparent); }
        .odo-col-tv::after  { bottom: 0; background: linear-gradient(to top,   rgba(5,17,27,0.95), transparent); }
        .odo-char-tv {
            font-family: 'Courier New', monospace;
            font-weight: 900;
            font-size: clamp(3.2rem, 7.5vw, 8rem);
            line-height: 1;
            display: block;
            text-align: center;
            position: relative;
            z-index: 1;
            user-select: none;
        }
        @keyframes odo-roll-tv {
            0%   { transform: translateY(-100%); opacity: 0.1; }
            50%  { opacity: 1; }
            100% { transform: translateY(0);     opacity: 1;   }
        }
        @keyframes odo-land-tv {
            0%   { transform: translateY(-100%); opacity: 0.2; }
            52%  { transform: translateY(11%);  }
            70%  { transform: translateY(-6%);  }
            85%  { transform: translateY(3%);   }
            94%  { transform: translateY(-1%);  }
            100% { transform: translateY(0);    opacity: 1; }
        }
        .odo-rolling-tv { animation: odo-roll-tv 0.08s ease-out forwards; }
        .odo-landing-tv { animation: odo-land-tv 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
    </style>
</head>
<body x-data="displayApp()" :class="bgClass()" class="flex flex-col h-screen">

{{-- Confetti (grand prize) --}}
<div class="confetti-wrap" x-show="state==='winner' && doorprize?.type==='grand_prize'" x-cloak>
    <template x-for="n in 70" :key="n">
        <div :style="`
            position:absolute;
            left:${(n*7.3)%100}%;
            width:${8+n%7}px; height:${12+n%9}px;
            border-radius:2px;
            background:hsl(${n*37%360},85%,60%);
            animation: fall ${2.5+n%3}s linear ${(n*0.07)%3}s infinite;
        `"></div>
    </template>
</div>

{{-- ── HEADER ── --}}
<header class="shrink-0 flex items-center justify-between px-8 py-3 border-b"
        style="border-color:rgba(64,100,126,0.25); background:rgba(13,30,45,0.7); backdrop-filter:blur(8px);">
    <img src="{{ asset('images/dharma-group.png') }}"
         class="h-8 w-auto object-contain"
         style="filter: drop-shadow(0 1px 4px rgba(255,255,255,0.7)) drop-shadow(0 0 10px rgba(255,255,255,0.4))"
         alt="Dharma Group">
    <p class="text-xs uppercase tracking-widest" style="color:#7B91A1">
        {{ $event?->nama ?? 'Konvensi Improvement Dharma' }}
    </p>
    <div class="flex items-center gap-2">
        <span class="w-2.5 h-2.5 rounded-full"
              :class="(state==='spinning'||state==='settling') ? 'bg-blue-400 animate-pulse' : (state==='winner' ? 'bg-green-400' : 'bg-gray-600')"></span>
        <span class="text-xs" style="color:#7B91A1"
              x-text="(state==='spinning'||state==='settling') ? 'LIVE' : (state==='winner' ? 'RESULT' : 'STANDBY')"></span>
    </div>
</header>

{{-- ── MAIN ── --}}
<main class="flex-1 flex flex-col items-center justify-center overflow-hidden relative" style="min-height:0">

    {{-- ══ IDLE ══ --}}
    <div x-show="!isMulti && state==='idle'" class="text-center">
        <div class="text-8xl mb-6 opacity-20">🎲</div>
        <p class="text-2xl font-light tracking-widest uppercase" style="color:rgba(123,145,161,0.5)">Menunggu Undian…</p>
    </div>

    {{-- ══ SPINNING / SETTLING ══ --}}
    <div x-show="!isMulti && (state==='spinning' || state==='settling')" x-cloak
         class="w-full h-full flex flex-col items-center justify-center gap-3 px-8 py-4">

        {{-- 1. LABEL DOORPRIZE / GRAND PRIZE --}}
        <p class="font-black uppercase tracking-[0.25em] leading-none text-center"
           :class="typeTheme().labelClass"
           style="font-size:clamp(1.6rem,3vw,2.8rem)"
           x-text="typeTheme().label"></p>

        {{-- 2. GAMBAR HADIAH --}}
        <div class="flex items-center justify-center" style="height:20vh">
            <template x-if="doorprize?.gambar">
                <img :src="doorprize.gambar" class="h-full w-auto object-contain rounded-2xl img-appear"
                     style="max-width:32vw; border:2px solid rgba(123,145,161,0.15); background:rgba(255,255,255,0.04); padding:10px;"
                     alt="Hadiah">
            </template>
            <template x-if="!doorprize?.gambar">
                <div class="text-9xl opacity-20" x-text="typeTheme().emptyEmoji"></div>
            </template>
        </div>

        {{-- 3. NAMA HADIAH --}}
        <p class="font-black text-white text-center leading-none"
           style="font-size:clamp(2.8rem,6vw,6rem)"
           x-text="doorprize?.nama ?? '—'"></p>

        {{-- Divider --}}
        <div class="w-full max-w-4xl h-px" style="background:linear-gradient(to right,transparent,rgba(64,100,126,0.4),transparent)"></div>

        {{-- 4. ODOMETER DRUM — 8 kolom digit --}}
        <div class="relative rounded-2xl overflow-hidden py-3 px-6"
             :style="'background:#05111b; border:2px solid ' + (state==='settling' ? '#22c55e' : typeTheme().borderColor) + '; box-shadow:0 0 40px ' + typeTheme().drumGlow">
            <div class="absolute inset-0 pointer-events-none"
                 :style="'background:radial-gradient(ellipse at center,' + typeTheme().drumGlow + ' 0%,transparent 65%)'"></div>
            <div class="odo-wrap-tv relative z-10">
                <template x-for="slot in _odoSlots" :key="slot.k">
                    <div class="odo-col-tv"
                         :style="'border-color:' + (state==='settling' ? '#22c55e' : (state==='spinning' ? typeTheme().borderColor : 'rgba(255,255,255,0.06)'))">
                        <span class="odo-char-tv"
                              :class="slot.landing ? 'odo-landing-tv' : (state==='spinning' ? 'odo-rolling-tv' : '')"
                              :style="state==='settling' ? 'color:#22c55e' : ('color:' + typeTheme().digitColor)"
                              x-text="slot.val"></span>
                    </div>
                </template>
            </div>
        </div>

        {{-- 5. Status label --}}
        <p class="text-xs tracking-[0.3em] uppercase animate-pulse" style="color:rgba(123,145,161,0.35)"
           x-show="state==='spinning'">
            ● &nbsp; Sedang Mengacak…
        </p>
        <p class="text-sm tracking-[0.2em] uppercase font-semibold" style="color:#22c55e"
           x-show="state==='settling'">
            ● &nbsp; Mengunci Hasil…
        </p>
    </div>

    {{-- ══ WINNER ══ — layout centered, nama tidak terpotong --}}
    <div x-show="!isMulti && state==='winner'" x-cloak
         class="win-pop w-full h-full flex flex-col items-center justify-center gap-4 px-12 text-center">

        {{-- Badge tipe --}}
        <div class="inline-flex items-center gap-2 px-5 py-1.5 rounded-full text-sm font-bold uppercase tracking-widest border"
             :class="typeTheme().badgeClass"
             x-text="typeTheme().badgeText">
        </div>

        {{-- Gambar hadiah --}}
        <template x-if="doorprize?.gambar">
            <img :src="doorprize.gambar"
                 class="object-contain rounded-2xl img-appear"
                 style="height:22vh; max-width:30vw; border:2px solid rgba(123,145,161,0.2); background:rgba(255,255,255,0.04); padding:10px;"
                 alt="Hadiah">
        </template>

        {{-- NPK --}}
        <p class="font-mono tracking-[0.18em]" style="color:rgba(123,145,161,0.5); font-size:1.1rem"
           x-text="winner?.npk"></p>

        {{-- NAMA — penuh, tidak terpotong, auto wrap jika panjang --}}
        <p class="font-black text-white leading-tight"
           style="font-size:clamp(2.2rem,5.5vw,5rem); word-break:break-word; overflow-wrap:anywhere; max-width:90vw"
           x-text="winner?.nama"></p>

        {{-- SubCo --}}
        <p style="color:#7B91A1; font-size:clamp(1rem,2vw,1.4rem)"
           x-text="winner?.subco"></p>

        {{-- Hadiah badge --}}
        <div class="inline-flex items-center gap-3 px-6 py-2.5 rounded-2xl border" :class="typeTheme().prizeBadgeClass">
            <span class="text-2xl" x-text="typeTheme().emptyEmoji"></span>
            <span class="font-bold"
                  style="font-size:clamp(1.2rem,2.5vw,2rem)"
                  :class="typeTheme().prizeTextClass"
                  x-text="winner?.hadiah"></span>
        </div>
    </div>

    {{-- ══ MULTI-SPIN — banyak kolom sekaligus ══ --}}
    <div x-show="isMulti" x-cloak class="w-full h-full flex flex-col items-center gap-3 px-6 py-3 overflow-hidden">

        {{-- Banner --}}
        <template x-if="multiBanner">
            <img :src="multiBanner" class="rounded-2xl object-contain img-appear shrink-0"
                 style="max-height:18vh; max-width:60vw; border:2px solid rgba(123,145,161,0.2); background:rgba(255,255,255,0.04); padding:8px;"
                 alt="Banner">
        </template>

        <p class="font-black uppercase tracking-[0.25em] text-center shrink-0"
           style="font-size:clamp(1.2rem,2.4vw,2rem); color:#a78bfa">
        SPIN DOORPRIZE <span x-text="multiSlots.filter(s => s.state === 'winner').length"></span> / <span x-text="multiSlots.length"></span> KONVENSI IMPROVEMENT DHARMA 31
        </p>

        {{-- Grid kolom --}}
        <div class="multi-grid flex-1 w-full overflow-y-auto"
             :style="'grid-template-columns: repeat(' + multiCols() + ', minmax(0,1fr))'">
            <template x-for="slot in multiSlots" :key="slot.id">
                <div class="multi-slot rounded-xl flex flex-col items-center justify-center text-center p-2"
                     :class="slot.state === 'winner' ? 'multi-slot-winner' : (slot.state === 'spinning' ? 'multi-slot-spinning' : '')">
                    <template x-if="slot.doorprize?.gambar">
                        <img :src="slot.doorprize.gambar" class="object-contain rounded-lg mb-1" style="height:14%; max-height:60px">
                    </template>
                    <p class="font-bold uppercase tracking-wide truncate w-full"
                       :style="'font-size:clamp(0.55rem,1vw,0.85rem); color:' + (slot.state === 'winner' ? '#22c55e' : '#7B91A1')"
                       x-text="slot.doorprize?.nama"></p>
                    {{-- Spinning: NPK cycling text --}}
                    <template x-if="slot.state !== 'winner' && !_slotSettling[slot.id]">
                        <p class="multi-drum font-mono font-black drum-glow"
                           style="color:#a78bfa"
                           x-text="slot.state === 'spinning' ? multiDrumNpk(slot) : '--------'"></p>
                    </template>
                    {{-- Settling / Settled: per-digit mini-odometer --}}
                    <template x-if="slot.state === 'winner' || _slotSettling[slot.id]">
                        <div class="slot-odo-wrap">
                            <template x-for="dg in (_slotOdo[slot.id] ?? [])" :key="dg.k">
                                <div class="slot-odo-col"
                                     :style="'border-color:' + (dg.state === 'settled' ? 'rgba(34,197,94,0.45)' : 'rgba(167,139,250,0.3)')">
                                    <span class="slot-odo-char"
                                          :class="dg.state === 'landing' ? 'slot-odo-landing' : (dg.state === 'rolling' ? 'slot-odo-rolling' : '')"
                                          :style="'color:' + (dg.state === 'settled' ? '#22c55e' : '#a78bfa')"
                                          x-text="dg.val">
                                    </span>
                                </div>
                            </template>
                        </div>
                    </template>
                    {{-- Nama pemenang muncul setelah semua digit selesai --}}
                    <template x-if="slot.state === 'winner' && !_slotSettling[slot.id]">
                        <div class="win-pop w-full">
                            <p class="font-black text-white truncate w-full" style="font-size:clamp(0.7rem,1.3vw,1.1rem)" x-text="slot.winner.nama"></p>
                            <p class="truncate w-full" style="font-size:clamp(0.55rem,0.9vw,0.8rem); color:#7B91A1" x-text="slot.winner.subco"></p>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>

</main>

{{-- ── FOOTER ── --}}
<footer class="shrink-0 text-center py-2.5 border-t" style="border-color:rgba(64,100,126,0.2)">
    <p class="text-xs tracking-widest uppercase" style="color:rgba(123,145,161,0.3)">
        {{ $event?->nama ?? 'Konvensi Improvement Dharma' }} · Dharma Group
    </p>
</footer>

<script>
function displayApp() {
    return {
        state: 'idle',
        doorprize: null,
        winner: null,
        samplePool: [],
        _poolIdx: 0,
        _lastUpdated: -1,
        _localSettling: false,

        // ── Odometer ──
        _odoSlots: [],
        _odoKeyCounter: 100,
        _odoSpinInt: null,

        // ── Multi-Spin (2-50 kolom) ──
        isMulti: false,
        multiSlots: [],
        multiBanner: null,
        _multiTick: 0,
        _multiInterval: null,
        _prevMultiStates: {},
        _slotOdo: {},
        _slotOdoKey: 50000,
        _slotSettling: {},

        multiCols() {
            const n = this.multiSlots.length;
            if (n <= 2)  return n || 1;
            if (n <= 4)  return 2;
            if (n <= 6)  return 3;
            if (n <= 9)  return 3;
            if (n <= 12) return 4;
            if (n <= 20) return 5;
            if (n <= 30) return 6;
            if (n <= 42) return 7;
            return 8;
        },
        multiDrumNpk(slot) {
            if (!this.samplePool.length) return '------';
            return this.samplePool[(this._multiTick + slot.id) % this.samplePool.length];
        },

        _settleSlotOdo(slotId, targetNpk) {
            const digits = targetNpk.replace(/\D/g, '').padStart(8, '0').split('');
            this._slotSettling[slotId] = true;
            // Inisialisasi semua digit sebagai 'rolling' (animasi masuk dari atas)
            this._slotOdo[slotId] = digits.map(d => ({ val: d, k: ++this._slotOdoKey, state: 'rolling' }));
            // Satu per satu digit "mendarat" dari kiri ke kanan
            digits.forEach((d, i) => {
                setTimeout(() => {
                    const odo = this._slotOdo[slotId];
                    if (!odo) return;
                    const next = [...odo];
                    next[i] = { val: d, k: ++this._slotOdoKey, state: 'landing' };
                    this._slotOdo[slotId] = next;
                    if (i < digits.length - 1) this.beep(440 + i * 35, 0.04, 'triangle', 0.05);
                }, i * 75 + 20);
            });
            // Semua digit selesai → tampilkan nama pemenang
            setTimeout(() => {
                const odo = this._slotOdo[slotId];
                if (odo) this._slotOdo[slotId] = odo.map(d => ({ ...d, state: 'settled' }));
                this._slotSettling[slotId] = false;
                this.beep(880, 0.07, 'sine', 0.06);
            }, digits.length * 75 + 220);
        },

        startMultiDrum() {
            if (this._multiInterval) return;
            this._multiTick = 0;
            this._multiInterval = setInterval(() => { this._multiTick++; this.playTick(); }, 90);
        },
        stopMultiDrum() {
            clearInterval(this._multiInterval);
            this._multiInterval = null;
        },

        typeThemes: {
            doorprize: {
                labelClass: 'text-blue-400', label: '🎁 DOORPRIZE', emptyEmoji: '🎁',
                drumGlow: 'rgba(64,100,126,0.13)',
                borderColor: 'rgba(96,165,250,0.35)',
                digitColor: '#60a5fa',
                badgeClass: 'bg-green-500/15 border-green-500/40 text-green-300', badgeText: '🎉  SELAMAT!',
                prizeBadgeClass: 'bg-blue-500/10 border-blue-500/20', prizeTextClass: 'text-blue-300',
                bgClass: 'bg-winner',
            },
            doorprize_utama: {
                labelClass: 'text-slate-300', label: '🥈 DOORPRIZE UTAMA', emptyEmoji: '🥈',
                drumGlow: 'rgba(148,163,184,0.16)',
                borderColor: 'rgba(148,163,184,0.35)',
                digitColor: '#94a3b8',
                badgeClass: 'bg-slate-400/15 border-slate-400/40 text-slate-200', badgeText: '🥈  DOORPRIZE UTAMA WINNER!',
                prizeBadgeClass: 'bg-slate-400/10 border-slate-400/30', prizeTextClass: 'text-slate-200',
                bgClass: 'bg-winner-utama',
            },
            grand_prize: {
                labelClass: 'text-yellow-400', label: '🏆 GRAND PRIZE', emptyEmoji: '🏆',
                drumGlow: 'rgba(234,179,8,0.1)',
                borderColor: 'rgba(234,179,8,0.35)',
                digitColor: '#fbbf24',
                badgeClass: 'bg-yellow-500/15 border-yellow-500/40 text-yellow-300', badgeText: '🏆  GRAND PRIZE WINNER!',
                prizeBadgeClass: 'bg-yellow-500/15 border-yellow-500/30', prizeTextClass: 'text-yellow-300',
                bgClass: 'bg-winner-gp',
            },
        },
        typeTheme() { return this.typeThemes[this.doorprize?.type] ?? this.typeThemes.doorprize; },

        // ── Efek suara ──
        _audioCtx: null,
        _ensureAudio() {
            if (!this._audioCtx) {
                const AC = window.AudioContext || window.webkitAudioContext;
                this._audioCtx = new AC();
            }
            if (this._audioCtx.state === 'suspended') this._audioCtx.resume();
            return this._audioCtx;
        },
        beep(freq, duration = 0.05, type = 'sine', vol = 0.12) {
            try {
                const ctx = this._ensureAudio();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = type;
                osc.frequency.value = freq;
                gain.gain.setValueAtTime(vol, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + duration);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + duration);
            } catch (e) {}
        },
        playTick() { this.beep(880, 0.045, 'square', 0.05); },
        playWin() {
            [523.25, 659.25, 783.99, 1046.5].forEach((f, i) => {
                setTimeout(() => this.beep(f, 0.18, 'triangle', 0.12), i * 110);
            });
        },

        // ── Odometer helpers ──
        _resetOdo() {
            this._odoSlots = Array.from({length: 8}, () => ({
                val: '_', k: ++this._odoKeyCounter, landing: false
            }));
        },
        _displaySpinOdo() {
            clearInterval(this._odoSpinInt);
            let tick = 0;
            this._odoSpinInt = setInterval(() => {
                tick++;
                if (tick % 2 === 0) this.playTick();
                this._odoSlots = this._odoSlots.map((slot, i) => {
                    const every = 8 - i;
                    if (tick % every === 0) {
                        return { val: String(Math.floor(Math.random() * 10)), k: ++this._odoKeyCounter, landing: false };
                    }
                    return slot;
                });
            }, 80);
        },
        _displaySettleOdo(targetNpk, onDone) {
            clearInterval(this._odoSpinInt);
            const digits = targetNpk.replace(/\D/g, '').padStart(8, '0').split('');
            digits.forEach((d, i) => {
                setTimeout(() => {
                    this._odoSlots = this._odoSlots.map((slot, j) =>
                        j === i ? { val: d, k: ++this._odoKeyCounter, landing: true } : slot
                    );
                    if (i < digits.length - 1) this.beep(440 + i * 40, 0.1, 'triangle', 0.08);
                }, i * 110 + 80);
            });
            if (onDone) setTimeout(onDone, digits.length * 110 + 220);
        },

        init() {
            const unlock = () => this._ensureAudio();
            document.addEventListener('click', unlock, { once: true });
            document.addEventListener('keydown', unlock, { once: true });
            this._odoKeyCounter = Date.now();
            this._resetOdo();
            this.poll();
            setInterval(() => this.poll(), 400);
            // Auto-reload halaman saat idle 5 menit — pastikan JS selalu fresh
            setInterval(() => {
                if (this.state === 'idle' && !this.isMulti) location.reload();
            }, 5 * 60 * 1000);
        },

        async poll() {
            if (this._localSettling) return;
            try {
                const res  = await fetch('{{ route('doorprizes.displayStatus') }}?_=' + Date.now());
                const data = await res.json();

                // Multi-spin: always apply server data (no skip) — single-spin: skip if same version
                const _newVer = (data.updated_at !== this._lastUpdated);
                if (!_newVer && data.mode !== 'multi') return;
                if (_newVer) this._lastUpdated = data.updated_at;

                const prev      = this.state;
                const prevMulti = this.isMulti;
                this.isMulti    = (data.mode === 'multi');

                if (this.isMulti) {
                    clearInterval(this._odoSpinInt);
                    if (!prevMulti) { this._resetOdo(); this.doorprize = null; this.winner = null; }

                    this.multiBanner = data.banner_image ?? null;
                    const newSlots   = data.slots ?? [];
                    this.samplePool  = (data.sample_npk && data.sample_npk.length) ? data.sample_npk : ['------'];
                    this.state       = data.state ?? 'idle';

                    newSlots.forEach(s => {
                        const prevState = this._prevMultiStates[s.id];
                        if (prevState && prevState !== 'winner' && s.state === 'winner' && s.winner?.npk) {
                            this._settleSlotOdo(s.id, s.winner.npk);
                        }
                        this._prevMultiStates[s.id] = s.state;
                    });
                    this.multiSlots = newSlots;

                    if (newSlots.some(s => s.state === 'spinning')) {
                        this.startMultiDrum();
                    } else {
                        this.stopMultiDrum();
                        if (this.state === 'winner' && prev !== 'winner') this.playWin();
                    }
                    return;
                }

                if (prevMulti) { this.stopMultiDrum(); this.multiSlots = []; this._prevMultiStates = {}; this._slotOdo = {}; this._slotSettling = {}; }

                this.doorprize = data.doorprize ?? null;

                if (data.state === 'spinning' && prev !== 'spinning') {
                    this.state  = 'spinning';
                    this.winner = null;
                    this.samplePool = (data.sample_npk && data.sample_npk.length)
                        ? data.sample_npk : ['--------'];
                    this._poolIdx = 0;
                    this._resetOdo();
                    this._displaySpinOdo();

                } else if (data.state === 'winner' && data.winner && prev === 'spinning') {
                    // Spin baru selesai → settle digit-by-digit dulu
                    this._localSettling = true;
                    this.state = 'settling';
                    this._displaySettleOdo(data.winner.npk, () => {
                        this._localSettling = false;
                        this.state  = 'winner';
                        this.winner = data.winner;
                        this.playWin();
                    });

                } else if (data.state === 'winner' && data.winner && prev !== 'winner' && prev !== 'settling') {
                    // Winner sudah ada saat halaman dibuka (reload setelah spin)
                    this.state  = 'winner';
                    this.winner = data.winner;

                } else if (data.state === 'idle') {
                    clearInterval(this._odoSpinInt);
                    this._resetOdo();
                    this.state  = 'idle';
                    this.winner = null;

                } else if (data.state !== 'spinning') {
                    this.state  = data.state ?? 'idle';
                    this.winner = data.winner ?? null;
                }

            } catch (e) {
                console.warn('Display poll error:', e);
            }
        },

        bgClass() {
            if (this.isMulti) {
                if (this.state === 'spinning') return 'bg-spinning';
                if (this.state === 'winner')   return 'bg-winner-utama';
                return 'bg-idle';
            }
            if (this.state === 'spinning' || this.state === 'settling') return 'bg-spinning';
            if (this.state === 'winner') return this.typeTheme().bgClass;
            return 'bg-idle';
        },
    }
}
</script>
</body>
</html>
