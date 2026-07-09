<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name'         => 'required|string|max:100',
            'app_mode'         => 'required|in:dark,light',
            'panitia_nama'     => 'nullable|string|max:100',
            'panitia_whatsapp' => 'nullable|string|max:20',
            'panitia_email'    => 'nullable|email|max:100',
        ]);

        foreach ($request->except(['_token', '_method']) as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
