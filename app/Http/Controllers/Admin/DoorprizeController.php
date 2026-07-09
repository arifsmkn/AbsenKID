<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Doorprize;
use App\Models\DoorprizeExcludeRole;
use App\Models\DoorprizeWinner;
use App\Models\Employee;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DoorprizeController extends Controller
{
    private const CACHE_KEY = 'doorprize_display_v2';
    private const CACHE_TTL = 60 * 8; // 8 jam

    public function index()
    {
        $event     = Event::where('is_active', true)->first();
        $doorprizes = Doorprize::where('event_id', $event?->id ?? 0)->orderBy('urutan')->get();
        return view('admin.doorprizes.index', compact('doorprizes', 'event'));
    }

    public function create()
    {
        $event = Event::where('is_active', true)->first();
        return view('admin.doorprizes.create', compact('event'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_hadiah' => 'required|string',
            'gambar'      => 'nullable|image|max:5120',
            'jumlah'      => 'required|integer|min:1',
            'urutan'      => 'nullable|integer',
            'type'        => 'required|in:doorprize,doorprize_utama,grand_prize',
        ]);

        $event = Event::where('is_active', true)->firstOrFail();
        $data['event_id'] = $event->id;

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('doorprizes', 'public');
        }

        Doorprize::create($data);
        return redirect()->route('admin.doorprizes.index')->with('success', 'Hadiah berhasil ditambahkan.');
    }

    public function edit(Doorprize $doorprize)
    {
        return view('admin.doorprizes.edit', compact('doorprize'));
    }

    public function update(Request $request, Doorprize $doorprize)
    {
        $data = $request->validate([
            'nama_hadiah' => 'required|string',
            'gambar'      => 'nullable|image|max:5120',
            'jumlah'      => 'required|integer|min:1',
            'urutan'      => 'nullable|integer',
            'type'        => 'required|in:doorprize,doorprize_utama,grand_prize',
        ]);

        if ($request->hasFile('gambar')) {
            if ($doorprize->gambar) Storage::disk('public')->delete($doorprize->gambar);
            $data['gambar'] = $request->file('gambar')->store('doorprizes', 'public');
        }

        $doorprize->update($data);
        return redirect()->route('admin.doorprizes.index')->with('success', 'Hadiah berhasil diperbarui.');
    }

    public function destroy(Doorprize $doorprize)
    {
        if ($doorprize->gambar) Storage::disk('public')->delete($doorprize->gambar);
        $doorprize->delete();
        return redirect()->route('admin.doorprizes.index')->with('success', 'Hadiah berhasil dihapus.');
    }

    public function spin()
    {
        $event       = Event::where('is_active', true)->firstOrFail();
        $doorprizes  = $event->doorprizes()->orderBy('urutan')->get();

        $alreadyWon      = DoorprizeWinner::where('event_id', $event->id)->pluck('employee_npk')->toArray();
        $excludedJabatan = DoorprizeExcludeRole::pluck('jabatan')->toArray();

        $eligibleCount = Attendance::where('event_id', $event->id)
            ->whereNotIn('employee_npk', $alreadyWon)
            ->join('employees', 'employees.npk', '=', 'attendances.employee_npk')
            ->when(!empty($excludedJabatan), fn($q) => $q->whereNotIn('employees.jabatan', $excludedJabatan))
            ->count();

        $jabatanList = Attendance::where('event_id', $event->id)
            ->join('employees', 'employees.npk', '=', 'attendances.employee_npk')
            ->distinct()->orderBy('employees.jabatan')
            ->pluck('employees.jabatan')->filter()->values();

        $subcoList = Attendance::where('event_id', $event->id)
            ->join('employees', 'employees.npk', '=', 'attendances.employee_npk')
            ->distinct()->orderBy('employees.subco')
            ->pluck('employees.subco')->filter()->values();

        // Jumlah eligible per-SubCo (belum menang, tidak di-exclude jabatan)
        $subcoEligibleCounts = Attendance::where('event_id', $event->id)
            ->whereNotIn('employee_npk', $alreadyWon)
            ->join('employees', 'employees.npk', '=', 'attendances.employee_npk')
            ->when(!empty($excludedJabatan), fn($q) => $q->whereNotIn('employees.jabatan', $excludedJabatan))
            ->selectRaw('employees.subco, COUNT(*) as cnt')
            ->groupBy('employees.subco')
            ->pluck('cnt', 'employees.subco');

        $displayUrl = route('doorprizes.displayPage');

        $winners = DoorprizeWinner::with(['employee', 'doorprize'])
            ->where('event_id', $event->id)
            ->orderByDesc('won_at')->get();

        return view('admin.doorprizes.spin', compact(
            'event', 'doorprizes', 'eligibleCount', 'jabatanList', 'subcoList',
            'subcoEligibleCounts', 'displayUrl', 'winners', 'excludedJabatan'
        ));
    }

    public function draw(Request $request)
    {
        $request->validate([
            'doorprize_id'       => 'required|exists:doorprizes,id',
            'excluded_jabatan'   => 'nullable|array',
            'excluded_jabatan.*' => 'string',
            'subco'              => 'nullable|array',
            'subco.*'            => 'string',
        ]);

        $event      = Event::where('is_active', true)->firstOrFail();
        $alreadyWon = DoorprizeWinner::where('event_id', $event->id)->pluck('employee_npk')->toArray();

        $excludedJabatan = DoorprizeExcludeRole::pluck('jabatan')->toArray();
        // Merge master exclude + manual tambahan dari request
        $allExcluded = array_unique(array_merge($excludedJabatan, $request->excluded_jabatan ?? []));

        $query = Attendance::where('event_id', $event->id)
            ->whereNotIn('employee_npk', $alreadyWon)
            ->join('employees', 'employees.npk', '=', 'attendances.employee_npk');

        if (!empty($allExcluded)) {
            $query->whereNotIn('employees.jabatan', $allExcluded);
        }

        // Kosong/tidak dipilih = semua subco ikut. Dipilih beberapa = hanya subco itu yang diundi
        // (mis. 1 grand prize diperebutkan 3 subco terpilih).
        if (!empty($request->subco)) {
            $query->whereIn('employees.subco', $request->subco);
        }

        $eligibleRows = $query
            ->select('attendances.employee_npk', 'employees.subco', 'employees.nama')
            ->get();

        if ($eligibleRows->isEmpty()) {
            return response()->json(['error' => 'Tidak ada peserta yang bisa dikocok.'], 422);
        }

        $eligible    = $eligibleRows->pluck('employee_npk')->toArray();
        $winnerNpk   = $eligible[array_rand($eligible)];
        $employee    = Employee::find($winnerNpk);
        $doorprize   = Doorprize::find($request->doorprize_id);

        // all_entries: untuk drum admin (npk + subco + nama)
        $allEntries = $eligibleRows->map(fn($r) => [
            'npk'   => $r->employee_npk,
            'subco' => $r->subco,
            'nama'  => $r->nama,
        ])->shuffle()->values()->all();

        return response()->json([
            'npk'          => $employee->npk,
            'nama'         => $employee->nama,
            'subco'        => $employee->subco,
            'jabatan'      => $employee->jabatan,
            'doorprize_id' => $doorprize->id,
            'nama_hadiah'  => $doorprize->nama_hadiah,
            'gambar_url'   => $doorprize->gambar_url,
            'type'         => $doorprize->type,
            'all_npk'      => $eligible,
            'all_entries'  => $allEntries,
        ]);
    }

    public function saveWinner(Request $request)
    {
        $request->validate([
            'employee_npk' => 'required|exists:employees,npk',
            'doorprize_id' => 'required|exists:doorprizes,id',
        ]);

        $event  = Event::where('is_active', true)->firstOrFail();
        $exists = DoorprizeWinner::where('event_id', $event->id)
            ->where('employee_npk', $request->employee_npk)->exists();

        if ($exists) {
            return response()->json(['error' => 'Peserta ini sudah pernah menang.'], 422);
        }

        $winner = DoorprizeWinner::create([
            'doorprize_id' => $request->doorprize_id,
            'event_id'     => $event->id,
            'employee_npk' => $request->employee_npk,
            'won_at'       => now(),
        ]);

        $winner->load(['employee', 'doorprize']);

        return response()->json([
            'success' => true,
            'winner'  => [
                'npk'    => $winner->employee->npk,
                'nama'   => $winner->employee->nama,
                'subco'  => $winner->employee->subco,
                'hadiah' => $winner->doorprize->nama_hadiah,
                'gambar' => $winner->doorprize->gambar_url,
                'type'   => $winner->doorprize->type,
                'won_at' => $winner->won_at->format('H:i:s'),
            ],
        ]);
    }

    public function winners()
    {
        $event   = Event::where('is_active', true)->first();
        $winners = DoorprizeWinner::with(['employee', 'doorprize'])
            ->where('event_id', $event?->id ?? 0)
            ->orderByDesc('won_at')->get();

        return view('admin.doorprizes.winners', compact('winners', 'event'));
    }

    /** Hapus satu pemenang */
    public function destroyWinner(DoorprizeWinner $winner)
    {
        $winner->delete();
        return back()->with('success', 'Pemenang berhasil dihapus. Peserta kembali eligible untuk spin.');
    }

    /** Hapus semua pemenang event aktif (gambar/hadiah tetap aman) */
    public function resetWinners()
    {
        $event = Event::where('is_active', true)->firstOrFail();
        $count = DoorprizeWinner::where('event_id', $event->id)->count();
        DoorprizeWinner::where('event_id', $event->id)->delete();

        // Reset layar display ke idle
        Cache::put(self::CACHE_KEY, [
            'state'      => 'idle',
            'doorprize'  => null,
            'winner'     => null,
            'sample_npk' => [],
            'updated_at' => Str::uuid()->toString(),
        ], self::CACHE_TTL * 60);

        return back()->with('success', "{$count} pemenang berhasil direset. Semua peserta kembali eligible untuk spin.");
    }

    // ── Display Sync API ──────────────────────────────────────

    /** Admin: mulai spin → kirim state ke cache */
    public function startDisplay(Request $request)
    {
        $request->validate([
            'doorprize_nama'   => 'required|string',
            'doorprize_type'   => 'required|string',
            'doorprize_gambar' => 'nullable|string',
            'sample_npk'       => 'nullable|array',
        ]);

        Cache::put(self::CACHE_KEY, [
            'state'   => 'spinning',
            'doorprize' => [
                'nama'   => $request->doorprize_nama,
                'type'   => $request->doorprize_type,
                'gambar' => $request->doorprize_gambar,
            ],
            'winner'     => null,
            'sample_npk' => array_slice($request->sample_npk ?? [], 0, 50),
            'updated_at' => Str::uuid()->toString(),
        ], self::CACHE_TTL * 60);

        return response()->json(['ok' => true]);
    }

    /** Admin: stop/reveal winner → kirim pemenang ke cache */
    public function stopDisplay(Request $request)
    {
        $request->validate([
            'winner' => 'required|array',
        ]);

        $current = Cache::get(self::CACHE_KEY, []);

        Cache::put(self::CACHE_KEY, array_merge($current, [
            'state'      => 'winner',
            'winner'     => $request->winner,
            'updated_at' => Str::uuid()->toString(),
        ]), self::CACHE_TTL * 60);

        return response()->json(['ok' => true]);
    }

    /** Admin: reset layar ke idle */
    public function resetDisplay()
    {
        Cache::put(self::CACHE_KEY, [
            'state'      => 'idle',
            'doorprize'  => null,
            'winner'     => null,
            'sample_npk' => [],
            'updated_at' => Str::uuid()->toString(),
        ], self::CACHE_TTL * 60);

        return response()->json(['ok' => true]);
    }

    /** Polling endpoint (public) — display page memanggil ini */
    public function displayStatus()
    {
        $state = Cache::get(self::CACHE_KEY, [
            'state'      => 'idle',
            'doorprize'  => null,
            'winner'     => null,
            'sample_npk' => [],
            'updated_at' => 0,
        ]);

        return response()->json($state);
    }

    /** Halaman layar display (public, untuk TV/proyektor) */
    public function displayPage()
    {
        $event = Event::where('is_active', true)->first();
        return response()
            ->view('admin.doorprizes.display', compact('event'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    // ── Multi-Column Spin (2-50 kolom sekaligus) ──────────────

    /** Admin: kocok pemenang unik untuk tiap kolom (belum tampil ke layar) */
    public function multiDraw(Request $request)
    {
        $request->validate([
            'doorprize_ids'      => 'required|array|min:2|max:50',
            'doorprize_ids.*'    => 'required|exists:doorprizes,id',
            'excluded_jabatan'   => 'nullable|array',
            'excluded_jabatan.*' => 'string',
        ]);

        $event           = Event::where('is_active', true)->firstOrFail();
        $alreadyWon      = DoorprizeWinner::where('event_id', $event->id)->pluck('employee_npk')->toArray();
        $excludedJabatan = DoorprizeExcludeRole::pluck('jabatan')->toArray();
        $allExcluded     = array_unique(array_merge($excludedJabatan, $request->excluded_jabatan ?? []));

        $query = Attendance::where('event_id', $event->id)
            ->whereNotIn('employee_npk', $alreadyWon)
            ->join('employees', 'employees.npk', '=', 'attendances.employee_npk');

        if (!empty($allExcluded)) {
            $query->whereNotIn('employees.jabatan', $allExcluded);
        }

        // Ambil NPK + data karyawan sekaligus dari satu query — NPK dan nama selalu dari baris yang sama
        $eligibleRecords = $query
            ->select('attendances.employee_npk', 'employees.nama', 'employees.subco', 'employees.jabatan')
            ->get()
            ->unique('employee_npk')
            ->values()
            ->shuffle();

        $slotCount = count($request->doorprize_ids);
        if ($eligibleRecords->count() < $slotCount) {
            return response()->json(['error' => 'Peserta eligible ('.$eligibleRecords->count().') kurang dari jumlah kolom ('.$slotCount.').'], 422);
        }

        $picked    = $eligibleRecords->take($slotCount)->values();
        $allNpks   = $eligibleRecords->pluck('employee_npk')->toArray();

        $slots = [];
        foreach ($request->doorprize_ids as $i => $doorprizeId) {
            $doorprize = Doorprize::find($doorprizeId);
            $record    = $picked[$i]; // NPK + nama + subco + jabatan dari baris DB yang sama

            $slots[] = [
                'id'        => $i,
                'doorprize' => [
                    'id'     => $doorprize->id,
                    'nama'   => $doorprize->nama_hadiah,
                    'type'   => $doorprize->type,
                    'gambar' => $doorprize->gambar_url,
                ],
                'state'  => 'idle',
                'winner' => [
                    'npk'    => $record->employee_npk,
                    'nama'   => $record->nama,
                    'subco'  => $record->subco,
                    'jabatan'=> $record->jabatan,
                ],
            ];
        }

        return response()->json([
            'slots'      => $slots,
            'sample_npk' => array_slice($allNpks, 0, 50),
        ]);
    }

    /** Admin: mulai tampilkan & putar semua kolom di layar */
    public function multiStart(Request $request)
    {
        $request->validate([
            'slots'              => 'required|array|min:2|max:50',
            'banner_image'       => 'nullable|string',
            'sample_npk'         => 'nullable|array',
        ]);

        // Re-fetch data karyawan dari DB berdasarkan NPK — cache SELALU sinkron dengan DB
        // Ini mencegah nama salah di display meski ada bug di client atau edge case lain
        $slots = collect($request->slots)->map(function ($slot) {
            $winner = null;
            if (!empty($slot['winner']['npk'])) {
                $emp = Employee::find($slot['winner']['npk']);
                if ($emp) {
                    $winner = [
                        'npk'     => $emp->npk,
                        'nama'    => $emp->nama,
                        'subco'   => $emp->subco,
                        'jabatan' => $emp->jabatan,
                    ];
                }
            }
            return [
                'id'        => $slot['id'],
                'doorprize' => $slot['doorprize'],
                'state'     => 'spinning',
                'winner'    => $winner,
            ];
        })->all();

        Cache::put(self::CACHE_KEY, [
            'mode'         => 'multi',
            'state'        => 'spinning',
            'banner_image' => $request->banner_image,
            'slots'        => $slots,
            'sample_npk'   => array_slice($request->sample_npk ?? [], 0, 50),
            'updated_at'   => Str::uuid()->toString(),
        ], self::CACHE_TTL * 60);

        return response()->json(['ok' => true]);
    }

    /** Admin: stop & reveal pemenang satu kolom */
    public function multiStopSlot(Request $request)
    {
        $request->validate(['slot_id' => 'required|integer']);

        $current = Cache::get(self::CACHE_KEY, []);
        if (($current['mode'] ?? null) !== 'multi') {
            return response()->json(['error' => 'Tidak ada sesi multi-spin aktif.'], 422);
        }

        $slots = collect($current['slots'])->map(function ($slot) use ($request) {
            if ($slot['id'] === $request->slot_id) {
                $slot['state'] = 'winner';
            }
            return $slot;
        })->all();

        $current['slots']      = $slots;
        $current['state']      = collect($slots)->every(fn ($s) => $s['state'] === 'winner') ? 'winner' : 'spinning';
        $current['updated_at'] = Str::uuid()->toString();

        Cache::put(self::CACHE_KEY, $current, self::CACHE_TTL * 60);

        return response()->json(['ok' => true]);
    }

    /** Admin: stop & reveal semua kolom sekaligus */
    public function multiStopAll()
    {
        $current = Cache::get(self::CACHE_KEY, []);
        if (($current['mode'] ?? null) !== 'multi') {
            return response()->json(['error' => 'Tidak ada sesi multi-spin aktif.'], 422);
        }

        $slots = collect($current['slots'])->map(function ($slot) {
            $slot['state'] = 'winner';
            return $slot;
        })->all();

        $current['slots']      = $slots;
        $current['state']      = 'winner';
        $current['updated_at'] = Str::uuid()->toString();

        Cache::put(self::CACHE_KEY, $current, self::CACHE_TTL * 60);

        return response()->json(['ok' => true]);
    }

    /** Admin: putar ulang satu kolom (kocok ulang pemenang baru) */
    public function multiResetSlot(Request $request)
    {
        $request->validate([
            'slot_id'            => 'required|integer',
            'excluded_jabatan'   => 'nullable|array',
            'excluded_jabatan.*' => 'string',
        ]);

        $current = Cache::get(self::CACHE_KEY, []);
        if (($current['mode'] ?? null) !== 'multi') {
            return response()->json(['error' => 'Tidak ada sesi multi-spin aktif.'], 422);
        }

        $event      = Event::where('is_active', true)->firstOrFail();
        $alreadyWon = DoorprizeWinner::where('event_id', $event->id)->pluck('employee_npk')->toArray();

        // NPK yang sedang dipegang slot lain tidak boleh diundi ulang
        $heldNpk = collect($current['slots'])
            ->where('id', '!=', $request->slot_id)
            ->pluck('winner.npk')->filter()->toArray();

        $query = Attendance::where('event_id', $event->id)
            ->whereNotIn('employee_npk', array_merge($alreadyWon, $heldNpk))
            ->join('employees', 'employees.npk', '=', 'attendances.employee_npk');

        if (!empty($request->excluded_jabatan)) {
            $query->whereNotIn('employees.jabatan', $request->excluded_jabatan);
        }

        $eligibleRecords = $query
            ->select('attendances.employee_npk', 'employees.nama', 'employees.subco', 'employees.jabatan')
            ->get()
            ->unique('employee_npk')
            ->values();

        if ($eligibleRecords->isEmpty()) {
            return response()->json(['error' => 'Tidak ada peserta eligible untuk dikocok ulang.'], 422);
        }

        $newRecord = $eligibleRecords->random();

        $slots = collect($current['slots'])->map(function ($slot) use ($request, $newRecord) {
            if ($slot['id'] === $request->slot_id) {
                $slot['state']  = 'spinning';
                $slot['winner'] = [
                    'npk'     => $newRecord->employee_npk,
                    'nama'    => $newRecord->nama,
                    'subco'   => $newRecord->subco,
                    'jabatan' => $newRecord->jabatan,
                ];
            }
            return $slot;
        })->all();

        $current['slots']      = $slots;
        $current['state']      = 'spinning';
        $current['updated_at'] = Str::uuid()->toString();

        Cache::put(self::CACHE_KEY, $current, self::CACHE_TTL * 60);

        // Kembalikan slot yang baru saja diupdate agar JS bisa sinkron
        $updatedSlot = collect($slots)->firstWhere('id', $request->slot_id);
        return response()->json(['ok' => true, 'slot' => $updatedSlot]);
    }

    /** Admin: reset semua kolom kembali ke idle (sesi multi dibatalkan) */
    public function multiResetAll()
    {
        Cache::put(self::CACHE_KEY, [
            'state'      => 'idle',
            'doorprize'  => null,
            'winner'     => null,
            'sample_npk' => [],
            'updated_at' => Str::uuid()->toString(),
        ], self::CACHE_TTL * 60);

        return response()->json(['ok' => true]);
    }

    /** Admin: simpan semua pemenang yang sudah terungkap ke history */
    public function multiSaveWinners()
    {
        $current = Cache::get(self::CACHE_KEY, []);
        if (($current['mode'] ?? null) !== 'multi') {
            return response()->json(['error' => 'Tidak ada sesi multi-spin aktif.'], 422);
        }

        $event   = Event::where('is_active', true)->firstOrFail();
        $saved   = 0;
        $skipped = 0;
        $mismatch = []; // NPK yang nama-nya beda antara cache dan database

        foreach ($current['slots'] as $slot) {
            if ($slot['state'] !== 'winner' || empty($slot['winner']) || empty($slot['doorprize']['id'])) {
                continue;
            }

            $cachedNpk  = $slot['winner']['npk'];
            $cachedNama = $slot['winner']['nama'] ?? '';

            // Re-fetch dari DB untuk memastikan NPK valid dan nama masih sinkron
            $employee = Employee::find($cachedNpk);
            if (!$employee) {
                $mismatch[] = "NPK {$cachedNpk} ({$cachedNama}) tidak ditemukan di database.";
                continue;
            }

            // Catat jika nama berbeda (data karyawan diupdate setelah spin)
            if ($employee->nama !== $cachedNama) {
                $mismatch[] = "NPK {$cachedNpk}: saat spin '{$cachedNama}', di database sekarang '{$employee->nama}'.";
            }

            $exists = DoorprizeWinner::where('event_id', $event->id)
                ->where('employee_npk', $cachedNpk)->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            DoorprizeWinner::create([
                'doorprize_id' => $slot['doorprize']['id'],
                'event_id'     => $event->id,
                'employee_npk' => $cachedNpk,
                'won_at'       => now(),
            ]);
            $saved++;
        }

        return response()->json([
            'ok'       => true,
            'saved'    => $saved,
            'skipped'  => $skipped,
            'mismatch' => $mismatch, // kosong jika semua data sinkron
        ]);
    }
}
