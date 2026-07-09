<?php

namespace App\Http\Controllers;

use App\Events\GuestScanned;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Event;
use App\Models\Invitation;
use App\Models\ScanNotification;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function index()
    {
        $event = Event::where('is_active', true)->first();
        return view('public.scan', compact('event'));
    }

    public function scanQr(string $qrCode)
    {
        $invitation = Invitation::with('employee', 'event')
            ->where('qr_code', $qrCode)
            ->first();

        if (!$invitation) {
            return view('public.scan-result', [
                'status'     => 'invalid',
                'message'    => 'QR Code tidak valid atau tidak ditemukan.',
                'employee'   => null,
                'invitation' => null,
            ]);
        }

        $alreadyScanned = Attendance::where('invitation_id', $invitation->id)->exists();

        if ($alreadyScanned) {
            return view('public.scan-result', [
                'status'     => 'duplicate',
                'message'    => 'Tamu ini sudah melakukan check-in sebelumnya.',
                'employee'   => $invitation->employee,
                'invitation' => $invitation,
            ]);
        }

        $attendance = Attendance::create([
            'invitation_id' => $invitation->id,
            'event_id'      => $invitation->event_id,
            'employee_npk'  => $invitation->employee_npk,
            'scanned_at'    => now(),
            'scanned_by'    => 'kiosk',
            'source'        => 'scan_qr',
        ]);

        $invitation->update(['is_confirmed' => true]);

        $this->createScanNotification($attendance);

        try {
            event(new GuestScanned($attendance->load('employee')));
        } catch (\Exception $e) {}

        return view('public.scan-result', [
            'status'     => 'success',
            'message'    => 'Selamat datang! Check-in berhasil.',
            'employee'   => $invitation->employee,
            'invitation' => $invitation,
        ]);
    }

    public function scanByNpk(Request $request)
    {
        $request->validate(['npk' => 'required|string']);

        $event = Event::where('is_active', true)->first();
        if (!$event) {
            return view('public.scan-result', [
                'status'     => 'invalid',
                'message'    => 'Tidak ada event aktif.',
                'employee'   => null,
                'invitation' => null,
            ]);
        }

        $invitation = Invitation::with('employee')
            ->where('event_id', $event->id)
            ->where('employee_npk', $request->npk)
            ->first();

        if (!$invitation) {
            // Coba cari employee langsung (bisa jadi belum dapat undangan)
            $employee = Employee::find($request->npk);
            return view('public.scan-result', [
                'status'     => 'invalid',
                'message'    => $employee
                    ? "NPK {$request->npk} ({$employee->nama}) tidak memiliki undangan untuk event ini."
                    : "NPK {$request->npk} tidak ditemukan.",
                'employee'   => $employee,
                'invitation' => null,
            ]);
        }

        return $this->scanQr($invitation->qr_code);
    }

    public function process(Request $request)
    {
        $request->validate(['qr_code' => 'required|string']);
        return redirect()->route('scan.qr', $request->qr_code);
    }

    // TV notification queue (polling)
    public function tvPage()
    {
        $event = Event::where('is_active', true)->first();
        return view('public.scan-tv', compact('event'));
    }

    // Dipanggil sekali saat halaman TV pertama dibuka, supaya tidak replay histori scan lama
    public function tvLatest()
    {
        $event = Event::where('is_active', true)->first();
        if (!$event) {
            return response()->json(['latest_id' => 0]);
        }

        $latestId = ScanNotification::where('event_id', $event->id)->max('id') ?? 0;
        return response()->json(['latest_id' => $latestId]);
    }

    public function tvQueue(Request $request)
    {
        $afterId = (int) $request->get('after', 0);
        $event   = Event::where('is_active', true)->first();

        if (!$event) {
            return response()->json(['notification' => null]);
        }

        $notif = ScanNotification::with('employee')
            ->where('event_id', $event->id)
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->first();

        if (!$notif) {
            return response()->json(['notification' => null, 'latest_id' => $afterId]);
        }

        return response()->json([
            'notification' => [
                'id'     => $notif->id,
                'nama'   => $notif->employee->nama,
                'subco'  => $notif->employee->subco,
                'npk'    => $notif->employee_npk,
                'time'   => $notif->scanned_at->format('H:i:s'),
            ],
            'latest_id' => $notif->id,
        ]);
    }

    private function createScanNotification(Attendance $attendance): void
    {
        try {
            ScanNotification::create([
                'event_id'      => $attendance->event_id,
                'employee_npk'  => $attendance->employee_npk,
                'attendance_id' => $attendance->id,
                'scanned_at'    => $attendance->scanned_at,
            ]);
        } catch (\Exception $e) {}
    }
}
