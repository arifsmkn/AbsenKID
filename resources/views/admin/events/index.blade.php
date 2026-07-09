@extends('layouts.admin')
@section('title', 'Events')
@section('page-title', 'Manajemen Event')
@section('content')
<div class="flex justify-end mb-4">
    <a href="{{ route('admin.events.create') }}" class="btn-primary">+ Buat Event Baru</a>
</div>
<div class="space-y-4">
    @forelse($events as $event)
    <div class="card flex flex-col sm:flex-row gap-4 items-start">
        @if($event->logo)
            <img src="{{ asset('storage/'.$event->logo) }}" class="h-20 w-20 object-contain rounded-xl bg-gray-100 dark:bg-gray-700 p-2 shrink-0">
        @else
            <div class="h-20 w-20 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-3xl shrink-0">📅</div>
        @endif
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap">
                <h3 class="font-bold text-lg">{{ $event->nama }}</h3>
            f    @if($event->is_active)
                    <span class="badge-green text-xs">● AKTIF</span>
                @endif
            </div>
            <p class="text-sm text-gray-500">{{ $event->tahun }} · {{ $event->lokasi ?? 'Lokasi belum diset' }}</p>
            <p class="text-sm text-gray-500">{{ $event->tanggal?->format('d M Y') }}</p>
            @if($event->tema)
            <p class="text-sm text-gray-400 italic mt-1">"{{ $event->tema }}"</p>
            @endif
        </div>
        <div class="flex gap-2 flex-wrap shrink-0">
            @if(!$event->is_active)
            <form method="POST" action="{{ route('admin.events.activate', $event) }}" onsubmit="return confirm('Aktifkan event ini?')">
                @csrf
                <button class="btn-secondary text-xs">✅ Aktifkan</button>
            </form>
            @endif
            <a href="{{ route('admin.events.slides.index', $event) }}" class="btn-secondary text-xs">🖼️ Slides</a>
            <a href="{{ route('admin.events.edit', $event) }}" class="btn-secondary text-xs">Edit</a>
            <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('Hapus event ini?')">
                @csrf @method('DELETE')
                <button class="btn-danger text-xs">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div class="card text-center py-10 text-gray-400">Belum ada event. <a href="{{ route('admin.events.create') }}" class="text-blue-500">Buat sekarang</a></div>
    @endforelse
</div>
@endsection
