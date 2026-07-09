@extends('layouts.admin')
@section('title', 'Exclude Jabatan Doorprize')
@section('page-title', 'Jabatan Exclude dari Doorprize')
@section('content')

@if(session('success'))
<div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">✅ {{ session('success') }}</div>
@endif

<div class="grid md:grid-cols-2 gap-6">

    {{-- Form Tambah --}}
    <div class="card p-5">
        <h2 class="font-semibold text-gray-800 mb-4">Tambah Jabatan Exclude</h2>
        <form method="POST" action="{{ route('admin.doorprize-exclude-roles.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih atau Ketik Jabatan</label>
                <input list="jabatan-list" name="jabatan" class="input w-full" placeholder="Ketik atau pilih jabatan..." required>
                <datalist id="jabatan-list">
                    @foreach($jabatanList as $j)
                        <option value="{{ $j }}">
                    @endforeach
                </datalist>
                @error('jabatan')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan (opsional)</label>
                <input type="text" name="keterangan" class="input w-full" placeholder="Contoh: Level Direktur">
            </div>
            <button class="btn-primary w-full">Tambahkan ke Daftar Exclude</button>
        </form>
        <p class="text-xs text-gray-400 mt-3">
            Jabatan yang ada di daftar ini <strong>tidak akan masuk</strong> ke dalam pool kocokan doorprize,
            meskipun mereka hadir secara fisik.
        </p>
    </div>

    {{-- List yang sudah exclude --}}
    <div class="card p-5">
        <h2 class="font-semibold text-gray-800 mb-4">Daftar Jabatan Exclude ({{ $roles->count() }})</h2>
        @if($roles->isEmpty())
            <div class="text-center py-8 text-gray-400">
                <p class="text-3xl mb-2">✅</p>
                <p class="text-sm">Belum ada jabatan yang di-exclude.<br>Semua peserta hadir masuk pool doorprize.</p>
            </div>
        @else
        <div class="space-y-2">
            @foreach($roles as $role)
            <div class="flex items-center justify-between p-3 rounded-xl bg-red-50 border border-red-100">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">{{ $role->jabatan }}</p>
                    @if($role->keterangan)
                        <p class="text-gray-400 text-xs">{{ $role->keterangan }}</p>
                    @endif
                </div>
                <form method="POST" action="{{ route('admin.doorprize-exclude-roles.destroy', $role) }}"
                      onsubmit="return confirm('Hapus exclude jabatan {{ $role->jabatan }}?')">
                    @csrf @method('DELETE')
                    <button class="text-xs text-red-500 hover:text-red-700 font-medium px-2 py-1 rounded hover:bg-red-100 transition-colors">
                        Hapus
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<div class="card mt-4 p-4 bg-amber-50 border border-amber-200">
    <p class="text-sm text-amber-800">
        <strong>ℹ️ Info:</strong> Exclude ini berlaku otomatis di semua kocokan doorprize (single & multi-spin).
        Admin masih bisa menambah exclude manual saat spin berlangsung.
    </p>
</div>

@endsection
