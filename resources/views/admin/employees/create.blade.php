@extends('layouts.admin')
@section('title', 'Tambah Karyawan')
@section('page-title', 'Tambah Karyawan')
@section('content')
<div class="max-w-xl">
    <form method="POST" action="{{ route('admin.employees.store') }}" class="card space-y-4">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">NPK <span class="text-red-500">*</span></label>
                <input type="text" name="npk" value="{{ old('npk') }}" class="input w-full" required>
                @error('npk')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" class="input w-full" required>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">SubCo <span class="text-red-500">*</span></label>
                <select name="subco" class="input w-full" required>
                    <option value="">-- Pilih SubCo --</option>
                    @foreach($subcos as $s)
                        <option value="{{ $s }}" {{ old('subco')==$s?'selected':'' }}>{{ $s }}</option>
                    @endforeach
                </select>
                @if($subcos->isEmpty())
                    <p class="text-orange-500 text-xs mt-1">⚠️ Belum ada SubCo. <a href="{{ route('admin.subcos.index') }}" class="underline">Tambah SubCo dulu</a></p>
                @endif
            </div>
            <div>
                <label class="label">Jabatan <span class="text-red-500">*</span></label>
                <input type="text" name="jabatan" value="{{ old('jabatan') }}" class="input w-full" required>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="label">Ukuran Baju</label>
                <select name="ukuran_baju" class="input w-full">
                    <option value="">-</option>
                    @foreach(['XS','S','M','L','XL','XXL','XXXL'] as $size)
                        <option value="{{ $size }}" {{ old('ukuran_baju')==$size?'selected':'' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label class="label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="input w-full">
            </div>
        </div>
        <div>
            <label class="label">No. Telepon</label>
            <input type="text" name="no_telpon" value="{{ old('no_telpon') }}" class="input w-full">
        </div>
        <div class="flex gap-2 pt-2">
            <button class="btn-primary">Simpan</button>
            <a href="{{ route('admin.employees.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
