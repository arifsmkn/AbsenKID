<?php

namespace App\Services;

use App\Models\Invitation;
use App\Models\InvitationSend;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class InvitationSendService
{
    /**
     * Kirim undangan satu peserta via semua channel yang aktif.
     * Return array ringkasan: ['wa' => bool|null, 'email' => bool|null]
     */
    public function sendOne(Invitation $invitation): array
    {
        $invitation->load('employee', 'event');
        $result = ['wa' => null, 'email' => null];

        if (Setting::get('wa_enabled') === '1') {
            $result['wa'] = $this->sendWhatsApp($invitation);
        }

        if (Setting::get('email_enabled') === '1') {
            $result['email'] = $this->sendEmail($invitation);
        }

        return $result;
    }

    // ─────────────────────────────────────────────────────────
    // WhatsApp via Fonnte API
    // ─────────────────────────────────────────────────────────
    public function sendWhatsApp(Invitation $invitation): bool
    {
        $phone = $invitation->employee?->no_telpon;
        if (!$phone) {
            $this->logSend($invitation, 'whatsapp', '-', 'failed', 'Nomor telepon kosong');
            return false;
        }

        $token   = Setting::get('wa_api_token', '');
        $apiUrl  = Setting::get('wa_api_url', 'https://api.fonnte.com/send');
        $message = $this->buildMessage($invitation);

        // Normalisasi nomor: hilangkan 0 di depan, pakai 62
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        try {
            $response = Http::timeout(15)
                ->withHeaders(['Authorization' => $token])
                ->post($apiUrl, [
                    'target'  => $phone,
                    'message' => $message,
                ]);

            $body = $response->json();

            if ($response->successful() && ($body['status'] ?? false)) {
                $this->logSend($invitation, 'whatsapp', $phone, 'sent');
                return true;
            }

            $error = $body['reason'] ?? $body['detail'] ?? 'Gagal kirim WA';
            $this->logSend($invitation, 'whatsapp', $phone, 'failed', $error);
            return false;

        } catch (\Throwable $e) {
            $this->logSend($invitation, 'whatsapp', $phone, 'failed', $e->getMessage());
            Log::error('WA send error: ' . $e->getMessage());
            return false;
        }
    }

    // ─────────────────────────────────────────────────────────
    // Email via Laravel Mail
    // ─────────────────────────────────────────────────────────
    public function sendEmail(Invitation $invitation): bool
    {
        $email = $invitation->employee?->email;
        if (!$email) {
            $this->logSend($invitation, 'email', '-', 'failed', 'Email kosong');
            return false;
        }

        try {
            Mail::send([], [], function ($mail) use ($invitation, $email) {
                $event    = $invitation->event;
                $employee = $invitation->employee;
                $qrUrl    = route('peserta.qr');
                $loginUrl = route('peserta.login');

                $message  = $this->buildMessage($invitation);

                $mail->to($email, $employee->nama)
                     ->subject('Undangan ' . ($event?->nama ?? 'Konvensi Improvement Dharma'))
                     ->html($this->buildEmailHtml($invitation));
            });

            $this->logSend($invitation, 'email', $email, 'sent');
            return true;

        } catch (\Throwable $e) {
            $this->logSend($invitation, 'email', $email, 'failed', $e->getMessage());
            Log::error('Email send error: ' . $e->getMessage());
            return false;
        }
    }

    // ─────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────
    private function buildMessage(Invitation $invitation): string
    {
        $event    = $invitation->event;
        $employee = $invitation->employee;
        $qrUrl    = Setting::get('site_url', route('peserta.login'));
        $tanggal  = $event?->tanggal?->isoFormat('dddd, D MMMM Y') ?? '-';
        $waktu    = $event?->waktu_mulai ? substr($event->waktu_mulai, 0, 5) . ' WIB' : '-';
        $lokasi   = $event?->lokasi ?? '-';

        $template = Setting::get('wa_template');
        if ($template) {
            return str_replace(
                ['{nama}', '{event}', '{tanggal}', '{waktu}', '{lokasi}', '{url}', '{npk}'],
                [$employee?->nama, $event?->nama, $tanggal, $waktu, $lokasi, $qrUrl, $employee?->npk],
                $template
            );
        }

        return "Halo *{$employee->nama}*,\n\n"
             . "Anda mendapat undangan untuk:\n"
             . "*{$event?->nama}*\n\n"
             . "📅 {$tanggal}\n"
             . "🕗 {$waktu}\n"
             . "📍 {$lokasi}\n\n"
             . "Silakan login di Portal Peserta untuk melihat & download QR undangan Anda:\n"
             . "{$qrUrl}\n\n"
             . "NPK: *{$employee->npk}*\n\n"
             . "_Pesan ini dikirim otomatis. Jangan reply._";
    }

    private function buildEmailHtml(Invitation $invitation): string
    {
        $event    = $invitation->event;
        $employee = $invitation->employee;
        $qrUrl    = Setting::get('site_url', route('peserta.login'));
        $tanggal  = $event?->tanggal?->isoFormat('dddd, D MMMM Y') ?? '-';
        $waktu    = $event?->waktu_mulai ? substr($event->waktu_mulai, 0, 5) . ' WIB' : '-';
        $nama     = $event?->nama ?? 'Konvensi Improvement Dharma';
        $peserta  = $employee->nama ?? '-';

        return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<style>
  body { font-family:'Segoe UI',Arial,sans-serif; background:#f4f7fb; margin:0; padding:24px 8px; color:#1a2e40; }
  .wrap { max-width:540px; margin:0 auto; }
  .card { background:#fff; border-radius:18px; overflow:hidden; box-shadow:0 4px 28px rgba(36,76,107,0.13); }
  .top-bar { height:6px; background:linear-gradient(90deg,#25563a,#2352fb,#D03F42); }
  .header { background:linear-gradient(135deg,#1a3a28 0%,#25563a 100%); padding:36px 28px 28px; text-align:center; color:#fff; }
  .flower { font-size:28px; letter-spacing:4px; display:block; margin-bottom:10px; }
  .header h1 { margin:0 0 6px; font-size:21px; font-weight:800; letter-spacing:.3px; line-height:1.3; }
  .header p { margin:0; opacity:.7; font-size:12px; letter-spacing:1px; text-transform:uppercase; }
  .body { padding:30px 28px 8px; }
  .greeting { font-size:16px; font-weight:700; color:#25563a; margin:0 0 6px; }
  .intro { font-size:14px; color:#4a5568; margin:0 0 20px; line-height:1.6; }
  .info-box { background:#f8fffe; border:1.5px solid #d1fae5; border-radius:12px; padding:18px 20px; margin:0 0 20px; }
  .info-row { display:flex; align-items:flex-start; gap:10px; margin:8px 0; font-size:14px; }
  .info-icon { font-size:16px; flex-shrink:0; margin-top:1px; }
  .info-label { color:#6b7280; font-size:12px; }
  .info-value { color:#1a2e40; font-weight:600; line-height:1.4; }
  .info-sep { border:none; border-top:1px solid #d1fae5; margin:12px 0; }
  .btn-wrap { text-align:center; margin:20px 0 24px; }
  .btn { display:inline-block; padding:13px 36px; background:linear-gradient(135deg,#25563a,#2d7a49); color:#fff !important; text-decoration:none; border-radius:50px; font-weight:700; font-size:15px; letter-spacing:.3px; }
  .note { font-size:12px; color:#9ca3af; text-align:center; margin:0 0 24px; }
  .footer { text-align:center; padding:16px 20px; color:#9ca3af; font-size:11px; border-top:1px solid #f0f4f8; }
</style>
</head>
<body>
<div class="wrap">
<div class="card">
  <div class="top-bar"></div>
  <div class="header">
    <span class="flower">🌸 🌸 🌸</span>
    <h1>{$nama}</h1>
    <p>Undangan Resmi Peserta</p>
  </div>
  <div class="body">
    <p class="greeting">Dear {$peserta},</p>
    <p class="intro">Kami mengundang Bapak/Ibu untuk dapat menghadiri acara <strong>"{$nama}"</strong> yang akan dilaksanakan pada :</p>

    <div class="info-box">
      <div class="info-row">
        <span class="info-icon">📅</span>
        <div><div class="info-label">Hari, Tanggal</div><div class="info-value">{$tanggal}</div></div>
      </div>
      <hr class="info-sep">
      <div class="info-row">
        <span class="info-icon">🕖</span>
        <div><div class="info-label">Waktu</div><div class="info-value">{$waktu} – selesai</div></div>
      </div>
      <hr class="info-sep">
      <div class="info-row">
        <span class="info-icon">📍</span>
        <div><div class="info-label">Lokasi</div><div class="info-value">Bekasi Convention Center Kawasan CBD Mega City Bekasi, Mega Bekasi Hypermall Lt. 5, Jl. Jend. A. Yani No.1, Bekasi</div></div>
      </div>
      <hr class="info-sep">
      <div class="info-row">
        <span class="info-icon">📌</span>
        <div><div class="info-label">Drop Off</div><div class="info-value">Lobby BCC, Lantai 5</div></div>
      </div>
      <hr class="info-sep">
      <div class="info-row">
        <span class="info-icon">👕</span>
        <div><div class="info-label">Dress Code</div><div class="info-value">Polo / Kemeja Putih</div></div>
      </div>
    </div>

    <div class="btn-wrap">
      <a href="{$qrUrl}" class="btn">🎫 Konfirmasi Kehadiran</a>
    </div>
    <p class="note">Atas perhatian Bapak/Ibu kami ucapkan terimakasih.<br>See you there! 🚀✨</p>
  </div>
  <div class="footer">Pesan ini dikirim otomatis oleh sistem AbsenKID &nbsp;·&nbsp; &copy; Dharma Group</div>
</div>
</div>
</body>
</html>
HTML;
    }

    private function logSend(Invitation $inv, string $channel, string $target, string $status, ?string $error = null): void
    {
        InvitationSend::updateOrCreate(
            ['invitation_id' => $inv->id, 'channel' => $channel],
            [
                'employee_npk'  => $inv->employee_npk,
                'target'        => $target,
                'status'        => $status,
                'error_message' => $error,
                'sent_at'       => $status === 'sent' ? now() : null,
            ]
        );
    }
}
