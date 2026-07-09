@extends('layouts.admin')
@section('title', 'Edit SubCo')
@section('page-title', 'Edit SubCo — '.$subco->nama)

@section('content')
<div class="max-w-md">
    <form method="POST" action="{{ route('admin.subcos.update', $subco) }}" class="card space-y-4">
        @csrf @method('PUT')
        <div>
            <label class="label">Nama SubCo <span class="text-red-500">*</span></label>
            <input type="text" name="nama" value="{{ old('nama', $subco->nama) }}" class="input w-full" required>
            @error('nama')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label class="label">Singkatan</label>
            <input type="text" name="singkatan" value="{{ old('singkatan', $subco->singkatan) }}" class="input w-full" maxlength="20">
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="is_active" value="1" id="is_active"
                   {{ $subco->is_active ? 'checked' : '' }} class="w-4 h-4 rounded">
            <label for="is_active" class="label mb-0 cursor-pointer">Aktif</label>
        </div>
        @if($subco->jumlah_karyawan ?? 0)
        <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg text-sm text-blue-700 dark:text-blue-300">
            ⚠️ Jika nama diubah, nama SubCo di semua karyawan akan ikut diperbarui.
        </div>
        @endif
        <div class="flex gap-2 pt-2">
            <button class="btn-primary">Update</button>
            <a href="{{ route('admin.subcos.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
