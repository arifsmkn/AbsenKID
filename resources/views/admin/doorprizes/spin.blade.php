@extends('layouts.admin')
@section('title', 'Spin Doorprize')
@section('page-title', 'Kontrol Undian Doorprize')

@push('head')
<style>
@keyframes winReveal {
    0%   { opacity: 0; transform: scale(0.85); }
    60%  { transform: scale(1.04); }
    100% { opacity: 1; transform: scale(1); }
}
.win-reveal { animation: winReveal 0.4s cubic-bezier(0.34,1.56,0.64,1) both; }

/* ── Odometer / KM Counter Drum ── */
.odo-wrap {
    display: flex;
    gap: clamp(3px, 0.7vw, 6px);
    justify-content: center;
    align-items: center;
    padding: 4px 0;
}
.odo-col {
    position: relative;
    overflow: hidden;
    width: clamp(38px, 7vw, 64px);
    height: clamp(52px, 9.5vw, 82px);
    border-radius: 10px;
    background: #05111b;
    border: 1.5px solid rgba(255,255,255,0.07);
    box-shadow: inset 0 4px 12px rgba(0,0,0,0.8), inset 0 -2px 4px rgba(255,255,255,0.02);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: border-color 0.2s;
}
/* Depth fades at top/bottom — seperti kaca penutup odometer */
.odo-col::before, .odo-col::after {
    content: '';
    position: absolute;
    left: 0; right: 0;
    height: 32%;
    z-index: 2;
    pointer-events: none;
}
.odo-col::before { top: 0;    background: linear-gradient(to bottom, rgba(5,17,27,0.92), transparent); }
.odo-col::after  { bottom: 0; background: linear-gradient(to top,   rgba(5,17,27,0.92), transparent); }

.odo-char {
    font-family: 'Courier New', monospace;
    font-weight: 900;
    font-size: clamp(1.9rem, 4.3vw, 3.5rem);
    line-height: 1;
    display: block;
    text-align: center;
    position: relative;
    z-index: 1;
    user-select: none;
}

/* Digit datang dari atas (spinning cepat) */
@keyframes odo-roll {
    0%   { transform: translateY(-100%); opacity: 0.1; }
    50%  { opacity: 1; }
    100% { transform: translateY(0);     opacity: 1;   }
}
/* Digit mendarat dengan per (spring) */
@keyframes odo-land {
    0%   { transform: translateY(-100%); opacity: 0.2; }
    52%  { transform: translateY(11%);  }
    70%  { transform: translateY(-6%);  }
    85%  { transform: translateY(3%);   }
    94%  { transform: translateY(-1%);  }
    100% { transform: translateY(0);    opacity: 1; }
}

.odo-rolling { animation: odo-roll 0.075s ease-out forwards; }
.odo-landing { animation: odo-land 0.55s cubic-bezier(0.34, 1.56, 0.64, 1) forwards; }
</style>
@endpush

