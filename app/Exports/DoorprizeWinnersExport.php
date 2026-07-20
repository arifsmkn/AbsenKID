<?php

namespace App\Exports;

use App\Models\DoorprizeWinner;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DoorprizeWinnersExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private int $eventId)
    {
    }

    public function collection()
    {
        return DoorprizeWinner::with(['employee', 'doorprize'])
            ->where('event_id', $this->eventId)
            ->orderBy('won_at')
            ->get();
    }

    public function headings(): array
    {
        return ['npk', 'nama', 'subco', 'hadiah', 'tipe', 'waktu_menang'];
    }

    public function map($winner): array
    {
        $typeLabel = [
            'doorprize'       => 'Doorprize',
            'doorprize_utama' => 'Doorprize Utama',
            'grand_prize'     => 'Grand Prize',
        ];

        return [
            $winner->employee?->npk,
            $winner->employee?->nama,
            $winner->employee?->subco,
            $winner->doorprize?->nama_hadiah,
            $typeLabel[$winner->doorprize?->type] ?? $winner->doorprize?->type,
            $winner->won_at->timezone('Asia/Jakarta')->format('Y-m-d H:i:s'),
        ];
    }
}
