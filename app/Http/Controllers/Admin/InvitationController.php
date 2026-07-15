<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceConfirmation;
use App\Models\Employee;
use App\Models\Event;
use App\Models\Invitation;
use App\Models\InvitationSend;
use App\Services\InvitationSendService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvitationController extends Controller
{
    public function __construct(private InvitationSendService $sender) {}

    public function index(Request $request)
    {
        $event = Event::where('is_active', true)->first();
        $query = Invitation::with(['employee', 'sends', 'attendance'])
            ->where('event_id', $event?->id ?? 0);

        if ($request->search) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('npk', 'like', "%{$request->search}%")
                  ->orWhere('nama', 'like', "%{$request->search}%");
            });
        }

        if ($request->subco) {
            $query->whereHas('employee', fn($q) => $q->where('subco', $request->subco));
        }

        if ($request->filter === 'no_wa') {
            $query->whereHas('employee', fn($q) => $q->whereNull('no_telpon')->orWhere('no_telpon', ''));
        } elseif ($request->filter === 'no_email') {
            $query->whereHas('employee', fn($q) => $q->whereNull('email')->orWhere('email', ''));
        } elseif ($request->filter === 'belum_hadir') {
            $query->whereDoesntHave('attendance');
        } elseif ($request->filter === 'konfirmasi_hadir') {
            $query->whereIn('employee_npk', AttendanceConfirmation::where('event_id', $event?->id ?? 0)
                ->where('status', 'hadir')->pluck('employee_npk'));
        } elseif ($request->filter === 'konfirmasi_tidak_hadir') {
            $query->whereIn('employee_npk', AttendanceConfirmation::where('event_id', $event?->id ?? 0)
                ->where('status', 'tidak_hadir')->pluck('employee_npk'));
        } elseif ($request->filter === 'belum_konfirmasi') {
            $query->whereNotIn('employee_npk', AttendanceConfirmation::where('event_id', $event?->id ?? 0)
                ->pluck('employee_npk'));
        }

        $invitations = $query->paginate(25)->withQueryString();
        $events      = Event::orderByDesc('tahun')->get();

        // Status konfirmasi RSVP online — terpisah dari status Hadir (yang khusus dari scan QR)
        $confirmations = AttendanceConfirmation::where('event_id', $event?->id ?? 0)
            ->pluck('status', 'employee_npk');

        // Jumlah peserta yang belum konfirmasi sama sekali, per SubCo
        $belumKonfirmasiBySubco = Invitation::where('invitations.event_id', $event?->id ?? 0)
            ->join('employees', 'employees.npk', '=', 'invitations.employee_npk')
            ->whereNotIn('invitations.employee_npk', $confirmations->keys())
            ->selectRaw('employees.subco, COUNT(*) as total')
            ->groupBy('employees.subco')
            ->pluck('total', 'employees.subco');

        // Jumlah peserta yang belum hadir (belum scan) sama sekali, per SubCo
        $belumHadirBySubco = Invitation::where('invitations.event_id', $event?->id ?? 0)
            ->join('employees', 'employees.npk', '=', 'invitations.employee_npk')
            ->whereDoesntHave('attendance')
            ->selectRaw('employees.subco, COUNT(*) as total')
            ->groupBy('employees.subco')
            ->pluck('total', 'employees.subco');

        // Gabungkan jadi satu daftar SubCo untuk tombol jalan pintas
        $subcoShortcuts = $belumKonfirmasiBySubco->keys()
            ->merge($belumHadirBySubco->keys())
            ->unique()
            ->sort()
            ->values()
            ->map(fn($subco) => (object) [
                'subco'            => $subco,
                'belum_konfirmasi' => $belumKonfirmasiBySubco[$subco] ?? 0,
                'belum_hadir'      => $belumHadirBySubco[$subco] ?? 0,
            ]);

        // Statistik pengiriman
        $sentWa    = InvitationSend::where('channel', 'whatsapp')->where('status', 'sent')
                        ->whereHas('invitation', fn($q) => $q->where('event_id', $event?->id ?? 0))->count();
        $sentEmail = InvitationSend::where('channel', 'email')->where('status', 'sent')
                        ->whereHas('invitation', fn($q) => $q->where('event_id', $event?->id ?? 0))->count();
        $failWa    = InvitationSend::where('channel', 'whatsapp')->where('status', 'failed')
                        ->whereHas('invitation', fn($q) => $q->where('event_id', $event?->id ?? 0))->count();
        $failEmail = InvitationSend::where('channel', 'email')->where('status', 'failed')
                        ->whereHas('invitation', fn($q) => $q->where('event_id', $event?->id ?? 0))->count();

        return view('admin.invitations.index', compact(
            'invitations', 'event', 'events', 'confirmations', 'subcoShortcuts',
            'sentWa', 'sentEmail', 'failWa', 'failEmail'
        ));
    }

    public function generate(Event $event)
    {
        $employees = Employee::whereDoesntHave('invitations', function ($q) use ($event) {
            $q->where('event_id', $event->id);
        })->get();

        $count = 0;
        foreach ($employees as $employee) {
            Invitation::create([
                'event_id'     => $event->id,
                'employee_npk' => $employee->npk,
                'qr_code'      => Str::uuid()->toString(),
            ]);
            $count++;
        }

        return redirect()->route('admin.invitations.index')
            ->with('success', "{$count} undangan berhasil di-generate.");
    }

    /**
     * Jalan pintas: konfirmasi semua peserta yang BELUM konfirmasi sama sekali
     * jadi status "hadir" atau "tidak_hadir" — bisa untuk semua peserta atau
     * di-scope ke satu SubCo saja. Peserta yang SUDAH pernah konfirmasi
     * (apa pun statusnya) tidak disentuh/ditimpa.
     */
    public function confirmAll(Request $request)
    {
        $request->validate([
            'status' => 'required|in:hadir,tidak_hadir',
            'subco'  => 'nullable|string',
        ]);

        $event = Event::where('is_active', true)->firstOrFail();

        $alreadyConfirmed = AttendanceConfirmation::where('event_id', $event->id)
            ->pluck('employee_npk');

        $query = Invitation::where('event_id', $event->id)
            ->whereNotIn('employee_npk', $alreadyConfirmed);

        if ($request->filled('subco')) {
            $query->whereHas('employee', fn($q) => $q->where('subco', $request->subco));
        }

        $belumKonfirmasi = $query->pluck('employee_npk');

        $now = now();
        $rows = $belumKonfirmasi->map(fn($npk) => [
            'event_id'     => $event->id,
            'employee_npk' => $npk,
            'status'       => $request->status,
            'confirmed_at' => $now,
            'created_at'   => $now,
            'updated_at'   => $now,
        ])->all();

        if (!empty($rows)) {
            AttendanceConfirmation::insert($rows);
        }

        $label = $request->status === 'hadir' ? 'Akan Hadir' : 'Tidak Hadir';
        $scope = $request->filled('subco') ? "SubCo \"{$request->subco}\"" : 'semua peserta';

        return redirect()->route('admin.invitations.index')
            ->with('success', count($rows) . " peserta yang belum konfirmasi di {$scope} berhasil ditandai \"{$label}\". Peserta yang sudah pernah konfirmasi tidak diubah.");
    }

    /** Kirim undangan satu peserta (support channel: wa/email/both) */
    public function sendOne(Request $request, Invitation $invitation)
    {
        $invitation->load('employee', 'event');
        $channel = $request->input('channel', 'both');

        if ($channel === 'wa') {
            $ok  = $this->sender->sendWhatsApp($invitation);
            $msg = $ok ? 'WA terkirim' : 'WA gagal';
            return back()->with($ok ? 'success' : 'error', "{$invitation->employee->nama}: {$msg}");
        }

        if ($channel === 'email') {
            $ok  = $this->sender->sendEmail($invitation);
            $msg = $ok ? 'Email terkirim' : 'Email gagal';
            return back()->with($ok ? 'success' : 'error', "{$invitation->employee->nama}: {$msg}");
        }

        $result = $this->sender->sendOne($invitation);
        $msgs = [];
        if ($result['wa'] === true)    $msgs[] = 'WA terkirim';
        if ($result['wa'] === false)   $msgs[] = 'WA gagal';
        if ($result['email'] === true) $msgs[] = 'Email terkirim';
        if ($result['email'] === false) $msgs[] = 'Email gagal';

        $msg = $msgs ? implode(', ', $msgs) : 'Tidak ada channel aktif.';
        return back()->with(in_array(true, $result, true) ? 'success' : 'error',
            "{$invitation->employee->nama}: {$msg}");
    }

    /** Kirim test ke email/WA admin sendiri */
    public function sendTest(Request $request)
    {
        $request->validate([
            'channel' => 'required|in:wa,email,both',
            'email'   => 'nullable|email',
            'phone'   => 'nullable|string|max:20',
        ]);

        $event = Event::where('is_active', true)->firstOrFail();
        $invitation = Invitation::with(['employee', 'event'])
            ->where('event_id', $event->id)->first();

        if (!$invitation) {
            return back()->with('error', 'Belum ada undangan. Generate dulu.');
        }

        $channel = $request->channel;
        $results = [];

        if (in_array($channel, ['email', 'both']) && $request->email) {
            $ok = $this->sender->sendTestEmail($invitation, $request->email);
            $results[] = $ok ? "Email ke {$request->email}: terkirim" : "Email ke {$request->email}: gagal";
        }

        if (in_array($channel, ['wa', 'both']) && $request->phone) {
            $ok = $this->sender->sendTestWa($invitation, $request->phone);
            $results[] = $ok ? "WA ke {$request->phone}: terkirim" : "WA ke {$request->phone}: gagal";
        }

        if (empty($results)) {
            return back()->with('error', 'Masukkan email atau nomor WA tujuan test.');
        }

        $allOk = !str_contains(implode(' ', $results), 'gagal');
        return back()->with($allOk ? 'success' : 'error', '🧪 Test: ' . implode(' | ', $results));
    }

    /** Kirim semua undangan yang belum terkirim (background-friendly) */
    public function sendAll(Request $request)
    {
        $event = Event::where('is_active', true)->firstOrFail();
        $channel = $request->input('channel', 'both'); // wa, email, both

        $query = Invitation::with(['employee', 'event'])
            ->where('event_id', $event->id);

        // Hanya yang belum terkirim sukses di channel yang dipilih
        if ($channel === 'wa') {
            $query->whereDoesntHave('sends', fn($q) => $q->where('channel', 'whatsapp')->where('status', 'sent'));
        } elseif ($channel === 'email') {
            $query->whereDoesntHave('sends', fn($q) => $q->where('channel', 'email')->where('status', 'sent'));
        } else {
            // both: kirim yang belum dapat salah satu
            $query->where(function ($q) {
                $q->whereDoesntHave('sends', fn($q2) => $q2->where('channel', 'whatsapp')->where('status', 'sent'))
                  ->orWhereDoesntHave('sends', fn($q2) => $q2->where('channel', 'email')->where('status', 'sent'));
            });
        }

        $invitations = $query->get();
        $sent = 0; $failed = 0;

        foreach ($invitations as $invitation) {
            if ($channel === 'wa') {
                $ok = $this->sender->sendWhatsApp($invitation);
            } elseif ($channel === 'email') {
                $ok = $this->sender->sendEmail($invitation);
            } else {
                $result = $this->sender->sendOne($invitation);
                $ok = in_array(true, $result, true);
            }
            $ok ? $sent++ : $failed++;

            if (in_array($channel, ['wa', 'both'])) {
                sleep(5);
            }
        }

        return back()->with('success', "Pengiriman selesai: {$sent} berhasil, {$failed} gagal dari {$invitations->count()} undangan.");
    }

    /** History pengiriman */
    public function sendHistory(Request $request)
    {
        $event = Event::where('is_active', true)->first();

        $query = InvitationSend::with(['invitation.employee'])
            ->whereHas('invitation', fn($q) => $q->where('event_id', $event?->id ?? 0))
            ->orderByDesc('updated_at');

        if ($request->channel && $request->channel !== 'all') {
            $query->where('channel', $request->channel);
        }
        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('target', 'like', "%{$request->search}%")
                  ->orWhereHas('invitation.employee', fn($q2) =>
                      $q2->where('nama', 'like', "%{$request->search}%")
                         ->orWhere('npk', 'like', "%{$request->search}%")
                  );
            });
        }

        $sends  = $query->paginate(30)->withQueryString();
        $stats  = [
            'wa_sent'     => InvitationSend::whereHas('invitation', fn($q) => $q->where('event_id', $event?->id ?? 0))->where('channel','whatsapp')->where('status','sent')->count(),
            'wa_failed'   => InvitationSend::whereHas('invitation', fn($q) => $q->where('event_id', $event?->id ?? 0))->where('channel','whatsapp')->where('status','failed')->count(),
            'email_sent'  => InvitationSend::whereHas('invitation', fn($q) => $q->where('event_id', $event?->id ?? 0))->where('channel','email')->where('status','sent')->count(),
            'email_failed'=> InvitationSend::whereHas('invitation', fn($q) => $q->where('event_id', $event?->id ?? 0))->where('channel','email')->where('status','failed')->count(),
        ];

        return view('admin.invitations.send-history', compact('sends', 'stats', 'event'));
    }

    public function showQr(Invitation $invitation)
    {
        $qr = QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->generate(route('scan.qr', $invitation->qr_code));

        return view('admin.invitations.qr', compact('invitation', 'qr'));
    }
}
