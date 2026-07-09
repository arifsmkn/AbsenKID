@extends('layouts.admin')
@section('title', 'Data Absensi')
@section('page-title', 'Data Absensi')
@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
    <div class="card text-center p-3">
        <p class="text-2xl font-bold text-blue-600">{{ number_format($totalInvited) }}</p>
        <p class="text-xs text-gray-500 mt-1">Total Undangan</p>
    </div>
    <div class="card text-center p-3">
        <p class="text-2xl font-bold text-green-600">{{ number_format($totalAttended) }}</p>
        <p class="text-xs text-gray-500 mt-1">Hadir Fisik</p>
    </div>
    <div class="card text-center p-3">
        <p class="text-2xl font-bold text-purple-600">{{ $totalInvited > 0 ? round(($totalAttended/$totalInvited)*100,1) : 0 }}%</p>
        <p class="text-xs text-gray-500 mt-1">Persentase</p>
    </div>
    <div class="card text-center p-3">
        <p class="text-2xl font-bold text-orange-500">{{ number_format($totalConfirmedHadir) }}</p>
        <p class="text-xs text-gray-500 mt-1">Konfirmasi Hadir</p>
    </div>
    <div class="card text-center p-3">
        <p class="text-2xl font-bold text-red-400">{{ number_format($totalConfirmedTidak) }}</p>
        <p class="text-xs text-gray-500 mt-1">Konfirmasi Tidak</p>
    </div>
</div>

{{-- Manual tambah hadir --}}
<div class="card mb-4 p-4" x-data="{ open: false }">
    <button @click="open = !open" class="flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-800">
        ➕ Tambah Kehadiran Manual
        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open" x-cloak class="mt-3 pt-3 border-t border-gray-100">
        @if(session('success'))
        <div class="mb-3 p-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="mb-3 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">❌ {{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('admin.attendances.manual') }}" class="flex gap-2">
            @csrf
            <input type="text" name="npk" placeholder="Masukkan NPK peserta..." class="input flex-1" required>
            <button class="btn-primary">Tandai Hadir</button>
        </form>
        <p class="text-xs text-gray-400 mt-1">Peserta akan ditandai hadir fisik tanpa scan QR. Notifikasi TV akan aktif.</p>
    </div>
</div>

{{-- Flash messages --}}
@if(session('success') && !request()->has('_flash_consumed'))
<div class="mb-3 p-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">✅ {{ session('success') }}</div>
@endif

<form method="GET" class="flex gap-2 mb-4">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NPK, Nama, SubCo..." class="input flex-1">
    <button class="btn-primary">Cari</button>
    <a href="{{ route('liveabsensi') }}" target="_blank" class="btn-secondary">🔗 Publik</a>
</form>

<div class="card overflow-hidden p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 text-left">NPK</th>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">SubCo</th>
                    <th class="px-4 py-3 text-left">Jabatan</th>
                    <th class="px-4 py-3 text-left">Waktu</th>
                    <th class="px-4 py-3 text-left">Sumber</th>
                    <th class="px-4 py-3 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($attendances as $att)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 font-mono text-blue-600 text-xs">{{ $att->employee->npk }}</td>
                    <td class="px-4 py-3 font-medium">{{ $att->employee->nama }}</td>
                    <td class="px-4 py-3"><span class="badge-blue">{{ $att->employee->subco }}</span></td>
                    <td class="px-4 py-3 text-gray-500 text-xs">{{ $att->employee->jabatan }}</td>
                    <td class="px-4 py-3 text-gray-500 text-xs font-mono">{{ $att->scanned_at->format('H:i:s') }}</td>
                    <td class="px-4 py-3">
                        @if($att->source === 'manual_admin')
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Manual</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Scan QR</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        @role('admin')
                        <form method="POST" action="{{ route('admin.attendances.destroy', $att) }}"
                              onsubmit="return confirm('Hapus kehadiran {{ $att->employee->nama }}?')">
                            @csrf @method('DELETE')
                            <button class="text-xs text-red-500 hover:text-red-700">Hapus</button>
                        </form>
                        @endrole
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-10 text-gray-400">Belum ada data absensi fisik</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">{{ $attendances->links() }}</div>
</div>

{{-- Peserta konfirmasi "Tidak Hadir" — bisa diubah jadi hadir --}}
<div class="card mt-4 p-4" x-data="{ open: {{ $tidakHadirList->count() ? 'true' : 'false' }} }">
    <button @click="open = !open" class="flex items-center gap-2 text-sm font-semibold text-orange-600 hover:text-orange-800">
        🔄 Peserta Konfirmasi "Tidak Hadir" ({{ $tidakHadirList->count() }})
        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>
    <div x-show="open" x-cloak class="mt-3 pt-3 border-t border-gray-100">
        <p class="text-xs text-gray-400 mb-3">Peserta yang konfirmasi tidak hadir di portal. Klik "Ubah ke Hadir" jika ternyata mereka berubah pikiran — ini hanya mengubah status konfirmasi, bukan kehadiran fisik (tetap harus scan QR saat datang).</p>
        @if($tidakHadirList->isEmpty())
            <p class="text-sm text-gray-400 text-center py-4">Tidak ada peserta yang konfirmasi tidak hadir.</p>
        @else
        <div class="space-y-2 max-h-72 overflow-y-auto">
            @foreach($tidakHadirList as $conf)
            <div class="flex items-center justify-between gap-3 p-2.5 rounded-lg bg-orange-50 dark:bg-orange-900/10 border border-orange-100 dark:border-orange-900/20">
                <div class="min-w-0">
                    <p class="text-sm font-medium truncate">{{ $conf->employee->nama ?? $conf->employee_npk }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $conf->employee->subco ?? '-' }} · {{ $conf->employee_npk }}</p>
                </div>
                <form method="POST" action="{{ route('admin.attendances.konfirmasi') }}" class="shrink-0">
                    @csrf
                    <input type="hidden" name="npk" value="{{ $conf->employee_npk }}">
                    <input type="hidden" name="status" value="hadir">
                    <button class="text-xs px-3 py-1.5 rounded-lg font-medium text-white bg-green-600 hover:bg-green-700 transition">
                        ✅ Ubah ke Hadir
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @endif

        <form method="POST" action="{{ route('admin.attendances.konfirmasi') }}" class="flex gap-2 mt-4 pt-3 border-t border-gray-100">
            @csrf
            <input type="text" name="npk" placeholder="Atau ketik NPK manual..." class="input flex-1" required>
            <select name="status" class="input w-40" required>
                <option value="hadir">Hadir</option>
                <option value="tidak_hadir">Tidak Hadir</option>
            </select>
            <button class="btn-primary">Update</button>
        </form>
    </div>
</div>

@endsection
