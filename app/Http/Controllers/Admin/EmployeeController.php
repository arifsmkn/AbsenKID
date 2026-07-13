<?php

namespace App\Http\Controllers\Admin;

use App\Exports\EmployeesExport;
use App\Exports\EmployeeTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\EmployeesImport;
use App\Models\Attendance;
use App\Models\AttendanceConfirmation;
use App\Models\DoorprizeWinner;
use App\Models\Employee;
use App\Models\Invitation;
use App\Models\InvitationSend;
use App\Models\ScanNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('npk', 'like', "%{$request->search}%")
                    ->orWhere('nama', 'like', "%{$request->search}%")
                    ->orWhere('subco', 'like', "%{$request->search}%");
            });
        }

        if ($request->subco) {
            $query->where('subco', $request->subco);
        }

        $employees = $query->paginate(20)->withQueryString();
        $subcos = \App\Models\Subco::where('is_active', true)->orderBy('nama')->pluck('nama');

        return view('admin.employees.index', compact('employees', 'subcos'));
    }

    public function create()
    {
        $subcos = \App\Models\Subco::where('is_active', true)->orderBy('nama')->pluck('nama');
        return view('admin.employees.create', compact('subcos'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'npk' => 'required|string|max:20|unique:employees,npk',
            'nama' => 'required|string|max:100',
            'subco' => 'required|string|max:100',
            'jabatan' => 'required|string|max:100',
            'ukuran_baju' => 'nullable|string|max:5',
            'email' => 'nullable|email|max:100',
            'no_telpon' => 'nullable|string|max:20',
        ]);

        Employee::create($data);

        return redirect()->route('admin.employees.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function edit(Employee $employee)
    {
        $subcos = \App\Models\Subco::where('is_active', true)->orderBy('nama')->pluck('nama');
        return view('admin.employees.edit', compact('employee', 'subcos'));
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'subco' => 'required|string|max:100',
            'jabatan' => 'required|string|max:100',
            'ukuran_baju' => 'nullable|string|max:5',
            'email' => 'nullable|email|max:100',
            'no_telpon' => 'nullable|string|max:20',
        ]);

        $employee->update($data);

        return redirect()->route('admin.employees.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        $hasRelatedData = $employee->invitations()->exists()
            || $employee->attendances()->exists()
            || $employee->confirmations()->exists()
            || $employee->doorprizeWins()->exists();

        if ($hasRelatedData) {
            return redirect()->route('admin.employees.index')
                ->with('error', "Tidak bisa hapus {$employee->nama} — karyawan ini sudah punya data terkait (undangan/kehadiran/konfirmasi/pemenang doorprize). Data tersebut dipertahankan sebagai riwayat event.");
        }

        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Karyawan berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);

        try {
            Excel::import(new EmployeesImport, $request->file('file'));
            return redirect()->route('admin.employees.index')->with('success', 'Data berhasil diimport.');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $errors = collect($e->failures())
                ->map(fn($f) => "Baris {$f->row()}: " . implode(', ', $f->errors()))
                ->take(5)
                ->implode(' | ');
            return redirect()->route('admin.employees.index')->with('error', "Gagal import — periksa format file: {$errors}");
        } catch (\Exception $e) {
            return redirect()->route('admin.employees.index')->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new EmployeesExport, 'employees-' . date('Ymd') . '.xlsx');
    }

    public function clearAll(Request $request)
    {
        $request->validate([
            'confirm_text' => 'required|in:HAPUS SEMUA',
        ]);

        DB::transaction(function () {
            ScanNotification::query()->delete();
            InvitationSend::query()->delete();
            Attendance::query()->delete();
            DoorprizeWinner::query()->delete();
            AttendanceConfirmation::query()->delete();
            Invitation::query()->delete();
            Employee::query()->delete();
        });

        return redirect()->route('admin.employees.index')
            ->with('success', 'Semua data master karyawan beserta data terkait (undangan, absensi, konfirmasi, doorprize) berhasil dihapus.');
    }

    public function downloadTemplate()
    {
        return Excel::download(new EmployeeTemplateExport, 'template-import-karyawan.xlsx');
    }
}
