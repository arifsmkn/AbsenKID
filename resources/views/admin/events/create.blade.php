@extends('layouts.admin')
@section('title', 'Buat Event')
@section('page-title', 'Buat Event Baru')
@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data" class="card space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2">
                <label class="label">Nama Event <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', 'Konvensi Improvement Dharma ke-') }}" class="input w-full" required>
            </div>
            <div>
                <label class="label">Tahun <span class="text-red-500">*</span></label>
                <input type="number" name="tahun" value="{{ old('tahun', date('Y')) }}" class="input w-full" required>
            </div>
            <div>
                <label class="label">Tanggal</label>
                <input type="date" name="tanggal" value="{{ old('tanggal') }}" class="input w-full">
            </div>
            <div>
                <label class="label">Waktu Mulai</label>
                <input type="time" name="waktu_mulai" value="{{ old('waktu_mulai', '08:00') }}" class="input w-full">
            </div>
            <div>
                <label class="label">Waktu Selesai</label>
                <input type="time" name="waktu_selesai" value="{{ old('waktu_selesai', '17:00') }}" class="input w-full">
            </div>
        </div>
        <div>
            <label class="label">Tema</label>
            <input type="text" name="tema" value="{{ old('tema') }}" class="input w-full" placeholder="Tema acara tahun ini...">
        </div>
        <div>
            <label class="label">Deskripsi</label>
            <textarea name="deskripsi" rows="3" class="input w-full" placeholder="Deskripsi singkat event...">{{ old('deskripsi') }}</textarea>
        </div>
        <div>
            <label class="label">Lokasi</label>
            <input type="text" name="lokasi" value="{{ old('lokasi') }}" class="input w-full" placeholder="Nama gedung / venue">
        </div>
        <div>
            <label class="label">Maps Embed (tampilan peta interaktif di halaman depan)</label>
            <textarea name="maps_embed" rows="2" class="input w-full text-xs" placeholder='&lt;iframe src="..." ...&gt;&lt;/iframe&gt;'>{{ old('maps_embed') }}</textarea>
        </div>
        <div>
            <label class="label">Link Google Maps (untuk tombol "Buka di Google Maps")</label>
            <input type="url" name="maps_url" value="{{ old('maps_url') }}" class="input w-full text-sm" placeholder="https://maps.app.goo.gl/xxxxx">
            <p class="text-xs text-gray-400 mt-1">Cara dapat link: buka Google Maps → cari lokasi venue → tombol <strong>Share/Bagikan</strong> → <strong>Copy link</strong> → tempel di sini.</p>
        </div>
        <div>
            <label class="label">Logo Event</label>
            <input type="file" name="logo" accept="image/*" class="input w-full">
        </div>
        <div>
            <label class="label">Wallpaper Halaman Depan</label>
            <input type="file" name="wallpaper" accept="image/*" class="input w-full">
            <p class="text-xs text-gray-500 mt-1">Background untuk halaman login &amp; dashboard peserta. Maks 5MB.</p>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="label">Warna Primer</label>
                <input type="color" name="primary_color" value="{{ old('primary_color', '#1e40af') }}" class="input w-full h-10 p-1">
            </div>
            <div>
                <label class="label">Warna Sekunder</label>
                <input type="color" name="secondary_color" value="{{ old('secondary_color', '#7c3aed') }}" class="input w-full h-10 p-1">
            </div>
            <div>
                <label class="label">Mode Tampilan</label>
                <select name="mode" class="input w-full">
                    <option value="dark">Dark</option>
                    <option value="light">Light</option>
                </select>
            </div>
        </div>
        <div class="flex gap-2 pt-2">
            <button class="btn-primary">Buat Event</button>
            <a href="{{ route('admin.events.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
