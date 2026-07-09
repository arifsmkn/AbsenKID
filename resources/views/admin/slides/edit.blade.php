@extends('layouts.admin')
@section('title', 'Edit Slide')
@section('page-title', 'Edit Slide')
@section('content')
<div class="max-w-lg">
    <form method="POST" action="{{ route('admin.events.slides.update', [$event, $slide]) }}" enctype="multipart/form-data" class="card space-y-4">
        @csrf @method('PUT')
        <div class="p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
            @if($slide->type === 'video')
                <video src="{{ $slide->file_url }}" controls class="w-full h-40 object-contain rounded"></video>
            @else
                <img src="{{ $slide->file_url }}" class="w-full h-40 object-contain rounded">
            @endif
        </div>
        <div>
            <label class="label">Ganti File</label>
            <input type="file" name="file" accept="image/*,video/*" class="input w-full">
        </div>
        <div>
            <label class="label">Judul</label>
            <input type="text" name="judul" value="{{ old('judul', $slide->judul) }}" class="input w-full">
        </div>
        <div>
            <label class="label">Caption</label>
            <textarea name="caption" rows="2" class="input w-full">{{ old('caption', $slide->caption) }}</textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Urutan</label>
                <input type="number" name="urutan" value="{{ old('urutan', $slide->urutan) }}" class="input w-full">
            </div>
            <div class="flex items-end">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $slide->is_active ? 'checked' : '' }} class="w-4 h-4 rounded">
                    <span class="label mb-0">Aktif</span>
                </label>
            </div>
        </div>
        <div class="flex gap-2 pt-2">
            <button class="btn-primary">Update</button>
            <a href="{{ route('admin.events.slides.index', $event) }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
