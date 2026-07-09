@extends('layouts.admin')
@section('title', 'History Pengiriman')
@section('page-title', 'History Pengiriman Undangan')
@section('content')

{{-- ── Stats Ringkasan ── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
    <div class="card p-4 text-center">
        <p class="text-3xl font-black text-green-500">{{ $stats['wa_sent'] }}</p>
        <p class="text-xs text-brand-slate mt-1">💬 WA Berhasil</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-3xl font-black text-brand-red">{{ $stats['wa_failed'] }}</p>
        <p class="text-xs text-brand-slate mt-1">💬 WA Gagal</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-3xl font-black text-green-500">{{ $stats['email_sent'] }}</p>
        <p class="text-xs text-brand-slate mt-1">📧 Email Berhasil</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-3xl font-black text-brand-red">{{ $stats['email_failed'] }}</p>
        <p class="text-xs text-brand-slate mt-1">📧 Email Gagal</p>
    </div>
</div>

{{-- ── Filter ── --}}
<form method="GET" class="flex gap-2 mb-4 flex-wrap">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, NPK, no WA, email..." class="input flex-1 min-w-40">
    <select name="channel" class="input">
        <option value="all"       {{ request('channel','all')==='all'?'selected':'' }}>Semua Channel</option>
        <option value="whatsapp"  {{ request('channel')==='whatsapp'?'selected':'' }}>💬 WhatsApp</option>
        <option value="email"     {{ request('channel')==='email'?'selected':'' }}>📧 Email</option>
    </select>
    <select name="status" class="input">
        <option value="all"    {{ request('status','all')==='all'?'selected':'' }}>Semua Status</option>
        <option value="sent"   {{ request('status')==='sent'?'selected':'' }}>✅ Berhasil</option>
        <option value="failed" {{ request('status')==='failed'?'selected':'' }}>❌ Gagal</option>
        <option value="pending"{{ request('status')==='pending'?'selected':'' }}>⏳ Pending</option>
    </select>
    <button class="btn-primary">Filter</button>
    <a href="{{ route('admin.invitations.sendHistory') }}" class="btn-secondary">Reset</a>
    <a href="{{ route('admin.invitations.index') }}" class="btn-secondary ml-auto">← Kembali</a>
</form>

<div class="card overflow-hidden p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-xs uppercase tracking-wide" style="background:rgba(36,76,107,0.06)">
                <tr>
                    <th class="px-4 py-3 text-left text-brand-slate">Peserta</th>
                    <th class="px-4 py-3 text-left text-brand-slate">Channel</th>
                    <th class="px-4 py-3 text-left text-brand-slate">Tujuan</th>
                    <th class="px-4 py-3 text-center text-brand-slate">Status</th>
                    <th class="px-4 py-3 text-left text-brand-slate">Waktu</th>
                    <th class="px-4 py-3 text-left text-brand-slate">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-slate/10 dark:divide-gray-700">
                @forelse($sends as $s)
                <tr class="hover:bg-brand-cream/50 dark:hover:bg-gray-700/30">
                    <td class="px-4 py-3">
                        <p class="font-semibold text-sm">{{ $s->invitation?->employee?->nama ?? '—' }}</p>
                        <p class="font-mono text-xs text-brand-slate">{{ $s->employee_npk }}</p>
                    </td>
                    <td class="px-4 py-3">
                        @if($s->channel === 'whatsapp')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-full bg-green-100 text-green-700">💬 WhatsApp</span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-1 rounded-full bg-blue-100 text-blue-700">📧 Email</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-mono text-xs text-brand-steel">{{ $s->target }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($s->status === 'sent')
                            <span class="badge-green">✅ Berhasil</span>
                        @elseif($s->status === 'failed')
                            <span class="badge-red">❌ Gagal</span>
                        @else
                            <span class="badge-gray">⏳ Pending</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-brand-slate whitespace-nowrap">
                        {{ $s->sent_at?->format('d/m H:i') ?? $s->updated_at->format('d/m H:i') }}
                    </td>
                    <td class="px-4 py-3 text-xs text-brand-red max-w-xs truncate" title="{{ $s->error_message }}">
                        {{ $s->error_message ?? '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-12 text-brand-slate">
                        <p class="text-4xl mb-3">📭</p>
                        <p>Belum ada history pengiriman.</p>
                        <p class="text-xs mt-1">Kirim undangan dari halaman <a href="{{ route('admin.invitations.index') }}" class="text-brand-navy underline">Undangan</a>.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-brand-slate/10">{{ $sends->links() }}</div>
</div>
@endsection
