<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\AttendanceConfirmation;
use App\Models\DoorprizeWinner;
use App\Models\Employee;
use App\Models\Event;
use App\Models\Invitation;
use App\Models\Setting;
use App\Services\InvitationSendService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private function getEmployee(): Employee
    {
        return Employee::findOrFail(session('peserta_npk'));
    }

    public function index()
    {
        $employee = $this->getEmployee();
        $event    = Event::where('is_active', true)->first();

        if (!$event) {
            return view('peserta.no-event', compact('employee'));
        }

        $confirmation = AttendanceConfirmation::where('event_id', $event->id)
            ->where('employee_npk', $employee->npk)
            ->first();

        if (!$confirmation) {
            return redirect()->route('peserta.konfirmasi');
        }

        if ($confirmation->status === 'tidak_hadir') {
            return redirect()->route('peserta.tidak-hadir');
        }

        // Confirmed hadir — tampilkan dashboard + QR
        $invitation = Invitation::where('event_id', $event->id)
            ->where('employee_npk', $employee->npk)
            ->first();

        $doorprizeWin = DoorprizeWinner::with('doorprize')
            ->where('event_id', $event->id)
            ->where('employee_npk', $employee->npk)
            ->first();

        $panitia = [
            'nama'     => Setting::get('panitia_nama', 'Panitia Konvensi'),
            'whatsapp' => Setting::get('panitia_whatsapp', ''),
            'email'    => Setting::get('panitia_email', ''),
        ];

        return view('peserta.dashboard', compact(
            'employee', 'event', 'invitation', 'confirmation', 'doorprizeWin', 'panitia'
        ));
    }

    public function showKonfirmasi()
    {
        $employee = $this->getEmployee();
        $event    = Event::where('is_active', true)->first();

        if (!$event) {
            return redirect()->route('peserta.dashboard');
        }

        // Jika sudah konfirmasi, redirect sesuai status
        $existing = AttendanceConfirmation::where('event_id', $event->id)
            ->where('employee_npk', $employee->npk)
            ->first();

        if ($existing?->status === 'hadir') {
            return redirect()->route('peserta.dashboard');
        }
        if ($existing?->status === 'tidak_hadir') {
            return redirect()->route('peserta.tidak-hadir');
        }

        return view('peserta.confirm', compact('employee', 'event'));
    }

    public function postKonfirmasi(Request $request)
    {
        $request->validate(['status' => 'required|in:hadir,tidak_hadir']);

        $employee = $this->getEmployee();
        $event    = Event::where('is_active', true)->firstOrFail();

        AttendanceConfirmation::updateOrCreate(
            ['event_id' => $event->id, 'employee_npk' => $employee->npk],
            ['status' => $request->status, 'confirmed_at' => now()]
        );

        if ($request->status === 'hadir') {
            // Update invitation is_confirmed
            $invitation = Invitation::where('event_id', $event->id)
                ->where('employee_npk', $employee->npk)
                ->first();

            if ($invitation) {
                $invitation->update(['is_confirmed' => true]);

                // Kirim QR via WA/email jika channel aktif
                try {
                    (new InvitationSendService())->sendOne($invitation);
                } catch (\Exception $e) {}
            }

            return redirect()->route('peserta.dashboard')
                ->with('success', 'Kehadiran Anda berhasil dikonfirmasi! QR undangan siap.');
        }

        return redirect()->route('peserta.tidak-hadir');
    }

    public function tidakHadir()
    {
        $employee = $this->getEmployee();
        $event    = Event::where('is_active', true)->first();

        $panitia = [
            'whatsapp' => Setting::get('panitia_whatsapp', ''),
            'email'    => Setting::get('panitia_email', ''),
        ];

        return view('peserta.tidak-hadir', compact('employee', 'event', 'panitia'));
    }
}
