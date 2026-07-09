<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DoorprizeExcludeRole;
use App\Models\Employee;
use Illuminate\Http\Request;

class DoorprizeExcludeRoleController extends Controller
{
    public function index()
    {
        $roles = DoorprizeExcludeRole::orderBy('jabatan')->get();
        $jabatanList = Employee::distinct()->orderBy('jabatan')
            ->whereNotNull('jabatan')->pluck('jabatan');

        return view('admin.doorprize-exclude-roles.index', compact('roles', 'jabatanList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jabatan'     => 'required|string|max:255|unique:doorprize_exclude_roles,jabatan',
            'keterangan'  => 'nullable|string|max:255',
        ]);

        DoorprizeExcludeRole::create($request->only('jabatan', 'keterangan'));

        return back()->with('success', "Jabatan \"{$request->jabatan}\" ditambahkan ke daftar exclude doorprize.");
    }

    public function destroy(DoorprizeExcludeRole $doorprizeExcludeRole)
    {
        $jabatan = $doorprizeExcludeRole->jabatan;
        $doorprizeExcludeRole->delete();
        return back()->with('success', "Jabatan \"{$jabatan}\" dihapus dari daftar exclude.");
    }
}
