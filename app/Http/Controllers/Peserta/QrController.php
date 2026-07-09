<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\AttendanceConfirmation;
use App\Models\DoorprizeWinner;
use App\Models\Employee;
use App\Models\Event;
use App\Models\Invitation;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrController extends Controller
{
    private function getEmployee(): Employee
    {
        return Employee::findOrFail(session('peserta_npk'));
    }

    public function show()
    {
        $employee   = $this->getEmployee();
        $event      = Event::where('is_active', true)->firstOrFail();

        // QR cuma boleh dilihat/didownload kalau sudah konfirmasi HADIR.
        // Mencegah peserta yang belum konfirmasi / sudah bilang tidak hadir
        // mengakses QR lewat akses langsung ke URL ini.
        $confirmation = AttendanceConfirmation::where('event_id', $event->id)
            ->where('employee_npk', $employee->npk)
            ->first();

        if (!$confirmation || $confirmation->status !== 'hadir') {
            return redirect()->route('peserta.dashboard');
        }

        $invitation = Invitation::where('event_id', $event->id)
                                ->where('employee_npk', $employee->npk)
                                ->firstOrFail();

        $qrSvg = QrCode::format('svg')
            ->size(300)
            ->margin(1)
            ->errorCorrection('H')
            ->generate(route('scan.qr', $invitation->qr_code));

        $doorprizeWin = DoorprizeWinner::with('doorprize')
            ->where('event_id', $event->id)
            ->where('employee_npk', $employee->npk)
            ->first();

        return view('peserta.qr', compact('employee', 'event', 'invitation', 'qrSvg', 'doorprizeWin'));
    }

}
