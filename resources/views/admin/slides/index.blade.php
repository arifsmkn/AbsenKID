@extends('layouts.admin')
@section('title', 'Slides')
@section('page-title', 'Slides — '.$event->nama)
@section('content')
<div class="flex justify-between mb-4">
    <a href="{{ route('admin.events.index') }}" class="btn-secondary text-xs">← Kembali ke Events</a>
    <a href="{{ route('admin.events.slides.create', $event) }}" class="btn-primary">+ Tambah Slide</a>
</div>
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
    @forelse($slides as $slide)
    <div class="card p-0 overflow-hidden">
        @if($slide->type === 'video')
            <video src="{{ $slide->file_url }}" class="w-full h-32 object-cover" muted></video>
        @else
            <img src="{{ $slide->file_url }}" class="w-full h-32 object-cover">
        @endif
        <div class="p-3">
            <div class="flex items-center justify-between mb-1">
                <p class="text-xs font-semibold truncate">{{ $slide->judul ?? 'Slide '.($loop->index+1) }}</p>
                <span class="text-xs {{ $slide->is_active ? 'text-green-500' : 'text-gray-400' }}">{{ $slide->is_active ? '●' : '○' }}</span>
            </div>
            <p class="text-xs text-gray-500 mb-2">{{ strtoupper($slide->type) }} · urutan: {{ $slide->urutan }}</p>
            <div class="flex gap-1">
                <a href="{{ route('admin.events.slides.edit', [$event, $slide]) }}" class="btn-secondary text-xs py-0.5 px-2">Edit</a>
                <form method="POST" action="{{ route('admin.events.slides.destroy', [$event, $slide]) }}" onsubmit="return confirm('Hapus slide ini?')">
                    @csrf @method('DELETE')
                    <button class="btn-danger text-xs py-0.5 px-2">Hapus</button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-4 card text-center py-10 text-gray-400">Belum ada slide. <a href="{{ route('admin.events.slides.create', $event) }}" class="text-blue-500">Tambah slide</a></div>
    @endforelse
</div>
@endsection
