@extends('layouts.admin')
@section('title', 'Edit Karyawan')
@section('page-title', 'Edit Karyawan — '.$employee->nama)
@section('content')
<div class="max-w-xl">
    <form method="POST" action="{{ route('admin.employees.update', $employee) }}" class="card space-y-4">
        @csrf @method('PUT')
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">NPK</label>
                <input type="text" value="{{ $employee->npk }}" class="input w-full bg-gray-100 dark:bg-gray-700" readonly>
            </div>
            <div>
                <label class="label">Nama Lengkap</label>
                <input type="text" name="nama" value="{{ old('nama', $employee->nama) }}" class="input w-full" required>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">SubCo</label>
                <select name="subco" class="input w-full" required>
                    <option value="">-- Pilih SubCo --</option>
                    @foreach($subcos as $s)
                        <option value="{{ $s }}" {{ old('subco',$employee->subco)==$s?'selected':'' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Jabatan</label>
                <input type="text" name="jabatan" value="{{ old('jabatan', $employee->jabatan) }}" class="input w-full" required>
            </div>
        </div>
        <div class="grid grid-cols-3 gap-4">
            <div>
                <label class="label">Ukuran Baju</label>
                <select name="ukuran_baju" class="input w-full">
                    <option value="">-</option>
                    @foreach(['XS','S','M','L','XL','XXL','XXXL'] as $size)
                        <option value="{{ $size }}" {{ old('ukuran_baju',$employee->ukuran_baju)==$size?'selected':'' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-2">
                <label class="label">Email</label>
                <input type="email" name="email" value="{{ old('email', $employee->email) }}" class="input w-full">
            </div>
        </div>
        <div>
            <label class="label">No. Telepon</label>
            <input type="text" name="no_telpon" value="{{ old('no_telpon', $employee->no_telpon) }}" class="input w-full">
        </div>
        <div class="flex gap-2 pt-2">
            <button class="btn-primary">Update</button>
            <a href="{{ route('admin.employees.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
