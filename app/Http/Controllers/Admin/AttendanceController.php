<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceConfirmation;
use App\Models\Employee;
use App\Models\Event;
use App\Models\Invitation;
use App\Models\ScanNotification;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $event = Event::where('is_active', true)->first();
        $query = Attendance::with('employee')->where('event_id', $event?->id ?? 0);

        if ($request->search) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('npk', 'like', "%{$request->search}%")
                    ->orWhere('nama', 'like', "%{$request->search}%")
                    ->orWhere('subco', 'like', "%{$request->search}%");
            });
        }

        $attendances   = $query->orderByDesc('scanned_at')->paginate(20)->withQueryString();
        $totalInvited  = Invitation::where('event_id', $event?->id ?? 0)->count();
        $totalAttended = Attendance::where('event_id', $event?->id ?? 0)->count();
        $totalConfirmedHadir = AttendanceConfirmation::where('event_id', $event?->id ?? 0)
            ->where('status', 'hadir')->count();
        $totalConfirmedTidak = AttendanceConfirmation::where('event_id', $event?->id ?? 0)
            ->where('status', 'tidak_hadir')->count();

        $tidakHadirList = AttendanceConfirmation::with('employee')
            ->where('event_id', $event?->id ?? 0)
            ->where('status', 'tidak_hadir')
            ->orderByDesc('confirmed_at')
            ->get();

        return view('admin.attendances.index', compact(
            'attendances', 'event', 'totalInvited', 'totalAttended',
            'totalConfirmedHadir', 'totalConfirmedTidak', 'tidakHadirList'
        ));
    }

    // Manual tambah kehadiran fisik (tanpa scan QR)
    public function manualStore(Request $request)
    {
        $request->validate(['npk' => 'required|exists:employees,npk']);

        $event = Event::where('is_active', true)->firstOrFail();

        $already = Attendance::where('event_id', $event->id)
            ->where('employee_npk', $request->npk)
            ->exists();

        if ($already) {
            return back()->with('error', 'Peserta ini sudah tercatat hadir.');
        }

        $invitation = Invitation::where('event_id', $event->id)
            ->where('employee_npk', $request->npk)
            ->first();

        $attendance = Attendance::create([
            'invitation_id' => $invitation?->id,
            'event_id'      => $event->id,
            'employee_npk'  => $request->npk,
            'scanned_at'    => now(),
            'scanned_by'    => 'admin-manual',
            'source'        => 'manual_admin',
        ]);

        // Buat scan notification untuk TV
        try {
            ScanNotification::create([
                'event_id'      => $event->id,
                'employee_npk'  => $request->npk,
                'attendance_id' => $attendance->id,
                'scanned_at'    => now(),
            ]);
        } catch (\Exception $e) {}

        if ($invitation) {
            $invitation->update(['is_confirmed' => true]);
        }

        $employee = Employee::find($request->npk);
        return back()->with('success', "Kehadiran {$employee->nama} berhasil ditambahkan secara manual.");
    }

    // Tandai hadir langsung (tanpa scan) untuk SEMUA peserta satu SubCo yang belum hadir
    public function manualStoreBySubco(Request $request)
    {
        $request->validate(['subco' => 'required|string']);

        $event = Event::where('is_active', true)->firstOrFail();

        $alreadyAttended = Attendance::where('event_id', $event->id)->pluck('employee_npk');

        $invitations = Invitation::where('event_id', $event->id)
            ->whereHas('employee', fn($q) => $q->where('subco', $request->subco))
            ->whereNotIn('employee_npk', $alreadyAttended)
            ->get();

        $count = 0;
        foreach ($invitations as $invitation) {
            $attendance = Attendance::create([
                'invitation_id' => $invitation->id,
                'event_id'      => $event->id,
                'employee_npk'  => $invitation->employee_npk,
                'scanned_at'    => now(),
                'scanned_by'    => 'admin-manual',
                'source'        => 'manual_admin',
            ]);

            try {
                ScanNotification::create([
                    'event_id'      => $event->id,
                    'employee_npk'  => $invitation->employee_npk,
                    'attendance_id' => $attendance->id,
                    'scanned_at'    => now(),
                ]);
            } catch (\Exception $e) {}

            $invitation->update(['is_confirmed' => true]);
            $count++;
        }

        return redirect()->route('admin.invitations.index')
            ->with('success', "{$count} peserta SubCo \"{$request->subco}\" berhasil ditandai Hadir langsung (tanpa scan).");
    }

    // Admin ubah status konfirmasi peserta
    public function updateKonfirmasi(Request $request)
    {
        $request->validate([
            'npk'    => 'required|exists:employees,npk',
            'status' => 'required|in:hadir,tidak_hadir',
        ]);

        $event = Event::where('is_active', true)->firstOrFail();

        AttendanceConfirmation::updateOrCreate(
            ['event_id' => $event->id, 'employee_npk' => $request->npk],
            ['status' => $request->status, 'confirmed_at' => now()]
        );

        $employee = Employee::find($request->npk);
        return back()->with('success', "Konfirmasi {$employee->nama} diubah ke: {$request->status}.");
    }

    // Hapus attendance (undo hadir)
    public function destroy(Attendance $attendance)
    {
        $nama = $attendance->employee->nama ?? $attendance->employee_npk;

        if ($attendance->invitation_id) {
            Invitation::where('id', $attendance->invitation_id)->update(['is_confirmed' => false]);
        }

        $attendance->delete();
        return back()->with('success', "Kehadiran {$nama} berhasil dihapus.");
    }

}
