@extends('layouts.admin')
@section('title', 'Kelola Hadiah')
@section('page-title', 'Kelola Hadiah Doorprize')
@section('content')
<div class="flex justify-between mb-4">
    <div class="flex gap-2">
        <a href="{{ route('admin.doorprizes.spin') }}" class="btn-primary">🎲 Mulai Spin</a>
        <a href="{{ route('admin.doorprizes.winners') }}" class="btn-secondary">🏆 History Pemenang</a>
    </div>
    <a href="{{ route('admin.doorprizes.create') }}" class="btn-secondary">+ Tambah Hadiah</a>
</div>

{{-- Doorprize biasa --}}
@php
    $regular = $doorprizes->where('type', 'doorprize');
    $utama   = $doorprizes->where('type', 'doorprize_utama');
    $grands  = $doorprizes->where('type', 'grand_prize');
@endphp

@if($grands->count())
<h3 class="font-semibold text-sm text-yellow-600 dark:text-yellow-400 uppercase tracking-widest mb-3 mt-2">🏆 Grand Prize</h3>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
    @foreach($grands as $dp)
    <div class="card p-0 overflow-hidden ring-2 ring-yellow-400/60">
        @if($dp->gambar)
            <img src="{{ $dp->gambar_url }}" class="w-full h-40 object-cover">
        @else
            <div class="w-full h-40 bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center text-5xl">🏆</div>
        @endif
        <div class="p-4">
            <span class="text-xs font-bold text-yellow-600 dark:text-yellow-400 uppercase">Grand Prize</span>
            <p class="font-semibold mt-0.5">{{ $dp->nama_hadiah }}</p>
            <p class="text-sm text-gray-500">Qty: {{ $dp->jumlah }}</p>
            <div class="flex gap-2 mt-3">
                <a href="{{ route('admin.doorprizes.edit', $dp) }}" class="btn-secondary text-xs py-1 px-2">Edit</a>
                <form method="POST" action="{{ route('admin.doorprizes.destroy', $dp) }}" onsubmit="return confirm('Hapus hadiah ini?')">
                    @csrf @method('DELETE')
                    <button class="btn-danger text-xs py-1 px-2">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

@if($utama->count())
<h3 class="font-semibold text-sm text-slate-500 dark:text-slate-300 uppercase tracking-widest mb-3 mt-2">🥈 Doorprize Utama</h3>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
    @foreach($utama as $dp)
    <div class="card p-0 overflow-hidden ring-2 ring-slate-400/50">
        @if($dp->gambar)
            <img src="{{ $dp->gambar_url }}" class="w-full h-40 object-cover">
        @else
            <div class="w-full h-40 bg-slate-100 dark:bg-slate-700/30 flex items-center justify-center text-5xl">🥈</div>
        @endif
        <div class="p-4">
            <span class="text-xs font-bold text-slate-500 dark:text-slate-300 uppercase">Doorprize Utama</span>
            <p class="font-semibold mt-0.5">{{ $dp->nama_hadiah }}</p>
            <p class="text-sm text-gray-500">Qty: {{ $dp->jumlah }}</p>
            <div class="flex gap-2 mt-3">
                <a href="{{ route('admin.doorprizes.edit', $dp) }}" class="btn-secondary text-xs py-1 px-2">Edit</a>
                <form method="POST" action="{{ route('admin.doorprizes.destroy', $dp) }}" onsubmit="return confirm('Hapus hadiah ini?')">
                    @csrf @method('DELETE')
                    <button class="btn-danger text-xs py-1 px-2">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

<h3 class="font-semibold text-sm text-blue-600 dark:text-blue-400 uppercase tracking-widest mb-3">🎁 Doorprize</h3>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
    @forelse($regular as $dp)
    <div class="card p-0 overflow-hidden">
        <div class="w-full h-24 bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-4xl">🎁</div>
        <div class="p-4">
            <p class="font-semibold">{{ $dp->nama_hadiah }}</p>
            <p class="text-sm text-gray-500">Qty: {{ $dp->jumlah }}</p>
            <div class="flex gap-2 mt-3">
                <a href="{{ route('admin.doorprizes.edit', $dp) }}" class="btn-secondary text-xs py-1 px-2">Edit</a>
                <form method="POST" action="{{ route('admin.doorprizes.destroy', $dp) }}" onsubmit="return confirm('Hapus hadiah ini?')">
                    @csrf @method('DELETE')
                    <button class="btn-danger text-xs py-1 px-2">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <p class="col-span-4 text-center text-gray-400 py-10">Belum ada hadiah. <a href="{{ route('admin.doorprizes.create') }}" class="text-blue-500">Tambah hadiah</a></p>
    @endforelse
</div>
@endsection
