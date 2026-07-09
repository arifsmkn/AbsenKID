@extends('layouts.admin')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard Live Monitoring')

@section('content')
@if(!$event)
    <div class="text-center py-20">
        <p class="text-4xl mb-4">📅</p>
        <h2 class="text-xl font-semibold mb-2">Belum ada event aktif</h2>
        <a href="{{ route('admin.events.create') }}" class="btn-primary">Buat Event Sekarang</a>
    </div>
@else
{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
    <div class="card-compact">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Undangan</p>
                <p class="text-xl font-bold text-blue-600 mt-0.5">{{ number_format($totalInvited) }}</p>
            </div>
            <span class="text-2xl opacity-20">🎫</span>
        </div>
    </div>
    <div class="card-compact">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 uppercase tracking-wide">Sudah Hadir</p>
                <p class="text-xl font-bold text-green-600 mt-0.5" id="total-hadir">{{ number_format($totalAttended) }}</p>
            </div>
            <span class="text-2xl opacity-20">✅</span>
        </div>
    </div>
    <div class="card-compact">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 uppercase tracking-wide">Belum Hadir</p>
                <p class="text-xl font-bold text-orange-500 mt-0.5" id="total-belum">{{ number_format($totalInvited - $totalAttended) }}</p>
            </div>
            <span class="text-2xl opacity-20">⏳</span>
        </div>
    </div>
    <div class="card-compact">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 uppercase tracking-wide">Persentase</p>
                <p class="text-xl font-bold text-purple-600 mt-0.5" id="percentage">{{ $percentage }}%</p>
            </div>
            <span class="text-2xl opacity-20">📊</span>
        </div>
    </div>
</div>

{{-- Konfirmasi (info dari portal peserta — bukan kehadiran fisik) --}}
<div class="grid grid-cols-2 gap-3 mb-6">
    <div class="card-compact">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 uppercase tracking-wide">Konfirmasi Akan Hadir</p>
                <p class="text-xl font-bold text-emerald-500 mt-0.5">{{ number_format($totalConfirmedHadir) }}</p>
            </div>
            <span class="text-2xl opacity-20">📝</span>
        </div>
    </div>
    <div class="card-compact">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-[11px] text-gray-500 dark:text-gray-400 uppercase tracking-wide">Konfirmasi Tidak Hadir</p>
                <p class="text-xl font-bold text-red-400 mt-0.5">{{ number_format($totalConfirmedTidak) }}</p>
            </div>
            <span class="text-2xl opacity-20">🚫</span>
        </div>
    </div>
    <p class="col-span-2 text-xs text-gray-400 -mt-2">ℹ️ Berdasarkan konfirmasi peserta di portal undangan — bukan kehadiran fisik (lihat "Sudah Hadir" di atas untuk data scan aktual).</p>
</div>

{{-- Progress Bar --}}
<div class="card mb-6">
    <div class="flex items-center justify-between mb-2">
        <h3 class="font-semibold">Progress Kehadiran — {{ $event->nama }}</h3>
        <span class="text-sm text-gray-500">{{ $event->tanggal?->format('d M Y') }}</span>
    </div>
    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-4 rounded-full transition-all duration-1000"
             id="progress-bar" style="width: {{ $percentage }}%"></div>
    </div>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $totalAttended }} dari {{ $totalInvited }} tamu hadir ({{ $percentage }}%)</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Subco Stats --}}
    <div class="card">
        <h3 class="font-semibold mb-4">Kehadiran per SubCo</h3>
        <div class="space-y-3 max-h-80 overflow-y-auto" id="subco-list">
            @foreach($subcoStats as $stat)
            @php $inv = $invitationBySubco[$stat->subco] ?? 1; $pct = round(($stat->hadir/$inv)*100); @endphp
            <div>
                <div class="flex justify-between items-center text-sm mb-1 gap-2">
                    <span class="font-medium truncate max-w-[160px] flex items-center gap-1.5">
                        @if($pct < 100)
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500 shrink-0" title="Belum 100% hadir"></span>
                        @endif
                        {{ $stat->subco }}
                    </span>
                    <span class="flex items-center gap-2 shrink-0">
                        <span class="text-gray-500">{{ $stat->hadir }}/{{ $inv }} ({{ $pct }}%)</span>
                        @if($pct < 100)
                            <a href="{{ route('admin.invitations.index', ['subco' => $stat->subco, 'filter' => 'belum_hadir']) }}"
                               class="text-xs px-2 py-0.5 rounded-full font-medium transition-colors"
                               style="background:rgba(249,115,22,0.12); color:#ea580c"
                               title="Lihat peserta {{ $stat->subco }} yang belum hadir">
                                Lihat →
                            </a>
                        @endif
                    </span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="h-2 rounded-full {{ $pct < 100 ? 'bg-orange-400' : 'bg-green-500' }}" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endforeach
            @if($subcoStats->isEmpty())
                <p class="text-center text-gray-400 py-6 text-sm">Belum ada data kehadiran</p>
            @endif
        </div>
    </div>

    {{-- Recent scan --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold">Scan Terbaru</h3>
            <span class="flex items-center gap-1 text-xs text-green-500">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Live
            </span>
        </div>
        <div class="space-y-2 max-h-80 overflow-y-auto" id="recent-list">
            @foreach($recentAttendances as $att)
            <div class="flex items-center gap-3 p-2 rounded-lg bg-gray-50 dark:bg-gray-700/50">
                <div class="w-9 h-9 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center text-sm font-bold text-blue-700 dark:text-blue-300 shrink-0">
                    {{ strtoupper(substr($att->employee->nama, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium truncate">{{ $att->employee->nama }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $att->employee->subco }} · {{ $att->employee->npk }}</p>
                </div>
                <span class="text-xs text-gray-400 shrink-0">{{ $att->scanned_at->format('H:i') }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
// Poll every 10 seconds for live updates
function refreshDashboard() {
    fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text()).then(() => {}).catch(() => {});
}
// Simple auto-refresh setiap 15 detik
setTimeout(() => location.reload(), 15000);
</script>
@endpush
