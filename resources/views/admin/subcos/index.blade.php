@extends('layouts.admin')
@section('title', 'Master SubCo')
@section('page-title', 'Master Data SubCo')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Form Tambah --}}
    <div class="lg:col-span-1">
        <div class="card">
            <h3 class="font-semibold mb-4">Tambah SubCo Baru</h3>
            <form method="POST" action="{{ route('admin.subcos.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="label">Nama SubCo <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" value="{{ old('nama') }}"
                           placeholder="contoh: PT Dharma Electrindo"
                           class="input w-full" required>
                    @error('nama')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="label">Singkatan</label>
                    <input type="text" name="singkatan" value="{{ old('singkatan') }}"
                           placeholder="contoh: DEM"
                           class="input w-full" maxlength="20">
                </div>
                <button class="btn-primary w-full justify-center">+ Tambah SubCo</button>
            </form>
        </div>
    </div>

    {{-- Tabel SubCo --}}
    <div class="lg:col-span-2">
        <div class="card overflow-hidden p-0">
            <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between">
                <h3 class="font-semibold">Daftar SubCo ({{ $subcos->count() }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3 text-left">Nama SubCo</th>
                            <th class="px-4 py-3 text-center">Singkatan</th>
                            <th class="px-4 py-3 text-center">Karyawan</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($subcos as $subco)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3 font-medium">{{ $subco->nama }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge-gray">{{ $subco->singkatan ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-semibold text-blue-600">{{ $subco->jumlah_karyawan }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($subco->is_active)
                                    <span class="badge-green">Aktif</span>
                                @else
                                    <span class="badge-gray">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.subcos.edit', $subco) }}"
                                       class="text-blue-500 hover:underline text-xs">Edit</a>
                                    <form method="POST" action="{{ route('admin.subcos.destroy', $subco) }}"
                                          onsubmit="return confirm('Hapus SubCo {{ $subco->nama }}?')">
                                        @csrf @method('DELETE')
                                        <button class="text-red-500 hover:underline text-xs">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-gray-400">
                                Belum ada SubCo. Tambahkan di form kiri.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
