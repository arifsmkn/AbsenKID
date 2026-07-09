<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SlideController extends Controller
{
    public function index(Event $event)
    {
        $slides = $event->slides;
        return view('admin.slides.index', compact('event', 'slides'));
    }

    public function create(Event $event)
    {
        return view('admin.slides.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'type' => 'required|in:image,video',
            'file' => 'required|file|max:51200',
            'judul' => 'nullable|string|max:100',
            'caption' => 'nullable|string',
            'urutan' => 'nullable|integer',
        ]);

        $path = $request->file('file')->store('slides', 'public');

        $event->slides()->create([
            'type' => $request->type,
            'file_path' => $path,
            'judul' => $request->judul,
            'caption' => $request->caption,
            'urutan' => $request->urutan ?? $event->slides()->count(),
        ]);

        return redirect()->route('admin.events.slides.index', $event)->with('success', 'Slide berhasil ditambahkan.');
    }

    public function edit(Event $event, Slide $slide)
    {
        return view('admin.slides.edit', compact('event', 'slide'));
    }

    public function update(Request $request, Event $event, Slide $slide)
    {
        $request->validate([
            'judul' => 'nullable|string|max:100',
            'caption' => 'nullable|string',
            'urutan' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'file' => 'nullable|file|max:51200',
        ]);

        $data = $request->only(['judul', 'caption', 'urutan']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('file')) {
            Storage::disk('public')->delete($slide->file_path);
            $data['file_path'] = $request->file('file')->store('slides', 'public');
            $data['type'] = $request->type ?? $slide->type;
        }

        $slide->update($data);

        return redirect()->route('admin.events.slides.index', $event)->with('success', 'Slide berhasil diperbarui.');
    }

    public function destroy(Event $event, Slide $slide)
    {
        Storage::disk('public')->delete($slide->file_path);
        $slide->delete();
        return redirect()->route('admin.events.slides.index', $event)->with('success', 'Slide berhasil dihapus.');
    }
}