@php
$typeMeta = [
    'doorprize'       => ['emoji' => '🎁', 'label' => 'Doorprize', 'color' => '#7B91A1', 'badgeBg' => 'rgba(36,76,107,0.08)', 'badgeColor' => '#40647E'],
    'doorprize_utama' => ['emoji' => '🥈', 'label' => 'Doorprize Utama', 'color' => '#64748b', 'badgeBg' => 'rgba(100,116,139,0.14)', 'badgeColor' => '#475569'],
    'grand_prize'     => ['emoji' => '🏆', 'label' => 'Grand Prize', 'color' => '#f59e0b', 'badgeBg' => 'rgba(245,158,11,0.12)', 'badgeColor' => '#d97706'],
];
@endphp
@section('content')
<div x-data="spinApp()" class="max-w-5xl mx-auto space-y-4">

    {{-- ── Top action bar ── --}}
    <div class="flex flex-wrap gap-2 items-center justify-between p-3 rounded-xl"
         style="background:rgba(36,76,107,0.08); border:1px solid rgba(36,76,107,0.15)">
        <div class="flex items-center gap-3 flex-wrap">
            {{-- Link ke kelola hadiah --}}
            <a href="{{ route('admin.doorprizes.index') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold transition-all"
               style="background:#244C6B; color:white">
                📁 Kelola Hadiah
            </a>
            <a href="{{ route('admin.doorprizes.create') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition-all"
               style="background:rgba(36,76,107,0.1); color:#244C6B; border:1px solid rgba(36,76,107,0.2)">
                + Tambah Hadiah
            </a>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs" style="color:#7B91A1">Tampilan layar:</span>
            <a href="{{ $displayUrl }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-semibold text-white transition-all"
               style="background:#D03F42">
                📺 Buka Display
            </a>
        </div>
    </div>

    {{-- ── TAB MODE ── --}}
    <div class="card p-1 flex gap-1">
        <button @click="mode='doorprize'; resetAll()"
                :class="mode==='doorprize'
                    ? 'text-white shadow'
                    : 'hover:bg-gray-100 dark:hover:bg-gray-700'"
                :style="mode==='doorprize' ? 'background:#244C6B; color:white' : 'color:#7B91A1'"
                class="flex-1 py-2.5 rounded-xl font-semibold text-sm transition-all">
            🎁 Doorprize
        </button>
        <button @click="mode='doorprize_utama'; resetAll()"
                :class="mode==='doorprize_utama' ? 'text-white shadow' : 'hover:bg-gray-100 dark:hover:bg-gray-700'"
                :style="mode==='doorprize_utama' ? 'background:linear-gradient(to right,#94a3b8,#64748b)' : 'color:#7B91A1'"
                class="flex-1 py-2.5 rounded-xl font-semibold text-sm transition-all">
            🥈 Doorprize Utama
        </button>
        <button @click="mode='grand_prize'; resetAll()"
                :class="mode==='grand_prize' ? 'text-white shadow' : 'hover:bg-gray-100 dark:hover:bg-gray-700'"
                :style="mode==='grand_prize' ? 'background:linear-gradient(to right,#f59e0b,#f97316)' : 'color:#7B91A1'"
                class="flex-1 py-2.5 rounded-xl font-semibold text-sm transition-all">
            🏆 Grand Prize
        </button>
        <button @click="mode='multi'; resetAll()"
                :class="mode==='multi' ? 'text-white shadow' : 'hover:bg-gray-100 dark:hover:bg-gray-700'"
                :style="mode==='multi' ? 'background:linear-gradient(to right,#7c3aed,#6366f1)' : 'color:#7B91A1'"
                class="flex-1 py-2.5 rounded-xl font-semibold text-sm transition-all">
            🎰 Multi-Spin
        </button>
    </div>

    {{-- ════════════════════════════════════════════
         MULTI-COLUMN SPIN (2–50 kolom sekaligus)
         ════════════════════════════════════════════ --}}
    <div x-show="mode==='multi'" x-cloak class="space-y-4">

        <div class="card" style="border-color:rgba(124,58,237,0.25)">
            <h3 class="font-bold text-base mb-1">🎰 Multi-Spin — Banyak Kolom Sekaligus</h3>
            <p class="text-xs mb-4" style="color:#7B91A1">
                Pilih hadiah dari tiap kategori &amp; atur jumlah kolom (total 2–50), lalu kocok untuk memulai sesi multi-kolom di layar display.
            </p>

            {{-- ── 3 Grup Kategori Hadiah ── --}}
            <div class="grid lg:grid-cols-3 gap-3 mb-4">

                {{-- DOORPRIZE 🎁 --}}
                @php $grpDp = $doorprizes->where('type','doorprize'); @endphp
                <div class="rounded-xl border p-3 space-y-2" style="border-color:rgba(36,76,107,0.4); background:rgba(36,76,107,0.04)">
                    <h4 class="font-bold text-xs uppercase tracking-widest flex items-center gap-1" style="color:#40647E">
                        🎁 Doorprize
                        <span class="ml-auto font-normal normal-case tracking-normal text-xs px-2 py-0.5 rounded-full"
                              style="background:rgba(36,76,107,0.12); color:#40647E"
                              x-text="multiQtyByType('doorprize') + ' kolom'"></span>
                    </h4>
                    @forelse($grpDp as $dp)
                    <div class="flex items-center gap-2 p-2 rounded-lg" style="background:rgba(36,76,107,0.07)">
                        @if($dp->gambar)
                        <img src="{{ $dp->gambar_url }}" class="h-8 w-8 object-contain rounded shrink-0" style="background:rgba(255,255,255,0.5)">
                        @else
                        <span class="text-base shrink-0">🎁</span>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-xs truncate">{{ $dp->nama_hadiah }}</p>
                            <p class="text-xs" style="color:#7B91A1">Stok: {{ $dp->jumlah }}</p>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button"
                                    @click="multiDecrement({{ $dp->id }}, @js($dp->nama_hadiah), '{{ $dp->gambar_url }}', '{{ $dp->type }}')"
                                    class="w-6 h-6 rounded font-bold text-sm leading-none" style="background:rgba(123,145,161,0.15); color:#7B91A1">−</button>
                            <input type="number" min="0" max="50"
                                   :value="multiQty({{ $dp->id }})"
                                   @change="multiSetQty({{ $dp->id }}, @js($dp->nama_hadiah), '{{ $dp->gambar_url }}', '{{ $dp->type }}', $event.target.value)"
                                   class="w-10 text-center font-black text-sm rounded border px-1 py-0.5"
                                   style="background:rgba(36,76,107,0.12);border-color:rgba(36,76,107,0.3);color:inherit">
                            <button type="button"
                                    @click="multiIncrement({{ $dp->id }}, @js($dp->nama_hadiah), '{{ $dp->gambar_url }}', '{{ $dp->type }}')"
                                    :disabled="multiTotalSlots() >= 50"
                                    class="w-6 h-6 rounded font-bold text-white text-sm leading-none disabled:opacity-40" style="background:#40647E">+</button>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-center py-4" style="color:#7B91A1">Belum ada hadiah doorprize.<br>Tambahkan di menu <strong>Hadiah</strong>.</p>
                    @endforelse
                </div>

                {{-- DOORPRIZE UTAMA 🥈 --}}
                @php $grpDu = $doorprizes->where('type','doorprize_utama'); @endphp
                <div class="rounded-xl border p-3 space-y-2" style="border-color:rgba(100,116,139,0.4); background:rgba(100,116,139,0.04)">
                    <h4 class="font-bold text-xs uppercase tracking-widest flex items-center gap-1" style="color:#64748b">
                        🥈 Doorprize Utama
                        <span class="ml-auto font-normal normal-case tracking-normal text-xs px-2 py-0.5 rounded-full"
                              style="background:rgba(100,116,139,0.12); color:#64748b"
                              x-text="multiQtyByType('doorprize_utama') + ' kolom'"></span>
                    </h4>
                    @forelse($grpDu as $dp)
                    <div class="flex items-center gap-2 p-2 rounded-lg" style="background:rgba(100,116,139,0.07)">
                        @if($dp->gambar)
                        <img src="{{ $dp->gambar_url }}" class="h-8 w-8 object-contain rounded shrink-0" style="background:rgba(255,255,255,0.5)">
                        @else
                        <span class="text-base shrink-0">🥈</span>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-xs truncate">{{ $dp->nama_hadiah }}</p>
                            <p class="text-xs" style="color:#7B91A1">Stok: {{ $dp->jumlah }}</p>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button"
                                    @click="multiDecrement({{ $dp->id }}, @js($dp->nama_hadiah), '{{ $dp->gambar_url }}', '{{ $dp->type }}')"
                                    class="w-6 h-6 rounded font-bold text-sm leading-none" style="background:rgba(123,145,161,0.15); color:#7B91A1">−</button>
                            <span class="w-5 text-center font-black text-sm" x-text="multiQty({{ $dp->id }})"></span>
                            <button type="button"
                                    @click="multiIncrement({{ $dp->id }}, @js($dp->nama_hadiah), '{{ $dp->gambar_url }}', '{{ $dp->type }}')"
                                    :disabled="multiTotalSlots() >= 50"
                                    class="w-6 h-6 rounded font-bold text-white text-sm leading-none disabled:opacity-40" style="background:#64748b">+</button>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-center py-4" style="color:#7B91A1">Belum ada hadiah doorprize utama.<br>Tambahkan di menu <strong>Hadiah</strong>.</p>
                    @endforelse
                </div>

                {{-- GRAND PRIZE 🏆 --}}
                @php $grpGp = $doorprizes->where('type','grand_prize'); @endphp
                <div class="rounded-xl border p-3 space-y-2" style="border-color:rgba(245,158,11,0.4); background:rgba(245,158,11,0.03)">
                    <h4 class="font-bold text-xs uppercase tracking-widest flex items-center gap-1" style="color:#d97706">
                        🏆 Grand Prize
                        <span class="ml-auto font-normal normal-case tracking-normal text-xs px-2 py-0.5 rounded-full"
                              style="background:rgba(245,158,11,0.12); color:#d97706"
                              x-text="multiQtyByType('grand_prize') + ' kolom'"></span>
                    </h4>
                    @forelse($grpGp as $dp)
                    <div class="flex items-center gap-2 p-2 rounded-lg" style="background:rgba(245,158,11,0.06)">
                        @if($dp->gambar)
                        <img src="{{ $dp->gambar_url }}" class="h-8 w-8 object-contain rounded shrink-0" style="background:rgba(255,255,255,0.5)">
                        @else
                        <span class="text-base shrink-0">🏆</span>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-xs truncate">{{ $dp->nama_hadiah }}</p>
                            <p class="text-xs" style="color:#7B91A1">Stok: {{ $dp->jumlah }}</p>
                        </div>
                        <div class="flex items-center gap-1 shrink-0">
                            <button type="button"
                                    @click="multiDecrement({{ $dp->id }}, @js($dp->nama_hadiah), '{{ $dp->gambar_url }}', '{{ $dp->type }}')"
                                    class="w-6 h-6 rounded font-bold text-sm leading-none" style="background:rgba(123,145,161,0.15); color:#7B91A1">−</button>
                            <span class="w-5 text-center font-black text-sm" x-text="multiQty({{ $dp->id }})"></span>
                            <button type="button"
                                    @click="multiIncrement({{ $dp->id }}, @js($dp->nama_hadiah), '{{ $dp->gambar_url }}', '{{ $dp->type }}')"
                                    :disabled="multiTotalSlots() >= 50"
                                    class="w-6 h-6 rounded font-bold text-white text-sm leading-none disabled:opacity-40" style="background:#d97706">+</button>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-center py-4" style="color:#7B91A1">Belum ada hadiah grand prize.<br>Tambahkan di menu <strong>Hadiah</strong>.</p>
                    @endforelse
                </div>
            </div>

            {{-- ── Summary bar ── --}}
            <div class="flex items-center gap-3 py-2.5 px-3 rounded-xl mb-4 text-xs flex-wrap" style="background:rgba(124,58,237,0.07); border:1px solid rgba(124,58,237,0.18)">
                <span style="color:#7B91A1">Total kolom:</span>
                <strong :style="multiTotalSlots() >= 2 && multiTotalSlots() <= 50 ? 'color:#22c55e' : 'color:#D03F42'" x-text="multiTotalSlots()"></strong>
                <span style="color:#7B91A1">/ 50</span>
                <span x-show="multiTotalSlots() >= 2" class="flex gap-3 ml-1">
                    <span x-show="multiQtyByType('doorprize') > 0" style="color:#40647E">🎁 <span x-text="multiQtyByType('doorprize')"></span></span>
                    <span x-show="multiQtyByType('doorprize_utama') > 0" style="color:#64748b">🥈 <span x-text="multiQtyByType('doorprize_utama')"></span></span>
                    <span x-show="multiQtyByType('grand_prize') > 0" style="color:#d97706">🏆 <span x-text="multiQtyByType('grand_prize')"></span></span>
                </span>
                <span x-show="multiTotalSlots() < 2" style="color:#D03F42">(minimal 2 kolom)</span>
            </div>

            {{-- ── Banner & Eksklusi Jabatan ── --}}
            <div class="grid lg:grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-widest" style="color:#7B91A1">Banner Layar (opsional)</label>
                    <input type="text" x-model="multiBanner" placeholder="URL gambar banner untuk ditampilkan di atas kolom"
                           class="input w-full mt-1 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-widest" style="color:#7B91A1">Eksklusi Jabatan</label>
                    <div class="flex flex-wrap gap-1.5 mt-1.5 max-h-20 overflow-y-auto">
                        @foreach($jabatanList as $jab)
                        <label class="flex items-center gap-1.5 px-2 py-1 rounded-lg text-xs cursor-pointer" style="background:rgba(123,145,161,0.08)">
                            <input type="checkbox" x-model="multiExcludedJabatan" value="{{ $jab }}" class="w-3.5 h-3.5 rounded" style="accent-color:#D03F42">
                            {{ $jab }}
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ── Tombol Aksi ── --}}
            <div class="flex flex-wrap gap-2">
                <button @click="multiDrawAndStart()"
                        :disabled="multiTotalSlots() < 2 || multiTotalSlots() > 50 || multiBusy || multiSession"
                        class="px-5 py-2.5 text-white font-bold rounded-xl text-sm transition-all disabled:opacity-40"
                        style="background:#7c3aed">
                    🎲 Kocok &amp; Mulai (<span x-text="multiTotalSlots()"></span> kolom)
                </button>
                <button @click="multiClearConfig()" :disabled="multiBusy || multiSession"
                        class="px-4 py-2.5 font-semibold rounded-xl text-sm transition-all disabled:opacity-40"
                        style="background:rgba(123,145,161,0.15); color:#7B91A1">
                    Bersihkan
                </button>
            </div>
        </div>

        {{-- Kontrol sesi aktif --}}
        <div class="card" x-show="multiSession" x-cloak style="border-color:rgba(124,58,237,0.3)">
            <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                <div>
                    <h3 class="font-bold text-base">🎰 Sesi Multi-Spin Aktif</h3>
                    <p class="text-xs mt-0.5" style="color:#7B91A1">
                        <span x-text="multiSlots.filter(s => s.state === 'winner').length"></span> / <span x-text="multiSlots.length"></span> kolom sudah terungkap
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button @click="multiStopAll()" :disabled="multiBusy"
                            class="px-4 py-2 text-white font-semibold rounded-xl text-sm transition-all disabled:opacity-40"
                            style="background:#D03F42">🛑 Stop Semua</button>
                    <button @click="multiSaveAll()" :disabled="multiBusy || !multiSlots.some(s => s.state === 'winner')"
                            class="px-4 py-2 text-white font-semibold rounded-xl text-sm transition-all disabled:opacity-40"
                            style="background:#22c55e">💾 Simpan Pemenang</button>
                    <button @click="multiResetAllSlots()" :disabled="multiBusy"
                            class="px-4 py-2 font-semibold rounded-xl text-sm transition-all disabled:opacity-40"
                            style="background:rgba(123,145,161,0.15); color:#7B91A1">🔄 Reset Sesi</button>
                </div>
            </div>

            {{-- Grid kolom --}}
            <div class="grid gap-3" style="grid-template-columns: repeat(auto-fill, minmax(150px, 1fr))">
                <template x-for="slot in multiSlots" :key="slot.id">
                    <div class="rounded-xl border-2 p-3 text-center transition-all"
                         :style="slot.state === 'winner'
                             ? 'border-color:#22c55e; background:rgba(34,197,94,0.06)'
                             : (slot.state === 'spinning' ? 'border-color:#7c3aed; background:rgba(124,58,237,0.06)' : 'border-color:rgba(123,145,161,0.25)')">
                        <p class="text-xs font-semibold truncate" :title="slot.doorprize.nama" x-text="slot.doorprize.nama"></p>
                        <p class="npk-drum text-base my-2"
                           :class="slot.state === 'spinning' ? 'drum-spinning' : ''"
                           :style="slot.state === 'winner' ? 'color:#22c55e' : (slot.state === 'spinning' ? 'color:#7c3aed' : 'color:rgba(123,145,161,0.4)')"
                           x-text="slot.state === 'winner' ? slot.winner.npk : (slot.state === 'spinning' ? multiDrumNpk(slot) : '_ _ _ _ _ _')"></p>
                        <template x-if="slot.state === 'winner'">
                            <div class="text-xs">
                                <p class="font-bold truncate" x-text="slot.winner.nama"></p>
                                <p class="truncate" style="color:#7B91A1" x-text="slot.winner.subco"></p>
                            </div>
                        </template>
                        <div class="flex gap-1 mt-2 justify-center flex-wrap">
                            <button @click="multiStopSlot(slot.id)" :disabled="slot.state !== 'spinning' || multiBusy"
                                    class="text-xs px-2 py-1 rounded-lg text-white disabled:opacity-30" style="background:#D03F42">Stop</button>
                            <button @click="multiResetSlot(slot.id)" :disabled="multiBusy"
                                    class="text-xs px-2 py-1 rounded-lg disabled:opacity-30" style="background:rgba(123,145,161,0.15); color:#7B91A1">Ulang</button>
                            <button @click="multiDisqualifySlot(slot.id)" x-show="slot.state === 'winner'" :disabled="multiBusy"
                                    class="text-xs px-2 py-1 rounded-lg text-white disabled:opacity-30" style="background:#7c2d2d">❌ Hangus</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-4" x-show="mode!=='multi'">

        {{-- ── KIRI: Pilih Hadiah + Filter Jabatan ── --}}
        <div class="space-y-4">

            {{-- Pilih Hadiah --}}
            <div class="card">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-xs uppercase tracking-widest" style="color:#7B91A1">Pilih Hadiah</h3>
                    <a href="{{ route('admin.doorprizes.create') }}" class="text-xs" style="color:#D03F42">+ Tambah</a>
                </div>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @forelse($doorprizes as $dp)
                    <div x-show="mode === '{{ $dp->type }}'">
                        <button
                            @click="selectPrize({{ $dp->id }}, @js($dp->nama_hadiah), '{{ $dp->gambar_url }}', {{ $dp->jumlah }}, '{{ $dp->type }}')"
                            :style="selectedPrize?.id === {{ $dp->id }}
                                ? ('border-color:' + theme().solid + '; background:' + theme().glow)
                                : ''"
                            class="w-full p-3 rounded-xl border text-left transition-all hover:bg-gray-50 dark:hover:bg-gray-700"
                            style="border-color:rgba(123,145,161,0.25)">
                            <div class="flex items-center gap-3">
                                {{-- Gambar mini --}}
                                @if($dp->gambar)
                                <img src="{{ $dp->gambar_url }}" class="h-10 w-10 object-contain rounded-lg shrink-0" style="background:rgba(255,255,255,0.5)">
                                @else
                                <span class="text-2xl shrink-0">{{ ['doorprize' => '🎁', 'doorprize_utama' => '🥈', 'grand_prize' => '🏆'][$dp->type] ?? '🎁' }}</span>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-sm truncate">{{ $dp->nama_hadiah }}</p>
                                    <p class="text-xs" style="color:#7B91A1">Qty: {{ $dp->jumlah }}</p>
                                </div>
                                <a href="{{ route('admin.doorprizes.edit', $dp) }}"
                                   onclick="event.stopPropagation()"
                                   class="text-xs px-1.5 py-0.5 rounded" style="color:#7B91A1; border:1px solid rgba(123,145,161,0.3)">Edit</a>
                            </div>
                        </button>
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <p class="text-sm" style="color:#7B91A1">Belum ada hadiah.</p>
                        <a href="{{ route('admin.doorprizes.create') }}" class="text-sm font-semibold" style="color:#D03F42">+ Tambah Hadiah</a>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Filter SubCo (khusus Doorprize Utama & Grand Prize) --}}
            <div class="card" x-show="mode==='doorprize_utama' || mode==='grand_prize'" x-cloak
                 style="border-color:rgba(245,158,11,0.3)">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-xs uppercase tracking-widest" style="color:#7B91A1">🏢 Pemenang dari SubCo</h3>
                    <div class="flex gap-2 text-xs">
                        <button @click="selectedSubcos=[]" style="color:#244C6B">Semua SubCo</button>
                    </div>
                </div>
                <p class="text-xs mb-2" style="color:#7B91A1">
                    Kosongkan = semua SubCo ikut diundi. Centang satu atau beberapa SubCo untuk membatasi undian hanya ke peserta SubCo itu
                    (mis. 1 Grand Prize diperebutkan 3 SubCo terpilih). Bisa diganti tiap kali sebelum Putar — untuk hadiah qty &gt; 1, tiap putaran boleh pilih kombinasi SubCo berbeda.
                </p>
                <div class="space-y-1.5 max-h-48 overflow-y-auto">
                    @foreach($subcoList as $sc)
                    <label class="flex items-center gap-2 cursor-pointer text-sm">
                        <input type="checkbox" x-model="selectedSubcos" value="{{ $sc }}"
                               class="w-4 h-4 rounded" style="accent-color:#f59e0b">
                        <span>{{ $sc }}</span>
                    </label>
                    @endforeach
                </div>
                @if($subcoList->isEmpty())
                    <p class="text-xs mt-2" style="color:#7B91A1">Belum ada peserta hadir.</p>
                @endif
                <p class="text-xs mt-2 pt-2" style="border-top:1px solid rgba(123,145,161,0.2); color:#7B91A1" x-show="selectedSubcos.length">
                    Terpilih: <strong style="color:#f59e0b" x-text="selectedSubcos.join(', ')"></strong>
                </p>
            </div>

            {{-- Filter Jabatan --}}
            <div class="card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-xs uppercase tracking-widest" style="color:#7B91A1">Eksklusi Jabatan</h3>
                    <div class="flex gap-2 text-xs">
                        <button @click="excludedJabatan=[]" style="color:#244C6B">Semua ikut</button>
                        <span style="color:#7B91A1">|</span>
                        <button @click="excludedJabatan=allJabatan" style="color:#D03F42">Semua tidak</button>
                    </div>
                </div>
                <p class="text-xs mb-2" style="color:#7B91A1">☑ = <strong>tidak</strong> ikut spin</p>
                <div class="space-y-1.5 max-h-48 overflow-y-auto">
                    @foreach($jabatanList as $jab)
                    <label class="flex items-center gap-2 cursor-pointer text-sm">
                        <input type="checkbox" x-model="excludedJabatan" value="{{ $jab }}"
                               class="w-4 h-4 rounded" style="accent-color:#D03F42">
                        <span>{{ $jab }}</span>
                    </label>
                    @endforeach
                    @if($jabatanList->isEmpty())
                        <p class="text-xs" style="color:#7B91A1">Belum ada peserta hadir.</p>
                    @endif
                </div>
                <p class="text-xs mt-3 pt-2" style="border-top:1px solid rgba(123,145,161,0.2); color:#7B91A1">
                    Eligible:
                    <strong class="text-green-500" x-text="filteredEligibleCount()"></strong> peserta
                    <span x-show="(mode==='doorprize_utama'||mode==='grand_prize') && selectedSubcos.length"
                          class="ml-1" style="color:#f59e0b">
                        (dari SubCo terpilih)
                    </span>
                </p>
            </div>
        </div>

        {{-- ── KANAN: Mesin Undian ── --}}
        <div class="lg:col-span-2 space-y-4">

            <div class="card" :style="mode!=='doorprize' ? ('border-color:' + theme().winnerBorder) : ''">

                {{-- Info hadiah + progress qty --}}
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-widest" style="color:#7B91A1">Hadiah</p>
                        <p class="font-bold" x-text="selectedPrize ? selectedPrize.nama : '— pilih hadiah —'"></p>
                    </div>
                    <div class="text-right" x-show="selectedPrize">
                        <p class="text-xs" style="color:#7B91A1">Pemenang ke-</p>
                        <p class="font-bold text-lg"
                           :style="'color:' + theme().solid"
                           x-text="(winnerIndex+1) + ' / ' + (selectedPrize?.qty ?? 1)"></p>
                    </div>
                </div>

                {{-- DRUM — Odometer / KM Counter Style --}}
                <div class="rounded-2xl bg-gray-900 border-2 p-4 mb-4 text-center overflow-hidden relative"
                     :style="isSpinning
                         ? ('border-color:' + theme().solid)
                         : (winner ? 'border-color:#22c55e' : 'border-color:rgba(123,145,161,0.3)')">
                    <div class="absolute inset-0 transition-opacity duration-300 pointer-events-none rounded-2xl"
                         :class="isSpinning ? 'opacity-100' : 'opacity-0'"
                         :style="'background:radial-gradient(ellipse at center,' + theme().glow + ' 0%,transparent 70%)'"></div>

                    {{-- 8 kolom digit odometer --}}
                    <div class="odo-wrap relative z-10">
                        <template x-for="slot in _odoSlots" :key="slot.k">
                            <div class="odo-col"
                                 :style="'border-color:' + (isSpinning ? theme().solid : (winner ? '#22c55e' : 'rgba(255,255,255,0.07)'))">
                                <span class="odo-char"
                                      :class="slot.landing ? 'odo-landing' : (isSpinning ? 'odo-rolling' : '')"
                                      :style="winner ? 'color:#22c55e' : (isSpinning ? ('color:' + theme().accent) : 'color:rgba(123,145,161,0.32)')"
                                      x-text="slot.val"></span>
                            </div>
                        </template>
                    </div>

                    {{-- SubCo peserta yang sedang cycling --}}
                    <p class="relative z-10 font-semibold tracking-wide mt-2 transition-all"
                       style="font-size:0.82rem; min-height:1.25em"
                       :style="isSpinning ? ('color:' + theme().solid) : (winner ? 'color:#22c55e' : 'color:transparent')"
                       x-text="displaySubco"></p>

                    <p class="text-xs relative z-10 mt-0.5"
                       :class="isSpinning ? 'animate-pulse' : 'opacity-0'"
                       style="color:rgba(123,145,161,0.45)">
                        <span x-show="isSpinning">
                            mengacak dari <strong x-text="eligibleCount"></strong> peserta
                        </span>
                    </p>
                </div>

                {{-- TOMBOL --}}
                <div class="flex gap-3 justify-center mb-4">
                    <button @click="startSpin()"
                            :disabled="!selectedPrize || isSpinning || !!winner"
                            class="px-10 py-3 text-white font-bold rounded-xl text-lg transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                            style="background:#244C6B">
                        🎲 Putar
                    </button>
                    <button @click="stopSpin()"
                            :disabled="!isSpinning"
                            class="px-10 py-3 text-white font-bold rounded-xl text-lg transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                            style="background:#D03F42">
                        🛑 Stop
                    </button>
                    <button @click="resetSpin()"
                            :disabled="isSpinning"
                            class="px-5 py-3 text-white font-bold rounded-xl text-lg transition-all disabled:opacity-40 disabled:cursor-not-allowed"
                            style="background:#7B91A1">
                        🔄
                    </button>
                </div>

                {{-- PEMENANG --}}
                <div x-show="winner" x-cloak class="win-reveal rounded-2xl p-5"
                     :style="'background:' + theme().winnerBg + '; border:1px solid ' + theme().winnerBorder">

                    <p class="text-center font-bold text-base mb-3"
                       :style="'color:' + theme().winnerTitleColor"
                       x-text="theme().winnerTitle"></p>

                    <div class="flex items-center gap-4">
                        <template x-if="winner?.gambar">
                            <img :src="winner.gambar" class="h-20 w-20 object-contain rounded-xl bg-white/10 p-1 shrink-0" alt="">
                        </template>
                        <div class="flex-1 min-w-0">
                            <p class="text-2xl font-black text-white" x-text="winner?.nama"></p>
                            <p class="text-sm" style="color:#7B91A1" x-text="'NPK: ' + winner?.npk"></p>
                            <p class="text-sm" style="color:#7B91A1" x-text="winner?.subco + ' · ' + winner?.jabatan"></p>
                            <p class="font-semibold mt-1"
                               :style="'color:' + theme().solid"
                               x-text="'🎁 ' + winner?.hadiah"></p>
                        </div>
                    </div>

                    <div class="flex gap-2 mt-4 justify-center">
                        <button @click="saveWinner()"
                                :disabled="saved || saving || disqualifying"
                                class="px-6 py-2.5 text-white font-semibold rounded-xl transition-all disabled:opacity-50"
                                style="background:#22c55e">
                            <span x-show="!saving && !saved">💾 Simpan</span>
                            <span x-show="saving" x-cloak>⏳ Menyimpan...</span>
                            <span x-show="saved" x-cloak>✅ Tersimpan!</span>
                        </button>
                        <button @click="disqualifyWinner()" x-show="!saved" x-cloak
                                :disabled="saving || disqualifying"
                                class="px-6 py-2.5 text-white font-semibold rounded-xl transition-all disabled:opacity-50"
                                style="background:#D03F42">
                            <span x-show="!disqualifying">❌ Tidak Ada / Hangus</span>
                            <span x-show="disqualifying" x-cloak>⏳ Memproses...</span>
                        </button>
                        <button @click="nextOrReset()" x-show="saved" x-cloak
                                class="px-6 py-2.5 text-white font-semibold rounded-xl transition-all"
                                style="background:#244C6B"
                                x-text="winnerIndex + 1 < (selectedPrize?.qty ?? 1) ? '▶ Berikutnya' : '🔄 Selesai'">
                        </button>
                    </div>
                </div>
            </div>

            {{-- Sesi saat ini --}}
            <div class="card" x-show="savedWinners.length > 0" x-cloak>
                <p class="font-semibold text-sm mb-2">Pemenang Sesi Ini</p>
                <div class="space-y-2 max-h-40 overflow-y-auto">
                    <template x-for="(w, i) in savedWinners" :key="i">
                        <div class="flex items-center gap-3 p-2.5 rounded-xl text-sm"
                             style="background:rgba(36,76,107,0.05); border:1px solid rgba(123,145,161,0.15)">
                            <span :x-text="modeThemes[w.type]?.emoji ?? '🎁'"></span>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold truncate" x-text="w.nama"></p>
                                <p class="text-xs" style="color:#7B91A1" x-text="w.subco"></p>
                            </div>
                            <p class="text-xs font-medium shrink-0" style="color:#D03F42" x-text="w.hadiah"></p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════════
         HISTORY PEMENANG (dari DB) + RESET BUTTON
         ════════════════════════════════════════════ --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-base">📋 History Pemenang Doorprize</h3>
                <p class="text-xs mt-0.5" style="color:#7B91A1">{{ $winners->count() }} pemenang tercatat · event aktif</p>
            </div>
            @if(auth()->user()->hasRole('admin'))
            <form method="POST" action="{{ route('admin.doorprizes.resetWinners') }}"
                  onsubmit="return confirm('⚠️ Hapus SEMUA pemenang?\n\nGambar dan data hadiah TETAP aman.\nSemua peserta akan kembali eligible untuk spin.\n\nLanjutkan?')">
                @csrf
                <button class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-semibold text-sm transition-all text-white"
                        style="background:rgba(208,63,66,0.15); color:#D03F42; border:1.5px solid rgba(208,63,66,0.3)">
                    🔃 Reset Semua Pemenang
                </button>
            </form>
            @endif
        </div>

        @if($winners->isEmpty())
        <div class="text-center py-10" style="color:#7B91A1">
            <p class="text-3xl mb-2">🎲</p>
            <p class="text-sm">Belum ada pemenang. Mulai spin untuk mendapatkan pemenang.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs uppercase tracking-widest" style="color:#7B91A1; border-bottom:1px solid rgba(123,145,161,0.2)">
                        <th class="py-2 px-3 text-center w-10">#</th>
                        <th class="py-2 px-3 text-left">Hadiah</th>
                        <th class="py-2 px-3 text-left">NPK</th>
                        <th class="py-2 px-3 text-left">Pemenang</th>
                        <th class="py-2 px-3 text-left">SubCo</th>
                        <th class="py-2 px-3 text-center">Waktu</th>
                        <th class="py-2 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($winners as $i => $w)
                    <tr class="border-b transition-colors" style="border-color:rgba(123,145,161,0.1)"
                        onmouseover="this.style.background='rgba(36,76,107,0.04)'"
                        onmouseout="this.style.background=''">
                        @php $meta = $typeMeta[$w->doorprize->type] ?? $typeMeta['doorprize']; @endphp
                        <td class="py-3 px-3 text-center font-bold" style="color:{{ $meta['color'] }}">
                            @if($i === 0) 🥇 @elseif($i === 1) 🥈 @elseif($i === 2) 🥉 @else {{ $i + 1 }} @endif
                        </td>
                        <td class="py-3 px-3">
                            <div class="flex items-center gap-2">
                                @if($w->doorprize->gambar)
                                    <img src="{{ $w->doorprize->gambar_url }}" class="h-9 w-9 object-contain rounded-lg"
                                         style="background:rgba(123,145,161,0.1)">
                                @else
                                    <span class="text-xl">{{ $meta['emoji'] }}</span>
                                @endif
                                <div>
                                    <p class="font-semibold text-sm">{{ $w->doorprize->nama_hadiah }}</p>
                                    <span class="text-xs px-1.5 py-0.5 rounded-full"
                                          style="background:{{ $meta['badgeBg'] }}; color:{{ $meta['badgeColor'] }}">
                                        {{ $meta['label'] }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 px-3 font-mono text-xs" style="color:#40647E">{{ $w->employee->npk }}</td>
                        <td class="py-3 px-3 font-semibold">{{ $w->employee->nama }}</td>
                        <td class="py-3 px-3"><span class="badge-blue">{{ $w->employee->subco }}</span></td>
                        <td class="py-3 px-3 text-center text-xs" style="color:#7B91A1">{{ $w->won_at->format('H:i:s') }}</td>
                        <td class="py-3 px-3 text-center">
                            @if(auth()->user()->hasRole('admin'))
                            <form method="POST" action="{{ route('admin.doorprizes.destroyWinner', $w) }}"
                                  onsubmit="return confirm('Hapus pemenang {{ addslashes($w->employee->nama) }}?')">
                                @csrf @method('DELETE')
                                <button class="text-xs px-2 py-1 rounded-lg transition-all"
                                        style="color:#D03F42; border:1px solid rgba(208,63,66,0.25)"
                                        onmouseover="this.style.background='rgba(208,63,66,0.08)'"
                                        onmouseout="this.style.background=''">Hapus</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- ════════════════════════════════════════════
         PESERTA HANGUS (dipanggil tapi tidak ada)
         ════════════════════════════════════════════ --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-bold text-base">❌ Peserta Hangus</h3>
                <p class="text-xs mt-0.5" style="color:#7B91A1">{{ $disqualifiedList->count() }} peserta ditandai hangus (dipanggil tapi tidak ada) · tidak ikut undian lagi</p>
            </div>
        </div>

        @if($disqualifiedList->isEmpty())
        <div class="text-center py-6" style="color:#7B91A1">
            <p class="text-sm">Belum ada peserta yang ditandai hangus.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs uppercase tracking-widest" style="color:#7B91A1; border-bottom:1px solid rgba(123,145,161,0.2)">
                        <th class="py-2 px-3 text-left">NPK</th>
                        <th class="py-2 px-3 text-left">Nama</th>
                        <th class="py-2 px-3 text-left">SubCo</th>
                        <th class="py-2 px-3 text-center">Waktu</th>
                        <th class="py-2 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($disqualifiedList as $d)
                    <tr class="border-b" style="border-color:rgba(123,145,161,0.1)">
                        <td class="py-3 px-3 font-mono text-xs" style="color:#40647E">{{ $d->employee_npk }}</td>
                        <td class="py-3 px-3 font-semibold">{{ $d->employee->nama ?? '-' }}</td>
                        <td class="py-3 px-3"><span class="badge-blue">{{ $d->employee->subco ?? '-' }}</span></td>
                        <td class="py-3 px-3 text-center text-xs" style="color:#7B91A1">{{ $d->disqualified_at?->format('H:i:s') }}</td>
                        <td class="py-3 px-3 text-center">
                            @if(auth()->user()->hasRole('admin'))
                            <form method="POST" action="{{ route('admin.doorprizes.destroyDisqualified', $d) }}"
                                  onsubmit="return confirm('Batalkan status hangus {{ addslashes($d->employee->nama ?? '') }}? Peserta ini akan eligible lagi untuk undian.')">
                                @csrf @method('DELETE')
                                <button class="text-xs px-2 py-1 rounded-lg transition-all"
                                        style="color:#244C6B; border:1px solid rgba(36,76,107,0.25)">Batalkan</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection

@push('scripts')
<script>
function spinApp() {
    return {
        mode: 'doorprize',
        selectedPrize: null,
        isSpinning: false,
        winner: null,
        saving: false,
        saved: false,
        disqualifying: false,
        excludedJabatan: [],
        selectedSubcos: [],
        allJabatan: @json($jabatanList),
        savedWinners: [],
        eligibleCount: {{ $eligibleCount }},
        winnerIndex: 0,
        displayNpk: '',
        displaySubco: '',
        _pool: [], _poolIdx: 0, _interval: null, _resolved: null,
        doorprizes: @json($doorprizes),
        subcoEligibleCounts: @json($subcoEligibleCounts),

        // ── Odometer state ──
        _odoSlots: Array.from({length: 8}, (_, i) => ({ val: '_', k: i + 1, landing: false })),
        _odoKeyCounter: 100,
        _odoSpinInt: null,

        init() {
            // Seed keys with high values to avoid clash with static init keys above
            this._odoKeyCounter = Date.now();
            this._resetOdo();
        },

        _resetOdo() {
            this._odoSlots = Array.from({length: 8}, () => ({
                val: '_', k: ++this._odoKeyCounter, landing: false
            }));
        },

        _spinOdo() {
            clearInterval(this._odoSpinInt);
            let tick = 0;
            this._odoSpinInt = setInterval(() => {
                if (!this.isSpinning) return;
                tick++;

                // Cycle pool entry for subco display + sound
                const entry = this._pool[this._poolIdx % this._pool.length];
                this.displaySubco = entry.subco ?? '';
                this._poolIdx++;
                if (tick % 2 === 0) this.playTick();

                // Digit columns: i=0 leftmost/slowest (updates every 8 ticks)
                //                i=7 rightmost/fastest (updates every tick)
                this._odoSlots = this._odoSlots.map((slot, i) => {
                    const every = 8 - i;
                    if (tick % every === 0) {
                        return { val: String(Math.floor(Math.random() * 10)), k: ++this._odoKeyCounter, landing: false };
                    }
                    return slot;
                });
            }, 80);
        },

        _settleOdo(targetNpk) {
            clearInterval(this._odoSpinInt);
            const digits = targetNpk.replace(/\D/g, '').padStart(8, '0').split('');
            // Kiri ke kanan: digit pertama berhenti duluan, digit satuan berhenti terakhir
            digits.forEach((d, i) => {
                setTimeout(() => {
                    this._odoSlots = this._odoSlots.map((slot, j) =>
                        j === i ? { val: d, k: ++this._odoKeyCounter, landing: true } : slot
                    );
                    if (i < digits.length - 1) this.beep(440 + i * 40, 0.1, 'triangle', 0.08);
                    else this.playWin();
                }, i * 110 + 80);
            });
        },

        filteredEligibleCount() {
            if ((this.mode !== 'doorprize_utama' && this.mode !== 'grand_prize') || !this.selectedSubcos.length) {
                return this.eligibleCount;
            }
            return this.selectedSubcos.reduce((sum, sc) => sum + (this.subcoEligibleCounts[sc] ?? 0), 0);
        },

        modeThemes: {
            doorprize:       { solid: '#244C6B', accent: '#40647E', gradient: 'linear-gradient(to right,#244C6B,#40647E)', glow: 'rgba(36,76,107,0.12)', winnerBg: 'rgba(34,197,94,0.06)', winnerBorder: 'rgba(34,197,94,0.25)', winnerTitleColor: '#22c55e', winnerTitle: '🎉 Pemenang Doorprize!', emoji: '🎁' },
            doorprize_utama: { solid: '#64748b', accent: '#94a3b8', gradient: 'linear-gradient(to right,#94a3b8,#64748b)', glow: 'rgba(100,116,139,0.16)', winnerBg: 'rgba(100,116,139,0.10)', winnerBorder: 'rgba(100,116,139,0.35)', winnerTitleColor: '#64748b', winnerTitle: '🥈 DOORPRIZE UTAMA WINNER!', emoji: '🥈' },
            grand_prize:     { solid: '#f59e0b', accent: '#f97316', gradient: 'linear-gradient(to right,#f59e0b,#f97316)', glow: 'rgba(234,179,8,0.12)', winnerBg: 'rgba(245,158,11,0.08)', winnerBorder: 'rgba(245,158,11,0.3)', winnerTitleColor: '#f59e0b', winnerTitle: '🏆 GRAND PRIZE WINNER!', emoji: '🏆' },
        },
        theme() { return this.modeThemes[this.mode]; },

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

        // ── Multi-Spin (2-50 kolom) ──
        multiConfig: {},
        multiBanner: '',
        multiExcludedJabatan: [],
        multiSlots: [],
        multiSession: false,
        multiBusy: false,
        multiSamplePool: [],
        _multiTick: 0,
        _multiInterval: null,

        multiQty(id) { return this.multiConfig[id]?.qty ?? 0; },
        multiTotalSlots() { return Object.values(this.multiConfig).reduce((sum, c) => sum + c.qty, 0); },
        multiQtyByType(type) { return Object.values(this.multiConfig).filter(c => c.type === type).reduce((sum, c) => sum + c.qty, 0); },
        multiIncrement(id, nama, gambar, type) {
            if (this.multiTotalSlots() >= 50) return;
            if (!this.multiConfig[id]) this.multiConfig[id] = { id, nama, gambar, type, qty: 0 };
            this.multiConfig[id].qty++;
        },
        multiDecrement(id, nama, gambar, type) {
            if (!this.multiConfig[id]) return;
            this.multiConfig[id].qty--;
            if (this.multiConfig[id].qty <= 0) delete this.multiConfig[id];
        },
        multiSetQty(id, nama, gambar, type, val) {
            const qty = Math.max(0, Math.min(50, parseInt(val) || 0));
            const remaining = 50 - this.multiTotalSlots() + (this.multiConfig[id]?.qty ?? 0);
            const clamped = Math.min(qty, remaining);
            if (clamped <= 0) { delete this.multiConfig[id]; return; }
            if (!this.multiConfig[id]) this.multiConfig[id] = { id, nama, gambar, type, qty: 0 };
            this.multiConfig[id].qty = clamped;
        },
        multiClearConfig() { this.multiConfig = {}; this.multiBanner = ''; this.multiExcludedJabatan = []; },

        multiDrumNpk(slot) {
            if (!this.multiSamplePool.length) return slot.winner?.npk ?? '_ _ _ _ _ _';
            return this.multiSamplePool[(this._multiTick + slot.id) % this.multiSamplePool.length];
        },

        async multiDrawAndStart() {
            const total = this.multiTotalSlots();
            if (total < 2 || total > 50) return;
            this.multiBusy = true;

            const doorprizeIds = [];
            Object.values(this.multiConfig).forEach(c => {
                for (let i = 0; i < c.qty; i++) doorprizeIds.push(c.id);
            });

            try {
                const drawRes = await fetch('{{ route('admin.doorprizes.multiDraw') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ doorprize_ids: doorprizeIds, excluded_jabatan: this.multiExcludedJabatan })
                });
                if (!drawRes.ok) { const err = await drawRes.json(); alert(err.error || 'Gagal mengocok.'); this.multiBusy = false; return; }
                const drawData = await drawRes.json();

                const startRes = await fetch('{{ route('admin.doorprizes.multiStart') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ slots: drawData.slots, banner_image: this.multiBanner || null, sample_npk: drawData.sample_npk })
                });
                if (!startRes.ok) { alert('Gagal memulai sesi multi-spin.'); this.multiBusy = false; return; }

                // Ambil data aktual dari server — bukan dari drawData lokal
                // Ini memastikan admin page dan TV display selalu menampilkan data yang persis sama
                const statusRes  = await fetch('{{ route('doorprizes.displayStatus') }}?_=' + Date.now());
                const statusData = await statusRes.json();
                this.multiSlots      = (statusData.slots ?? drawData.slots).map(s => ({ ...s, state: 'spinning' }));
                this.multiSamplePool = statusData.sample_npk ?? drawData.sample_npk;
                this.multiSession = true;
                this._multiTick = 0;
                clearInterval(this._multiInterval);
                this._multiInterval = setInterval(() => { this._multiTick++; this.playTick(); }, 90);
            } finally {
                this.multiBusy = false;
            }
        },

        async multiStopSlot(slotId) {
            this.multiBusy = true;
            try {
                const res = await fetch('{{ route('admin.doorprizes.multiStopSlot') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ slot_id: slotId })
                });
                if (!res.ok) { const err = await res.json(); alert(err.error || 'Gagal stop.'); return; }
                const slot = this.multiSlots.find(s => s.id === slotId);
                if (slot) slot.state = 'winner';
                this.playWin();
            } finally {
                this.multiBusy = false;
            }
        },

        async multiStopAll() {
            this.multiBusy = true;
            try {
                const res = await fetch('{{ route('admin.doorprizes.multiStopAll') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                });
                if (!res.ok) { const err = await res.json(); alert(err.error || 'Gagal stop semua.'); return; }
                this.multiSlots.forEach(s => s.state = 'winner');
                this.playWin();
            } finally {
                this.multiBusy = false;
            }
        },

        async multiResetSlot(slotId) {
            this.multiBusy = true;
            try {
                const res = await fetch('{{ route('admin.doorprizes.multiResetSlot') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ slot_id: slotId, excluded_jabatan: this.multiExcludedJabatan })
                });
                if (!res.ok) { const err = await res.json(); alert(err.error || 'Gagal mengulang.'); return; }
                const data = await res.json();
                const slot = this.multiSlots.find(s => s.id === slotId);
                if (slot && data.slot) {
                    slot.winner = data.slot.winner; // update pemenang baru dari server
                    slot.state  = 'spinning';
                }
            } finally {
                this.multiBusy = false;
            }
        },

        async multiDisqualifySlot(slotId) {
            const slot = this.multiSlots.find(s => s.id === slotId);
            if (!slot || !slot.winner) return;
            if (!confirm(`Tandai ${slot.winner.nama} (NPK ${slot.winner.npk}) hangus? Tidak akan bisa ikut undian lagi di event ini.`)) return;
            this.multiBusy = true;
            try {
                const res = await fetch('{{ route('admin.doorprizes.disqualify') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ employee_npk: slot.winner.npk })
                });
                if (!res.ok) { alert('Gagal menandai hangus.'); return; }
            } finally {
                this.multiBusy = false;
            }
            await this.multiResetSlot(slotId);
        },

        async multiResetAllSlots() {
            if (!confirm('Reset seluruh sesi multi-spin? Pemenang yang belum disimpan akan hilang.')) return;
            this.multiBusy = true;
            try {
                await fetch('{{ route('admin.doorprizes.multiResetAll') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                });
                clearInterval(this._multiInterval);
                this.multiSlots = [];
                this.multiSession = false;
                this.multiSamplePool = [];
            } finally {
                this.multiBusy = false;
            }
        },

        async multiSaveAll() {
            this.multiBusy = true;
            try {
                const res = await fetch('{{ route('admin.doorprizes.multiSaveWinners') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                });
                const data = await res.json();
                if (data.ok) {
                    let msg = `✅ ${data.saved} pemenang tersimpan` + (data.skipped ? `, ${data.skipped} dilewati (sudah pernah menang).` : '.');
                    if (data.mismatch && data.mismatch.length) {
                        msg += `\n\n⚠️ PERHATIAN — Data karyawan berubah setelah spin:\n` + data.mismatch.join('\n');
                        msg += `\n\nPemenang tetap disimpan berdasarkan NPK. Nama di halaman Cek Doorprize mengikuti data karyawan terbaru.`;
                    }
                    alert(msg);
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    alert(data.error || 'Gagal menyimpan.');
                }
            } finally {
                this.multiBusy = false;
            }
        },

        resetAll() {
            this.resetSpin(); this.selectedPrize = null; this.selectedSubcos = [];
            if (this.mode !== 'multi') this.syncDisplay('reset');
        },

        selectPrize(id, nama, gambar, qty, type) {
            if (this.isSpinning) return;
            this.selectedPrize = { id, nama, gambar, qty, type };
            this.resetSpin();
        },

        async startSpin() {
            if (!this.selectedPrize) { alert('Pilih hadiah terlebih dahulu!'); return; }
            this.winner = null; this.saved = false; this.saving = false; this._resolved = null;

            const res = await fetch('{{ route('admin.doorprizes.draw') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({
                    doorprize_id: this.selectedPrize.id,
                    excluded_jabatan: this.excludedJabatan,
                    subco: (this.mode === 'doorprize_utama' || this.mode === 'grand_prize') ? this.selectedSubcos : [],
                })
            });
            if (!res.ok) { const err = await res.json(); alert(err.error || 'Gagal.'); return; }

            const data = await res.json();
            this._resolved = data;
            // Pool berisi objek {npk, subco, nama} — difilter sesuai SubCo terpilih
            this._pool = data.all_entries ?? data.all_npk.map(n => ({ npk: n, subco: '', nama: '' }));
            this._poolIdx = 0;
            this.eligibleCount = this._pool.length;
            await this.syncDisplay('start', data.all_npk);

            this.isSpinning = true;
            this._spinOdo(); // Mulai odometer
        },

        async stopSpin() {
            if (!this.isSpinning || !this._resolved) return;
            clearInterval(this._interval);
            this.isSpinning = false;
            this.displayNpk   = this._resolved.npk;
            this.displaySubco = this._resolved.subco ?? '';
            // Animasi odometer berhenti digit per digit
            this._settleOdo(this._resolved.npk);
            this.winner = {
                npk: this._resolved.npk, nama: this._resolved.nama,
                subco: this._resolved.subco, jabatan: this._resolved.jabatan,
                hadiah: this._resolved.nama_hadiah, gambar: this._resolved.gambar_url,
                doorprize_id: this._resolved.doorprize_id, type: this._resolved.type,
            };
            await this.syncDisplay('stop', null, this.winner);
        },

        resetSpin() {
            clearInterval(this._interval);
            clearInterval(this._odoSpinInt);
            this.isSpinning = false; this.winner = null; this.saved = false;
            this.saving = false; this._resolved = null; this._poolIdx = 0;
            this.displayNpk = ''; this.displaySubco = ''; this.winnerIndex = 0;
            this._resetOdo();
        },

        async saveWinner() {
            if (!this.winner || this.saved || this.saving) return;
            this.saving = true;
            const res = await fetch('{{ route('admin.doorprizes.saveWinner') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ employee_npk: this.winner.npk, doorprize_id: this.winner.doorprize_id })
            });
            const data = await res.json();
            this.saving = false;
            if (data.success) {
                this.saved = true;
                this.eligibleCount = Math.max(0, this.eligibleCount - 1);
                this.savedWinners.unshift(data.winner);
                // Refresh halaman setelah simpan agar tabel history update
                setTimeout(() => { if (this.saved) { window.location.reload(); } }, 3000);
            } else {
                alert(data.error || 'Gagal menyimpan.');
            }
        },

        async disqualifyWinner() {
            if (!this.winner || this.saving || this.disqualifying) return;
            if (!confirm(`Tandai ${this.winner.nama} (NPK ${this.winner.npk}) hangus? Tidak akan bisa ikut undian lagi di event ini.`)) return;
            this.disqualifying = true;
            try {
                const res = await fetch('{{ route('admin.doorprizes.disqualify') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ employee_npk: this.winner.npk })
                });
                if (!res.ok) { alert('Gagal menandai hangus.'); return; }
                this.eligibleCount = Math.max(0, this.eligibleCount - 1);
                this.winner = null; this._resolved = null; this.displayNpk = '';
                this.syncDisplay('reset');
            } finally {
                this.disqualifying = false;
            }
        },

        nextOrReset() {
            const qty = this.selectedPrize?.qty ?? 1;
            if (this.winnerIndex + 1 < qty) {
                this.winnerIndex++;
                this.winner = null; this.saved = false; this.saving = false;
                this._resolved = null; this.displayNpk = '';
                this.syncDisplay('reset');
            } else {
                this.winnerIndex = 0;
                this.resetSpin();
                this.syncDisplay('reset');
            }
        },

        async syncDisplay(action, sampleNpk = null, winnerData = null) {
            try {
                if (action === 'start') {
                    await fetch('{{ route('admin.doorprizes.startDisplay') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ doorprize_nama: this.selectedPrize.nama, doorprize_type: this.selectedPrize.type, doorprize_gambar: this.selectedPrize.gambar ?? null, sample_npk: sampleNpk ?? [] })
                    });
                } else if (action === 'stop') {
                    await fetch('{{ route('admin.doorprizes.stopDisplay') }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ winner: winnerData })
                    });
                } else {
                    await fetch('{{ route('admin.doorprizes.resetDisplay') }}', {
                        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: '{}'
                    });
                }
            } catch (e) {}
        },
    }
}
</script>
@endpush
