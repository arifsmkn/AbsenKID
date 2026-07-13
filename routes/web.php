<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\DoorprizeExcludeRoleController;
use App\Http\Controllers\Admin\SubcoController;
use App\Http\Controllers\Peserta\AuthController as PesertaAuth;
use App\Http\Controllers\Peserta\DashboardController as PesertaDashboard;
use App\Http\Controllers\Peserta\QrController as PesertaQr;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DoorprizeController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\InvitationController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SlideController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ScanController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [PublicController::class, 'index'])->name('home');

// Live absensi publik (tanpa login)
Route::get('/liveabsensi', [PublicController::class, 'liveAbsensi'])->name('liveabsensi');
Route::get('/liveabsensi/data', [PublicController::class, 'liveAbsensiData'])->name('liveabsensi.data');

// Doorprize display (public — untuk TV/proyektor)
Route::get('/doorprize-display', [DoorprizeController::class, 'displayPage'])->name('doorprizes.displayPage');
Route::get('/doorprize-display/status', [DoorprizeController::class, 'displayStatus'])->name('doorprizes.displayStatus');

// Redirect /dashboard ke admin dashboard
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

// Kiosk scan (public, no auth)
Route::get('/scan', [ScanController::class, 'index'])->name('scan.index');
Route::post('/scan', [ScanController::class, 'process'])->name('scan.process');
Route::post('/scan/npk', [ScanController::class, 'scanByNpk'])->name('scan.npk');
Route::get('/scan/qr/{qr_code}', [ScanController::class, 'scanQr'])->name('scan.qr');

// TV notification display (public)
Route::get('/scan/tv', [ScanController::class, 'tvPage'])->name('scan.tv');
Route::get('/scan/tv/queue', [ScanController::class, 'tvQueue'])->name('scan.tv.queue');
Route::get('/scan/tv/latest', [ScanController::class, 'tvLatest'])->name('scan.tv.latest');

// Auth routes
require __DIR__ . '/auth.php';

// ── Peserta routes ──────────────────────────────────────
Route::prefix('peserta')->name('peserta.')->group(function () {
    Route::get('login',  [PesertaAuth::class, 'showLogin'])->name('login');
    Route::post('login', [PesertaAuth::class, 'login'])->name('login.post');
    Route::post('logout',[PesertaAuth::class, 'logout'])->name('logout');
    Route::get('cek-doorprize', [PesertaAuth::class, 'cekDoorprize'])->name('cek-doorprize');

    Route::middleware('peserta')->group(function () {
        Route::get('dashboard',         [PesertaDashboard::class, 'index'])->name('dashboard');
        Route::get('konfirmasi',        [PesertaDashboard::class, 'showKonfirmasi'])->name('konfirmasi');
        Route::post('konfirmasi',       [PesertaDashboard::class, 'postKonfirmasi'])->name('konfirmasi.post');
        Route::get('tidak-hadir',       [PesertaDashboard::class, 'tidakHadir'])->name('tidak-hadir');
        Route::get('qr',                [PesertaQr::class, 'show'])->name('qr');
    });
});

