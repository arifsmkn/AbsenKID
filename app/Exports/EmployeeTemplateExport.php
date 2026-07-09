<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeeTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['12345', 'Budi Santoso', 'PT Contoh Sejahtera', 'Staff', 'L', 'budi@contoh.co.id', '081234567890'],
        ];
    }

    public function headings(): array
    {
        return ['npk', 'nama', 'subco', 'jabatan', 'ukuran_baju', 'email', 'no_telpon'];
    }
}
