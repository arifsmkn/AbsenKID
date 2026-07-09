@extends('layouts.admin')
@section('title', 'Pemenang Doorprize')
@section('page-title', 'History Pemenang Doorprize')
@section('content')
<div class="flex justify-end mb-4">
    <a href="{{ route('admin.doorprizes.spin') }}" class="btn-primary">🎲 Kocok Doorprize</a>
</div>
<div class="card overflow-hidden p-0">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700 text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-3 text-center">#</th>
                    <th class="px-4 py-3 text-left">Hadiah</th>
                    <th class="px-4 py-3 text-left">NPK</th>
                    <th class="px-4 py-3 text-left">Nama</th>
                    <th class="px-4 py-3 text-left">SubCo</th>
                    <th class="px-4 py-3 text-left">Waktu Menang</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($winners as $i => $w)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-4 py-3 text-center">
                        @if($i === 0) 🥇 @elseif($i === 1) 🥈 @elseif($i === 2) 🥉 @else {{ $i+1 }} @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            @if($w->doorprize->gambar)
                                <img src="{{ $w->doorprize->gambar_url }}" class="h-10 w-10 object-contain rounded">
                            @else
                                <span class="text-2xl">🎁</span>
                            @endif
                            <span class="font-medium">{{ $w->doorprize->nama_hadiah }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 font-mono text-blue-600">{{ $w->employee->npk }}</td>
                    <td class="px-4 py-3 font-medium">{{ $w->employee->nama }}</td>
                    <td class="px-4 py-3"><span class="badge-blue">{{ $w->employee->subco }}</span></td>
                    <td class="px-4 py-3 text-gray-500">{{ $w->won_at->format('H:i:s') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-10 text-gray-400">Belum ada pemenang doorprize</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
