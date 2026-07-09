<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\DoorprizeWinner;
use App\Models\Employee;
use App\Models\Event;
use App\Models\Subco;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session('peserta_npk')) {
            return redirect()->route('peserta.dashboard');
        }
        $event = Event::where('is_active', true)->first();
        $subcos = Subco::where('is_active', true)->orderBy('nama')->get();
        return view('peserta.login', compact('event', 'subcos'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'singkatan' => 'required|string',
            'npk'       => 'required|string',
        ], [
            'singkatan.required' => 'Singkatan perusahaan wajib diisi.',
            'npk.required'       => 'NPK wajib diisi.',
        ]);

        // Cari subco berdasarkan singkatan
        $subco = Subco::where('singkatan', strtoupper(trim($request->singkatan)))
                      ->where('is_active', true)
                      ->first();

        if (!$subco) {
            return back()->withErrors(['singkatan' => 'Singkatan perusahaan tidak ditemukan.'])->withInput();
        }

        // Cari employee berdasarkan NPK dan subco tersebut
        $employee = Employee::where('npk', trim($request->npk))
                            ->where('subco', $subco->nama)
                            ->first();

        if (!$employee) {
            return back()->withErrors(['npk' => 'NPK tidak ditemukan atau tidak sesuai dengan perusahaan.'])->withInput();
        }

        // Login peserta via session
        session([
            'peserta_npk'   => $employee->npk,
            'peserta_nama'  => $employee->nama,
            'peserta_subco' => $employee->subco,
        ]);

        return redirect()->route('peserta.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['peserta_npk', 'peserta_nama', 'peserta_subco']);
        return redirect()->route('peserta.login');
    }

    public function cekDoorprize(Request $request)
    {
        $event    = Event::where('is_active', true)->first();
        $employee = null;
        $winner   = null;

        if ($request->filled('npk')) {
            $employee = Employee::find(trim($request->npk));
            if ($employee && $event) {
                $winner = DoorprizeWinner::with('doorprize')
                    ->where('event_id', $event->id)
                    ->where('employee_npk', $employee->npk)
                    ->first();
            }
        }

        return view('peserta.cek-doorprize', compact('event', 'employee', 'winner'));
    }
}
