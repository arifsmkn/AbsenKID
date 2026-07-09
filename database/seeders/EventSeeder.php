<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::firstOrCreate(
            ['tahun' => 2026],
            [
                'nama' => 'Konvensi Improvement Dharma ke-31',
                'tahun' => 2026,
                'tema' => 'Inovasi Tanpa Batas, Dharma Maju Bersama',
                'deskripsi' => 'Konvensi Improvement Dharma merupakan ajang tahunan untuk berbagi inovasi dan improvement dari seluruh unit bisnis Dharma Group.',
                'lokasi' => 'Gedung Dharma Convention Center',
                'tanggal' => '2026-08-15',
                'waktu_mulai' => '08:00:00',
                'waktu_selesai' => '17:00:00',
                'is_active' => true,
                'theme_config' => [
                    'primary_color' => '#1e40af',
                    'secondary_color' => '#7c3aed',
                    'mode' => 'dark',
                ],
            ]
        );

        Setting::set('app_name', 'AbsenKID');
        Setting::set('app_mode', 'dark');
    }
}
