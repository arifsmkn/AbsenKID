<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Subco;
use Illuminate\Http\Request;

class SubcoController extends Controller
{
    public function index()
    {
        $subcos = Subco::orderBy('nama')->get()->map(function ($s) {
            $s->jumlah_karyawan = Employee::where('subco', $s->nama)->count();
            return $s;
        });
        return view('admin.subcos.index', compact('subcos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'       => 'required|string|max:100|unique:subcos,nama',
            'singkatan'  => 'nullable|string|max:20',
        ]);
        Subco::create($data);
        return redirect()->route('admin.subcos.index')->with('success', 'SubCo berhasil ditambahkan.');
    }

    public function edit(Subco $subco)
    {
        return view('admin.subcos.edit', compact('subco'));
    }

    public function update(Request $request, Subco $subco)
    {
        $data = $request->validate([
            'nama'       => 'required|string|max:100|unique:subcos,nama,' . $subco->id,
            'singkatan'  => 'nullable|string|max:20',
            'is_active'  => 'nullable|boolean',
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        // Sync nama subco di tabel employees jika nama berubah
        if ($subco->nama !== $data['nama']) {
            Employee::where('subco', $subco->nama)->update(['subco' => $data['nama']]);
        }

        $subco->update($data);
        return redirect()->route('admin.subcos.index')->with('success', 'SubCo berhasil diperbarui.');
    }

    public function destroy(Subco $subco)
    {
        $count = Employee::where('subco', $subco->nama)->count();
        if ($count > 0) {
            return redirect()->route('admin.subcos.index')
                ->with('error', "Tidak bisa hapus — ada {$count} karyawan dengan SubCo ini.");
        }
        $subco->delete();
        return redirect()->route('admin.subcos.index')->with('success', 'SubCo berhasil dihapus.');
    }
}
