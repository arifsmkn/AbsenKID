<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Employee::orderBy('subco')->orderBy('nama')->get();
    }

    public function headings(): array
    {
        return ['npk', 'nama', 'subco', 'jabatan', 'ukuran_baju', 'email', 'no_telpon'];
    }

    public function map($employee): array
    {
        return [
            $employee->npk,
            $employee->nama,
            $employee->subco,
            $employee->jabatan,
            $employee->ukuran_baju,
            $employee->email,
            $employee->no_telpon,
        ];
    }
}
