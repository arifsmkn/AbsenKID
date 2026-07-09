<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderByDesc('tahun')->get();
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required|string',
            'tahun' => 'required|integer',
            'tema' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'lokasi' => 'nullable|string',
            'tanggal' => 'nullable|date',
            'waktu_mulai' => 'nullable',
            'waktu_selesai' => 'nullable',
            'maps_embed' => 'nullable|string',
            'maps_url' => 'nullable|url|max:500',
            'logo' => 'nullable|image|max:2048',
            'wallpaper' => 'nullable|image|max:5120',
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'mode' => 'nullable|in:dark,light',
        ]);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('wallpaper')) {
            $data['wallpaper'] = $request->file('wallpaper')->store('wallpapers', 'public');
        }

        $data['theme_config'] = [
            'primary_color' => $data['primary_color'] ?? '#1e40af',
            'secondary_color' => $data['secondary_color'] ?? '#7c3aed',
            'mode' => $data['mode'] ?? 'dark',
        ];

        unset($data['primary_color'], $data['secondary_color'], $data['mode']);

        Event::create($data);

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dibuat.');
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'nama' => 'required|string',
            'tahun' => 'required|integer',
            'tema' => 'nullable|string',
            'deskripsi' => 'nullable|string',
            'lokasi' => 'nullable|string',
            'tanggal' => 'nullable|date',
            'waktu_mulai' => 'nullable',
            'waktu_selesai' => 'nullable',
            'maps_embed' => 'nullable|string',
            'maps_url' => 'nullable|url|max:500',
            'logo' => 'nullable|image|max:2048',
            'wallpaper' => 'nullable|image|max:5120',
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'mode' => 'nullable|in:dark,light',
        ]);

        if ($request->hasFile('logo')) {
            if ($event->logo) Storage::disk('public')->delete($event->logo);
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('wallpaper')) {
            if ($event->wallpaper) Storage::disk('public')->delete($event->wallpaper);
            $data['wallpaper'] = $request->file('wallpaper')->store('wallpapers', 'public');
        }

        $data['theme_config'] = [
            'primary_color' => $data['primary_color'] ?? $event->theme_config['primary_color'] ?? '#1e40af',
            'secondary_color' => $data['secondary_color'] ?? $event->theme_config['secondary_color'] ?? '#7c3aed',
            'mode' => $data['mode'] ?? $event->theme_config['mode'] ?? 'dark',
        ];

        unset($data['primary_color'], $data['secondary_color'], $data['mode']);

        $event->update($data);

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil diperbarui.');
    }

    public function destroy(Event $event)
    {
        if ($event->logo) Storage::disk('public')->delete($event->logo);
        if ($event->wallpaper) Storage::disk('public')->delete($event->wallpaper);
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dihapus.');
    }

    public function activate(Event $event)
    {
        Event::where('is_active', true)->update(['is_active' => false]);
        $event->update(['is_active' => true]);
        return redirect()->route('admin.events.index')->with('success', "Event '{$event->nama}' sekarang aktif.");
    }
}
