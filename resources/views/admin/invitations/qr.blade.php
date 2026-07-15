<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>QR — {{ $invitation->employee->nama }}</title>
    @vite(['resources/css/app.css'])
    <style>body{font-family:sans-serif;}</style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-50 p-8">
<div class="max-w-sm w-full bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-6 text-white text-center">
        <h1 class="text-lg font-bold">Konvensi Improvement Dharma</h1>
        <p class="text-sm opacity-80">Undangan Resmi</p>
    </div>
    <div class="p-6 text-center">
        <div class="flex justify-center mb-4">
            {!! $qr !!}
        </div>
        <h2 class="text-xl font-bold">{{ $invitation->employee->nama }}</h2>
        <p class="text-gray-500 text-sm">NPK: {{ $invitation->employee->npk }}</p>
        <p class="text-gray-500 text-sm">{{ $invitation->employee->subco }}</p>
        <div class="mt-4 p-3 bg-gray-50 rounded-xl text-xs text-gray-400 font-mono break-all">
            {{ $invitation->qr_code }}
        </div>
    </div>
    <div class="px-6 pb-6 flex gap-2 justify-center">
        <button onclick="window.print()" class="btn-primary text-sm">🖨️ Print</button>
        <a href="{{ route('admin.invitations.index') }}" class="btn-secondary text-sm">← Kembali</a>
    </div>
</div>
</body>
</html>
