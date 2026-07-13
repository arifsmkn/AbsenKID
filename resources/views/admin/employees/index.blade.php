@extends('layouts.admin')
@section('title', 'Master Data Karyawan')
@section('page-title', 'Master Data Karyawan')

@section('content')
<div class="flex flex-col sm:flex-row gap-3 mb-4">
    <form method="GET" class="flex gap-2 flex-1">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NPK, Nama, SubCo..." class="input flex-1">
        <select name="subco" class="input w-40">
            <option value="">Semua SubCo</option>
            @foreach($subcos as $s)<option value="{{ $s }}" {{ request('subco')==$s?'selected':'' }}>{{ $s }}</option>@endforeach
        </select>
        <button class="btn-primary">Cari</button>
    </form>
    <div class="flex gap-2">
        <a href="{{ route('admin.employees.create') }}" class="btn-primary">+ Tambah</a>
        <button onclick="document.getElementById('importModal').classList.remove('hidden')" class="btn-secondary">📥 Import</button>
        <a href="{{ route('admin.employees.export') }}" class="btn-secondary">📤 Export</a>
        <button onclick="document.getElementById('clearAllModal').classList.remove('hidden')" class="btn-secondary text-red-600">🗑️ Hapus Semua</button>
    </div>
</div>

<div class="card overflow-hidden p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 text-left">NPK</th>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">SubCo</th>
                    <th class="px-4 py-3 text-left">Jabatan</th>
                    <th class="px-4 py-3 text-left">Baju</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Telp</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($employees as $emp)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-4 py-3 font-mono font-medium text-blue-600">{{ $emp->npk }}</td>
                    <td class="px-4 py-3 font-medium">{{ $emp->nama }}</td>
                    <td class="px-4 py-3"><span class="badge-blue">{{ $emp->subco }}</span></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $emp->jabatan }}</td>
                    <td class="px-4 py-3 text-center"><span class="badge-gray">{{ $emp->ukuran_baju ?? '-' }}</span></td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">{{ $emp->email ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">{{ $emp->no_telpon ?? '-' }}</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.employees.edit', $emp) }}" class="text-blue-500 hover:underline text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.employees.destroy', $emp) }}" onsubmit="return confirm('Hapus karyawan ini?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:underline text-xs">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-10 text-gray-400">Belum ada data karyawan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $employees->links() }}</div>
</div>

{{-- Import Modal --}}
<div id="importModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md">
        <h3 class="font-semibold mb-4">Import Data Karyawan</h3>
        <p class="text-sm text-gray-500 mb-3">Format kolom: <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">npk, nama, subco, jabatan, ukuran_baju, email, no_telpon</code></p>
        <a href="{{ route('admin.employees.template') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-800 mb-4">
            ⬇️ Download Template Excel
        </a>
        <form method="POST" action="{{ route('admin.employees.import') }}" enctype="multipart/form-data">
            @csrf
            <input type="file" name="file" accept=".xlsx,.xls,.csv" class="input w-full mb-4" required>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="btn-secondary">Batal</button>
                <button class="btn-primary">Import</button>
            </div>
        </form>
    </div>
</div>

{{-- Clear All Modal --}}
<div id="clearAllModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="card w-full max-w-md">
        <h3 class="font-semibold mb-2 text-red-600">⚠️ Hapus Semua Data Karyawan</h3>
        <p class="text-sm text-gray-500 mb-3">
            Ini akan menghapus <strong>seluruh</strong> data karyawan beserta data terkait (undangan, absensi, konfirmasi kehadiran, pemenang doorprize, log kirim WA/email). Tindakan ini <strong>tidak bisa dibatalkan</strong>.
        </p>
        <form method="POST" action="{{ route('admin.employees.clear-all') }}" onsubmit="return confirm('Yakin? Semua data karyawan & data terkait akan dihapus permanen.')">
            @csrf @method('DELETE')
            <label class="text-sm font-medium mb-1 block">Ketik <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">HAPUS SEMUA</code> untuk konfirmasi:</label>
            <input type="text" name="confirm_text" class="input w-full mb-4" autocomplete="off" required>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="document.getElementById('clearAllModal').classList.add('hidden')" class="btn-secondary">Batal</button>
                <button class="btn-primary bg-red-600 hover:bg-red-700">Hapus Semua</button>
            </div>
        </form>
    </div>
</div>
@endsection
