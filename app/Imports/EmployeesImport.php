<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployeesImport implements ToModel, WithHeadingRow, WithUpserts, WithValidation
{
    public function model(array $row): Employee
    {
        return new Employee([
            'npk'         => (string) $row['npk'],
            'nama'        => $row['nama'],
            'subco'       => $row['subco'],
            'jabatan'     => $row['jabatan'],
            'ukuran_baju' => $row['ukuran_baju'] ?? null,
            'email'       => $row['email'] ?? null,
            'no_telpon'   => $row['no_telpon'] ?? null,
        ]);
    }

    public function uniqueBy(): string
    {
        return 'npk';
    }

    public function rules(): array
    {
        return [
            'npk'   => 'required',
            'nama'  => 'required',
            'subco' => 'required',
            'jabatan' => 'required',
        ];
    }
}
