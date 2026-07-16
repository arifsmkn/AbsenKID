<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Invitation;
use App\Models\Setting;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        $event = Event::where('is_active', true)->with(['slides' => function ($q) {
            $q->where('is_active', true)->orderBy('urutan');
        }])->first();

        $appMode = Setting::get('app_mode', 'dark');

        return view('public.landing', compact('event', 'appMode'));
    }

    public function liveAbsensi()
    {
        $event = Event::where('is_active', true)->first();
        return view('public.liveabsensi', compact('event'));
    }

    public function liveAbsensiData()
    {
        $event = Event::where('is_active', true)->first();
        if (!$event) {
            return response()->json(['total' => 0, 'recent' => [], 'by_subco' => []]);
        }

        $totalInvited  = Invitation::where('event_id', $event->id)->count();
        $totalAttended = Attendance::where('event_id', $event->id)->count();

        $recent = Attendance::where('attendances.event_id', $event->id)
            ->join('employees', 'employees.npk', '=', 'attendances.employee_npk')
            ->selectRaw('employees.nama, employees.subco, employees.jabatan, attendances.scanned_at, attendances.source')
            ->orderByDesc('attendances.scanned_at')
            ->limit(50)
            ->get()
            ->map(fn($a) => [
                'nama'   => $a->nama,
                'subco'  => $a->subco,
                'waktu'  => \Carbon\Carbon::parse($a->scanned_at)->format('H:i:s'),
                'source' => $a->source,
            ]);

        $invBySubco = Invitation::where('invitations.event_id', $event->id)
            ->join('employees', 'employees.npk', '=', 'invitations.employee_npk')
            ->selectRaw('employees.subco, COUNT(*) as total')
            ->groupBy('employees.subco')
            ->pluck('total', 'employees.subco');

        $hadirBySubco = Attendance::where('attendances.event_id', $event->id)
            ->join('employees', 'employees.npk', '=', 'attendances.employee_npk')
            ->selectRaw('employees.subco, COUNT(*) as hadir')
            ->groupBy('employees.subco')
            ->pluck('hadir', 'employees.subco');

        // Sertakan semua SubCo yang punya undangan, termasuk yang belum ada kehadiran sama sekali (0%)
        $bySubco = $invBySubco->keys()
            ->map(fn($subco) => [
                'subco' => $subco,
                'hadir' => $hadirBySubco[$subco] ?? 0,
                'total' => $invBySubco[$subco],
                'pct'   => round((($hadirBySubco[$subco] ?? 0) / $invBySubco[$subco]) * 100),
            ])
            ->sortByDesc('total')
            ->values();

        return response()->json(compact('totalInvited', 'totalAttended', 'recent', 'bySubco'));
    }
}
