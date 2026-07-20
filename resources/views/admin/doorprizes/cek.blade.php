@extends('layouts.admin')
@section('title', 'Cek Doorprize')
@section('page-title', 'Cek Doorprize (Verifikasi Klaim Hadiah)')
@section('content')
<div class="max-w-lg mx-auto space-y-4">

    <div class="card">
        <form method="GET" action="{{ route('admin.doorprizes.cek') }}" class="flex gap-2">
            <input type="text" name="npk" value="{{ request('npk') }}"
                   placeholder="Masukkan NPK peserta..."
                   class="input flex-1" required autocomplete="off" autofocus>
            <button class="btn-primary">🔍 Cek</button>
        </form>
    </div>

    @if(request('npk'))
        @if($winner)
        <div class="card overflow-hidden p-0">
            <div class="p-4 text-white text-center" style="background:linear-gradient(135deg,#f59e0b,#ef4444)">
                <p class="text-lg font-black">🎉 Menang Doorprize!</p>
            </div>
            @php
                $cekMeta = [
                    'doorprize'       => ['emoji' => '🎁', 'label' => 'Doorprize', 'badge' => 'bg-blue-100 text-blue-700'],
                    'doorprize_utama' => ['emoji' => '🥈', 'label' => 'Doorprize Utama', 'badge' => 'bg-slate-200 text-slate-700'],
                    'grand_prize'     => ['emoji' => '🏆', 'label' => 'Grand Prize', 'badge' => 'bg-yellow-100 text-yellow-700'],
                ];
                $cekType = $cekMeta[$winner->doorprize?->type] ?? $cekMeta['doorprize'];
            @endphp
            <div class="p-5">
                <div class="flex items-center gap-4">
                    @if($winner->doorprize?->gambar)
                        <img src="{{ $winner->doorprize->gambar_url }}" class="h-20 w-20 object-contain rounded-xl border border-gray-100">
                    @else
                        <div class="text-5xl">{{ $cekType['emoji'] }}</div>
                    @endif
                    <div>
                        <p class="font-black text-gray-800 text-xl">{{ $employee?->nama }}</p>
                        <p class="text-gray-500 text-sm">NPK: {{ $employee?->npk }}</p>
                        <p class="text-gray-500 text-sm">{{ $employee?->subco }}</p>
                        <div class="mt-2 px-3 py-1.5 rounded-xl inline-block {{ $cekType['badge'] }}">
                            <p class="font-bold text-sm">{{ $cekType['emoji'] }} {{ $cekType['label'] }}: {{ $winner->doorprize?->nama_hadiah ?? 'Hadiah' }}</p>
                        </div>
                        <p class="text-gray-400 text-xs mt-1">Pukul {{ $winner->won_at->timezone('Asia/Jakarta')->format('H:i') }} WIB</p>
                    </div>
                </div>
            </div>
        </div>

        @elseif($employee)
        <div class="card text-center">
            <p class="text-4xl mb-3">😔</p>
            <p class="text-gray-700 font-semibold">Belum menang doorprize</p>
            <p class="text-gray-500 text-sm mt-1">{{ $employee->nama }} ({{ $employee->npk }})</p>
        </div>

        @else
        <div class="card text-center" style="background:rgba(220,38,38,0.06); border-color:rgba(220,38,38,0.25)">
            <p class="text-red-600 font-semibold">NPK tidak ditemukan</p>
            <p class="text-red-400 text-sm mt-1">Pastikan NPK yang dimasukkan benar.</p>
        </div>
        @endif
    @endif

</div>
@endsection
