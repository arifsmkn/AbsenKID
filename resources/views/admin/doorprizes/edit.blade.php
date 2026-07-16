@extends('layouts.admin')
@section('title', 'Edit Hadiah')
@section('page-title', 'Edit Hadiah — '.$doorprize->nama_hadiah)
@section('content')
<div class="max-w-lg">
    <form method="POST" action="{{ route('admin.doorprizes.update', $doorprize) }}" enctype="multipart/form-data" class="card space-y-4"
          onsubmit="const btn=this.querySelector('button[type=submit]'); btn.disabled=true; btn.textContent='⏳ Menyimpan...';">
        @csrf @method('PUT')

        {{-- Tipe --}}
        <div>
            <label class="label">Tipe Hadiah</label>
            <div class="grid grid-cols-3 gap-3 mt-1">
                <label class="relative flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all
                              {{ old('type', $doorprize->type) === 'doorprize' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-600' }}">
                    <input type="radio" name="type" value="doorprize" class="sr-only" {{ old('type', $doorprize->type) === 'doorprize' ? 'checked' : '' }}>
                    <span class="text-2xl">🎁</span>
                    <div>
                        <p class="font-semibold text-sm">Doorprize</p>
                        <p class="text-xs text-gray-500">Hadiah kecil, bisa banyak</p>
                    </div>
                </label>
                <label class="relative flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all
                              {{ old('type', $doorprize->type) === 'doorprize_utama' ? 'border-slate-400 bg-slate-100 dark:bg-slate-700/30' : 'border-gray-200 dark:border-gray-600' }}">
                    <input type="radio" name="type" value="doorprize_utama" class="sr-only" {{ old('type', $doorprize->type) === 'doorprize_utama' ? 'checked' : '' }}>
                    <span class="text-2xl">🥈</span>
                    <div>
                        <p class="font-semibold text-sm">Doorprize Utama</p>
                        <p class="text-xs text-gray-500">Hadiah menengah, lebih istimewa</p>
                    </div>
                </label>
                <label class="relative flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all
                              {{ old('type', $doorprize->type) === 'grand_prize' ? 'border-yellow-500 bg-yellow-50 dark:bg-yellow-900/20' : 'border-gray-200 dark:border-gray-600' }}">
                    <input type="radio" name="type" value="grand_prize" class="sr-only" {{ old('type', $doorprize->type) === 'grand_prize' ? 'checked' : '' }}>
                    <span class="text-2xl">🏆</span>
                    <div>
                        <p class="font-semibold text-sm">Grand Prize</p>
                        <p class="text-xs text-gray-500">Hadiah utama, tampilkan foto</p>
                    </div>
                </label>
            </div>
        </div>

        @if($doorprize->gambar)
        <div class="flex items-center gap-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
            <img src="{{ $doorprize->gambar_url }}" class="h-20 w-auto object-contain rounded">
            <p class="text-sm text-gray-500">Gambar saat ini</p>
        </div>
        @endif

        <div>
            <label class="label">Nama Hadiah</label>
            <input type="text" name="nama_hadiah" value="{{ old('nama_hadiah', $doorprize->nama_hadiah) }}" class="input w-full" required>
        </div>

        <div>
            <label class="label">Ganti Gambar</label>
            <input type="file" name="gambar" accept="image/*" class="input w-full">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="label">Jumlah / Qty</label>
                <input type="number" name="jumlah" value="{{ old('jumlah', $doorprize->jumlah) }}" min="1" class="input w-full">
                <p class="text-xs text-gray-400 mt-1">Jumlah pemenang yang di-spin</p>
            </div>
            <div>
                <label class="label">Urutan</label>
                <input type="number" name="urutan" value="{{ old('urutan', $doorprize->urutan) }}" class="input w-full">
            </div>
        </div>

        <div class="flex gap-2 pt-2">
            <button type="submit" class="btn-primary">Update</button>
            <a href="{{ route('admin.doorprizes.index') }}" class="btn-secondary">Batal</a>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('input[name="type"]').forEach(radio => {
    radio.addEventListener('change', () => {
        document.querySelectorAll('label[class*="cursor-pointer"]').forEach(label => {
            const input = label.querySelector('input[type="radio"]');
            const colors = {
                doorprize: ['border-blue-500', 'bg-blue-50'],
                doorprize_utama: ['border-slate-400', 'bg-slate-100'],
                grand_prize: ['border-yellow-500', 'bg-yellow-50'],
            };
            if (input.checked) {
                label.classList.add(...colors[input.value]);
                label.classList.remove('border-gray-200', 'dark:border-gray-600');
            } else {
                Object.values(colors).flat().forEach(c => label.classList.remove(c));
                label.classList.add('border-gray-200', 'dark:border-gray-600');
            }
        });
    });
});
</script>
@endsection
