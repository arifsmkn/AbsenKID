@extends('layouts.admin')
@section('title', 'Tambah Slide')
@section('page-title', 'Tambah Slide')
@section('content')
<div class="max-w-lg">
    <form method="POST" action="{{ route('admin.events.slides.store', $event) }}" enctype="multipart/form-data" class="card space-y-4">
        @csrf
        <div>
            <label class="label">Tipe</label>
            <select name="type" class="input w-full">
                <option value="image">🖼️ Gambar</option>
                <option value="video">🎬 Video</option>
            </select>
        </div>
        <div>
            <label class="label">File (Gambar: max 50MB, Video: max 50MB)</label>
            <input type="file" name="file" accept="image/*,video/*" class="input w-full" required>
        </div>
        <div>
            <label class="label">Judul (opsional)</label>
            <input type="text" name="judul" value="{{ old('judul') }}" class="input w-full">
        </div>
        <div>
            <label class="label">Caption (opsional)</label>
            <textarea name="caption" rows="2" class="input w-full">{{ old('caption') }}</textarea>
        </div>
        <div>
            <label class="label">Urutan</label>
            <input type="number" name="urutan" value="{{ old('urutan', 0) }}" class="input w-full">
        </div>
        <div class="flex gap-2 pt-2">
            <button class="btn-primary">Upload Slide</button>
            <a href="{{ route('admin.events.slides.index', $event) }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
