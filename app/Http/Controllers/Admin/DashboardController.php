<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceConfirmation;
use App\Models\Employee;
use App\Models\Event;
use App\Models\Invitation;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $event = Event::where('is_active', true)->first();

        if (!$event) {
            return view('admin.dashboard', ['event' => null]);
        }

        $totalInvited = Invitation::where('event_id', $event->id)->count();
        $totalAttended = Attendance::where('event_id', $event->id)->count();
        $percentage = $totalInvited > 0 ? round(($totalAttended / $totalInvited) * 100, 1) : 0;

        $totalConfirmedHadir = AttendanceConfirmation::where('event_id', $event->id)
            ->where('status', 'hadir')->count();
        $totalConfirmedTidak = AttendanceConfirmation::where('event_id', $event->id)
            ->where('status', 'tidak_hadir')->count();

        $invitationBySubco = Invitation::where('invitations.event_id', $event->id)
            ->join('employees', 'employees.npk', '=', 'invitations.employee_npk')
            ->selectRaw('employees.subco, COUNT(*) as total')
            ->groupBy('employees.subco')
            ->pluck('total', 'employees.subco');

        $hadirBySubco = Attendance::where('attendances.event_id', $event->id)
            ->join('employees', 'employees.npk', '=', 'attendances.employee_npk')
            ->selectRaw('employees.subco, COUNT(*) as hadir')
            ->groupBy('employees.subco')
            ->pluck('hadir', 'employees.subco');

        // Sertakan subco yang belum punya kehadiran sama sekali (0%), bukan cuma yang sudah ada scan
        $subcoStats = $invitationBySubco->keys()
            ->map(fn($subco) => (object) ['subco' => $subco, 'hadir' => $hadirBySubco[$subco] ?? 0])
            ->sortByDesc('hadir')
            ->values();

        $recentAttendances = Attendance::where('attendances.event_id', $event->id)
            ->with('employee')
            ->orderByDesc('scanned_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'event', 'totalInvited', 'totalAttended', 'percentage',
            'totalConfirmedHadir', 'totalConfirmedTidak',
            'subcoStats', 'invitationBySubco', 'recentAttendances'
        ));
    }
}