// Authenticated routes (admin + user)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Employee master data — export/import HARUS sebelum resource agar tidak ditangkap {employee}
    Route::post('employees/import', [EmployeeController::class, 'import'])->name('employees.import');
    Route::get('employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::get('employees/template', [EmployeeController::class, 'downloadTemplate'])->name('employees.template');
    Route::delete('employees/clear-all', [EmployeeController::class, 'clearAll'])->name('employees.clear-all');
    Route::resource('employees', EmployeeController::class);

    // Events
    Route::resource('events', EventController::class)->middleware('role:admin');
    Route::post('events/{event}/activate', [EventController::class, 'activate'])->name('events.activate')->middleware('role:admin');

    // Slides
    Route::resource('events.slides', SlideController::class)->except(['show'])->middleware('role:admin');

    // Invitations
    Route::get('invitations', [InvitationController::class, 'index'])->name('invitations.index');
    Route::post('invitations/generate/{event}', [InvitationController::class, 'generate'])->name('invitations.generate')->middleware('role:admin');
    Route::get('invitations/{invitation}/qr', [InvitationController::class, 'showQr'])->name('invitations.qr');
    Route::post('invitations/{invitation}/send', [InvitationController::class, 'sendOne'])->name('invitations.sendOne')->middleware('role:admin');
    Route::post('invitations/send-all', [InvitationController::class, 'sendAll'])->name('invitations.sendAll')->middleware('role:admin');
    Route::post('invitations/send-test', [InvitationController::class, 'sendTest'])->name('invitations.sendTest')->middleware('role:admin');
    Route::post('invitations/confirm-all', [InvitationController::class, 'confirmAll'])->name('invitations.confirmAll')->middleware('role:admin');
    Route::get('invitations/send-history', [InvitationController::class, 'sendHistory'])->name('invitations.sendHistory');

    // Attendance
    Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('attendances/manual', [AttendanceController::class, 'manualStore'])->name('attendances.manual');
    Route::post('attendances/manual-subco', [AttendanceController::class, 'manualStoreBySubco'])->name('attendances.manualSubco')->middleware('role:admin');
    Route::post('attendances/konfirmasi', [AttendanceController::class, 'updateKonfirmasi'])->name('attendances.konfirmasi');
    Route::delete('attendances/{attendance}', [AttendanceController::class, 'destroy'])->name('attendances.destroy')->middleware('role:admin');

    // Doorprize
    Route::resource('doorprizes', DoorprizeController::class)->middleware('role:admin');
    Route::get('doorprize/spin', [DoorprizeController::class, 'spin'])->name('doorprizes.spin');
    Route::post('doorprize/draw', [DoorprizeController::class, 'draw'])->name('doorprizes.draw');
    Route::post('doorprize/save-winner', [DoorprizeController::class, 'saveWinner'])->name('doorprizes.saveWinner');
    Route::get('doorprize/winners', [DoorprizeController::class, 'winners'])->name('doorprizes.winners');
    Route::post('doorprize/start-display', [DoorprizeController::class, 'startDisplay'])->name('doorprizes.startDisplay');
    Route::post('doorprize/stop-display', [DoorprizeController::class, 'stopDisplay'])->name('doorprizes.stopDisplay');
    Route::post('doorprize/reset-display', [DoorprizeController::class, 'resetDisplay'])->name('doorprizes.resetDisplay');
    Route::post('doorprize/multi/draw', [DoorprizeController::class, 'multiDraw'])->name('doorprizes.multiDraw');
    Route::post('doorprize/multi/start', [DoorprizeController::class, 'multiStart'])->name('doorprizes.multiStart');
    Route::post('doorprize/multi/stop-slot', [DoorprizeController::class, 'multiStopSlot'])->name('doorprizes.multiStopSlot');
    Route::post('doorprize/multi/stop-all', [DoorprizeController::class, 'multiStopAll'])->name('doorprizes.multiStopAll');
    Route::post('doorprize/multi/reset-slot', [DoorprizeController::class, 'multiResetSlot'])->name('doorprizes.multiResetSlot');
    Route::post('doorprize/multi/reset-all', [DoorprizeController::class, 'multiResetAll'])->name('doorprizes.multiResetAll');
    Route::post('doorprize/multi/save-winners', [DoorprizeController::class, 'multiSaveWinners'])->name('doorprizes.multiSaveWinners');
    Route::post('doorprize/reset-winners', [DoorprizeController::class, 'resetWinners'])->name('doorprizes.resetWinners')->middleware('role:admin');
    Route::delete('doorprize/winners/{winner}', [DoorprizeController::class, 'destroyWinner'])->name('doorprizes.destroyWinner')->middleware('role:admin');

    // Doorprize Exclude Roles
    Route::get('doorprize-exclude-roles', [DoorprizeExcludeRoleController::class, 'index'])->name('doorprize-exclude-roles.index')->middleware('role:admin');
    Route::post('doorprize-exclude-roles', [DoorprizeExcludeRoleController::class, 'store'])->name('doorprize-exclude-roles.store')->middleware('role:admin');
    Route::delete('doorprize-exclude-roles/{doorprizeExcludeRole}', [DoorprizeExcludeRoleController::class, 'destroy'])->name('doorprize-exclude-roles.destroy')->middleware('role:admin');

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index')->middleware('role:admin');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update')->middleware('role:admin');

    // SubCo master data
    Route::resource('subcos', SubcoController::class)->except(['create', 'show']);

    // Dark/light mode toggle
    Route::post('set-mode', function (\Illuminate\Http\Request $request) {
        \App\Models\Setting::set('app_mode', $request->mode === 'dark' ? 'dark' : 'light');
        return response()->json(['ok' => true]);
    })->name('admin.set-mode');
});
