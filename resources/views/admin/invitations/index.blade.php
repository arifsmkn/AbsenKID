@extends('layouts.admin')
@section('title', 'Undangan')
@section('page-title', 'Manajemen Undangan')
@section('content')

{{-- ── Stats pengiriman ── --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
    <div class="card p-4 text-center">
        <p class="text-2xl font-black text-green-500">{{ $sentWa }}</p>
        <p class="text-xs text-brand-slate mt-1">💬 WA Terkirim</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-2xl font-black text-brand-red">{{ $failWa }}</p>
        <p class="text-xs text-brand-slate mt-1">💬 WA Gagal</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-2xl font-black text-green-500">{{ $sentEmail }}</p>
        <p class="text-xs text-brand-slate mt-1">📧 Email Terkirim</p>
    </div>
    <div class="card p-4 text-center">
        <p class="text-2xl font-black text-brand-red">{{ $failEmail }}</p>
        <p class="text-xs text-brand-slate mt-1">📧 Email Gagal</p>
    </div>
</div>

@if(auth()->user()->hasRole('admin'))
<div class="card mb-4 space-y-4">
    {{-- Generate --}}
    <div class="flex gap-3 items-center flex-wrap">
        <form method="POST" action="{{ route('admin.invitations.generate', $event) }}"
              onsubmit="return confirm('Generate QR untuk semua karyawan yang belum punya undangan?')">
            @csrf
            <button class="btn-primary">🎫 Generate Semua Undangan</button>
        </form>
        <span class="text-sm text-brand-slate">Total undangan: <strong>{{ $invitations->total() }}</strong></span>
        <a href="{{ route('admin.invitations.sendHistory') }}" class="btn-secondary ml-auto">📋 History Pengiriman</a>
    </div>

    {{-- Kirim Semua --}}
    <div class="border-t border-brand-slate/20 pt-4">
        <p class="text-sm font-semibold text-brand-navy dark:text-white mb-3">📤 Kirim Undangan Massal</p>
        <div class="flex gap-2 flex-wrap">
            <form method="POST" action="{{ route('admin.invitations.sendAll') }}"
                  onsubmit="return confirm('Kirim WA ke semua peserta yang belum dapat?')">
                @csrf
                <input type="hidden" name="channel" value="wa">
                <button class="btn-primary" style="background:#25D366">💬 Kirim Semua via WA</button>
            </form>
            <form method="POST" action="{{ route('admin.invitations.sendAll') }}"
                  onsubmit="return confirm('Kirim Email ke semua peserta yang belum dapat?')">
                @csrf
                <input type="hidden" name="channel" value="email">
                <button class="btn-primary">📧 Kirim Semua via Email</button>
            </form>
            <form method="POST" action="{{ route('admin.invitations.sendAll') }}"
                  onsubmit="return confirm('Kirim WA + Email ke semua peserta yang belum dapat?')">
                @csrf
                <input type="hidden" name="channel" value="both">
                <button class="btn-secondary">📤 Kirim WA + Email</button>
            </form>
        </div>
        <p class="text-xs text-brand-slate mt-2">Hanya dikirim ke peserta yang belum berhasil menerima. Pastikan WA/Email diaktifkan di <a href="{{ route('admin.settings.index') }}" class="text-brand-navy underline">Pengaturan</a>.</p>
    </div>

    {{-- Jalan pintas konfirmasi --}}
    <div class="border-t border-brand-slate/20 pt-4">
        <p class="text-sm font-semibold text-brand-navy dark:text-white mb-3">⚡ Jalan Pintas Konfirmasi — Semua SubCo</p>
        <div class="flex gap-2 flex-wrap">
            <form method="POST" action="{{ route('admin.invitations.confirmAll') }}"
                  onsubmit="return confirm('Tandai SEMUA peserta yang belum konfirmasi sebagai \'Akan Hadir\'?\n\nPeserta yang sudah konfirmasi (apa pun statusnya) TIDAK akan diubah/ditimpa.')">
                @csrf
                <input type="hidden" name="status" value="hadir">
                <button class="btn-primary" style="background:#16a34a">✅ Konfirmasi Semua jadi Hadir</button>
            </form>
        </div>
        <p class="text-xs text-brand-slate mt-2">Hanya berlaku untuk peserta yang belum pernah konfirmasi sama sekali. Peserta yang sudah pernah konfirmasi (status apa pun) tidak akan diubah.</p>
    </div>

    {{-- Jalan pintas per SubCo --}}
    @if($subcoShortcuts->isNotEmpty())
    <div class="border-t border-brand-slate/20 pt-4">
        <p class="text-sm font-semibold text-brand-navy dark:text-white mb-3">⚡ Jalan Pintas — per SubCo</p>
        <div class="space-y-2">
            @foreach($subcoShortcuts as $row)
            <div class="flex items-center justify-between gap-3 p-2.5 rounded-lg bg-brand-cream/40 dark:bg-gray-700/30 flex-wrap">
                <div class="min-w-0">
                    <p class="text-sm font-medium truncate">{{ $row->subco }}</p>
                    <p class="text-xs text-brand-slate">
                        {{ $row->belum_konfirmasi }} belum konfirmasi · {{ $row->belum_hadir }} belum hadir
                    </p>
                </div>
                <div class="flex gap-2 shrink-0 flex-wrap">
                    @if($row->belum_konfirmasi > 0)
                    <form method="POST" action="{{ route('admin.invitations.confirmAll') }}"
                          onsubmit="return confirm('Tandai semua peserta {{ addslashes($row->subco) }} yang belum konfirmasi sebagai \'Akan Hadir\'?')">
                        @csrf
                        <input type="hidden" name="status" value="hadir">
                        <input type="hidden" name="subco" value="{{ $row->subco }}">
                        <button class="text-xs px-3 py-1.5 rounded-lg font-medium text-white" style="background:#16a34a">✅ Konfirmasi Hadir</button>
                    </form>
                    <form method="POST" action="{{ route('admin.invitations.confirmAll') }}"
                          onsubmit="return confirm('Tandai semua peserta {{ addslashes($row->subco) }} yang belum konfirmasi sebagai \'Tidak Hadir\'?')">
                        @csrf
                        <input type="hidden" name="status" value="tidak_hadir">
                        <input type="hidden" name="subco" value="{{ $row->subco }}">
                        <button class="text-xs px-3 py-1.5 rounded-lg font-medium" style="background:rgba(208,63,66,0.1); color:#D03F42; border:1px solid rgba(208,63,66,0.3)">🚫 Konfirmasi Tidak Hadir</button>
                    </form>
                    @endif
                    @if($row->belum_hadir > 0)
                    <form method="POST" action="{{ route('admin.attendances.manualSubco') }}"
                          onsubmit="return confirm('Tandai HADIR LANGSUNG (tanpa scan) untuk semua peserta {{ addslashes($row->subco) }} yang belum hadir?\n\nIni akan langsung membuat catatan kehadiran fisik untuk {{ $row->belum_hadir }} peserta.')">
                        @csrf
                        <input type="hidden" name="subco" value="{{ $row->subco }}">
                        <button class="text-xs px-3 py-1.5 rounded-lg font-medium text-white" style="background:#244C6B">🎟️ Hadir Langsung</button>
                    </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif

{{-- Filter + Search --}}
<form method="GET" class="flex gap-2 mb-4 flex-wrap">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari NPK atau Nama..." class="input flex-1 min-w-40">
    <input type="text" name="subco" value="{{ request('subco') }}" placeholder="Filter SubCo..." class="input w-40">
    <select name="filter" class="input">
        <option value="">Semua</option>
        <option value="belum_hadir" {{ request('filter')==='belum_hadir'?'selected':'' }}>⏳ Belum Hadir</option>
        <option value="konfirmasi_hadir" {{ request('filter')==='konfirmasi_hadir'?'selected':'' }}>📝 Konfirmasi Akan Hadir</option>
        <option value="konfirmasi_tidak_hadir" {{ request('filter')==='konfirmasi_tidak_hadir'?'selected':'' }}>🚫 Konfirmasi Tidak Hadir</option>
        <option value="belum_konfirmasi" {{ request('filter')==='belum_konfirmasi'?'selected':'' }}>❔ Belum Konfirmasi</option>
        <option value="no_wa"    {{ request('filter')==='no_wa'?'selected':'' }}>❌ No. WA Kosong</option>
        <option value="no_email" {{ request('filter')==='no_email'?'selected':'' }}>❌ Email Kosong</option>
    </select>
    <button class="btn-primary">Cari</button>
    @if(request('search') || request('filter') || request('subco'))
        <a href="{{ route('admin.invitations.index') }}" class="btn-secondary">Reset</a>
    @endif
</form>

<div class="card overflow-hidden p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="text-xs uppercase tracking-wide" style="background:rgba(36,76,107,0.06)">
                <tr>
                    <th class="px-4 py-3 text-left text-brand-slate">NPK</th>
                    <th class="px-4 py-3 text-left text-brand-slate">Nama</th>
                    <th class="px-4 py-3 text-left text-brand-slate">SubCo</th>
                    <th class="px-4 py-3 text-center text-brand-slate">Konfirmasi</th>
                    <th class="px-4 py-3 text-center text-brand-slate">Hadir</th>
                    <th class="px-4 py-3 text-center text-brand-slate">WA</th>
                    <th class="px-4 py-3 text-center text-brand-slate">Email</th>
                    <th class="px-4 py-3 text-center text-brand-slate">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-slate/10 dark:divide-gray-700">
                @forelse($invitations as $inv)
                @php
                    $waSend    = $inv->sends->firstWhere('channel','whatsapp');
                    $emailSend = $inv->sends->firstWhere('channel','email');
                @endphp
                <tr class="hover:bg-brand-cream/50 dark:hover:bg-gray-700/30">
                    <td class="px-4 py-3 font-mono text-xs" style="color:#244C6B">{{ $inv->employee->npk }}</td>
                    <td class="px-4 py-3 font-medium">{{ $inv->employee->nama }}</td>
                    <td class="px-4 py-3"><span class="badge-blue">{{ $inv->employee->subco }}</span></td>
                    @php $konfirmasi = $confirmations[$inv->employee_npk] ?? null; @endphp
                    <td class="px-4 py-3 text-center">
                        @if($konfirmasi === 'hadir')
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background:rgba(34,197,94,0.12); color:#16a34a">📝 Akan Hadir</span>
                        @elseif($konfirmasi === 'tidak_hadir')
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium" style="background:rgba(208,63,66,0.1); color:#D03F42">🚫 Tidak Hadir</span>
                        @else
                            <span class="badge-gray text-xs">Belum Konfirmasi</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($inv->attendance)
                            <span class="badge-green">✅ Hadir</span>
                        @else
                            <span class="badge-gray">Belum</span>
                        @endif
                    </td>

                    {{-- Status WA --}}
                    <td class="px-4 py-3 text-center">
                        @if($waSend?->status === 'sent')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-600">✅ Terkirim</span>
                        @elseif($waSend?->status === 'failed')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-brand-red" title="{{ $waSend->error_message }}">❌ Gagal</span>
                        @elseif(!$inv->employee->no_telpon)
                            <span class="text-xs text-brand-slate">— kosong</span>
                        @else
                            <span class="text-xs text-brand-slate">Belum</span>
                        @endif
                    </td>

                    {{-- Status Email --}}
                    <td class="px-4 py-3 text-center">
                        @if($emailSend?->status === 'sent')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-600">✅ Terkirim</span>
                        @elseif($emailSend?->status === 'failed')
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-brand-red" title="{{ $emailSend->error_message }}">❌ Gagal</span>
                        @elseif(!$inv->employee->email)
                            <span class="text-xs text-brand-slate">— kosong</span>
                        @else
                            <span class="text-xs text-brand-slate">Belum</span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('admin.invitations.qr', $inv) }}" target="_blank"
                               class="text-xs px-2 py-1 rounded text-brand-navy border border-brand-slate/30 hover:bg-brand-cream transition">QR</a>
                            @if(auth()->user()->hasRole('admin'))
                            <form method="POST" action="{{ route('admin.invitations.sendOne', $inv) }}">
                                @csrf
                                <button class="text-xs px-2 py-1 rounded text-white transition"
                                        style="background:#244C6B"
                                        onclick="return confirm('Kirim undangan ke {{ addslashes($inv->employee->nama) }}?')">
                                    📤 Kirim
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-10 text-brand-slate">
                        Belum ada undangan. <a href="#" onclick="document.querySelector('form[action*=generate]').submit()" class="text-brand-navy underline">Generate sekarang</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-brand-slate/10">{{ $invitations->links() }}</div>
</div>
@endsection
