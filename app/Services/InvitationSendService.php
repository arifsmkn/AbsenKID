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
        $siteUrl  = Setting::get('site_url', url('/'));
        $qrUrl    = rtrim($siteUrl, '/') . '/peserta/login';
        $tanggal  = $event?->tanggal?->isoFormat('dddd, D MMMM Y') ?? '-';
        $waktu    = $event?->waktu_mulai ? substr($event->waktu_mulai, 0, 5) . ' WIB' : '-';
        $nama     = $event?->nama ?? 'Konvensi Improvement Dharma';
        $peserta  = $employee->nama ?? '-';
        $logoUrl      = $event?->logo ? $siteUrl . '/storage/' . $event->logo : null;
        $wallpaperUrl = $event?->wallpaper ? $siteUrl . '/storage/' . $event->wallpaper : null;

        $logoHtml = $logoUrl
            ? "<img src=\"{$logoUrl}\" alt=\"{$nama}\" width=\"140\" height=\"79\" style=\"width:140px;height:auto;max-width:140px;display:block;margin:0 auto 14px;\">"
            : "<div style=\"font-size:28px;font-weight:900;color:#fff;letter-spacing:2px;margin-bottom:8px;\">KID 31</div>";

        $bgAttr  = $wallpaperUrl ? "background=\"{$wallpaperUrl}\"" : '';
        $bgStyle = $wallpaperUrl
            ? "background-image:url('{$wallpaperUrl}');background-size:cover;background-position:center top;background-color:#1b3d26;"
            : "background-color:#1b3d26;";

        return <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#e8edf0;font-family:'Segoe UI',Arial,sans-serif;color:#1a2e40;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#e8edf0;padding:32px 8px;">
<tr><td align="center">
<table width="100%" style="max-width:560px;" cellpadding="0" cellspacing="0">

  <!-- HEADER dengan wallpaper background -->
  <tr><td bgcolor="#1b3d26" {$bgAttr} style="{$bgStyle}border-radius:20px 20px 0 0;">
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr><td style="background:rgba(10,28,16,0.68);border-radius:20px 20px 0 0;padding:36px 32px 28px;text-align:center;">
        {$logoHtml}
        <div style="color:#a8f0c0;font-size:11px;letter-spacing:3px;text-transform:uppercase;margin-bottom:6px;">Undangan Resmi</div>
        <div style="color:#ffffff;font-size:22px;font-weight:800;line-height:1.3;margin-bottom:4px;">{$nama}</div>
        <table cellpadding="0" cellspacing="0" style="margin:10px auto 0;">
          <tr><td style="background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.35);border-radius:20px;padding:5px 18px;">
            <span style="color:#ffffff;font-size:12px;font-weight:600;"> MONOZUKURI S.P.I.R.I.T </span>
          </td></tr>
        </table>
      </td></tr>
    </table>
  </td></tr>

  <!-- DIVIDER -->
  <tr><td bgcolor="#1b3d26" style="background:#1b3d26;height:6px;font-size:1px;line-height:1px;">&nbsp;</td></tr>

  <!-- BODY -->
  <tr><td style="background:#ffffff;padding:8px 32px 0;">
    <p style="margin:0 0 4px;font-size:18px;font-weight:700;color:#1b3d26;">Dear <span style="color:#25563a;">{$peserta}</span>,</p>
    <p style="margin:0 0 22px;font-size:14px;color:#4a5568;line-height:1.7;">Kami mengundang Bapak/Ibu untuk dapat menghadiri acara <strong style="color:#1b3d26;">"{$nama}"</strong> yang akan dilaksanakan pada :</p>

    <!-- INFO CARDS -->
    <table width="100%" cellpadding="0" cellspacing="0">

      <tr><td style="padding:0 0 10px;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0faf4;border-radius:12px;border-left:4px solid #25563a;">
          <tr>
            <td style="padding:14px 16px;width:40px;font-size:22px;vertical-align:middle;">📅</td>
            <td style="padding:14px 4px 14px 0;vertical-align:middle;">
              <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">Hari, Tanggal</div>
              <div style="font-size:15px;font-weight:700;color:#1b3d26;">{$tanggal}</div>
            </td>
          </tr>
        </table>
      </td></tr>

      <tr><td style="padding:0 0 10px;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0faf4;border-radius:12px;border-left:4px solid #25563a;">
          <tr>
            <td style="padding:14px 16px;width:40px;font-size:22px;vertical-align:middle;">🕖</td>
            <td style="padding:14px 4px 14px 0;vertical-align:middle;">
              <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">Waktu</div>
              <div style="font-size:15px;font-weight:700;color:#1b3d26;">{$waktu} – selesai</div>
            </td>
          </tr>
        </table>
      </td></tr>

      <tr><td style="padding:0 0 10px;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0faf4;border-radius:12px;border-left:4px solid #25563a;">
          <tr>
            <td style="padding:14px 16px;width:40px;font-size:22px;vertical-align:middle;">📍</td>
            <td style="padding:14px 4px 14px 0;vertical-align:middle;">
              <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">Lokasi</div>
              <div style="font-size:14px;font-weight:700;color:#1b3d26;line-height:1.4;">Bekasi Convention Center<br><span style="font-weight:400;font-size:13px;color:#4a5568;">Kawasan CBD Mega City Bekasi, Mega Bekasi Hypermall Lt. 5, Jl. Jend. A. Yani No.1, Bekasi</span></div>
            </td>
          </tr>
        </table>
      </td></tr>

      <tr><td style="padding:0 0 10px;">
        <table width="100%" cellpadding="0" cellspacing="0">
          <tr>
            <td style="width:49%;padding-right:6px;">
              <table width="100%" cellpadding="0" cellspacing="0" style="background:#fff8f0;border-radius:12px;border-left:4px solid #e07820;">
                <tr>
                  <td style="padding:12px 14px;">
                    <div style="font-size:20px;margin-bottom:4px;">📌</div>
                    <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">Drop Off</div>
                    <div style="font-size:13px;font-weight:700;color:#7a3800;">Lobby BCC, Lantai 5</div>
                  </td>
                </tr>
              </table>
            </td>
            <td style="width:49%;padding-left:6px;">
              <table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4ff;border-radius:12px;border-left:4px solid #2352fb;">
                <tr>
                  <td style="padding:12px 14px;">
                    <div style="font-size:20px;margin-bottom:4px;">👕</div>
                    <div style="font-size:11px;color:#6b7280;text-transform:uppercase;letter-spacing:.8px;margin-bottom:2px;">Dress Code</div>
                    <div style="font-size:13px;font-weight:700;color:#1a2e8c;">Polo / Kemeja Putih</div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td></tr>

    </table>

    <!-- CTA BUTTON -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0 8px;">
      <tr><td align="center">
        <table cellpadding="0" cellspacing="0" border="0">
          <tr>
            <td bgcolor="#1b3d26" style="background:#1b3d26;border-radius:50px;mso-padding-alt:0;">
              <a href="{$qrUrl}" style="display:block;color:#ffffff;text-decoration:none;font-size:15px;font-weight:700;padding:14px 40px;border-radius:50px;font-family:'Segoe UI',Arial,sans-serif;"> Download QR Undangan</a>
            </td>
          </tr>
        </table>
      </td></tr>
    </table>

    <p style="text-align:center;font-size:13px;color:#6b7280;margin:16px 0 28px;line-height:1.7;">Atas perhatian Bapak/Ibu kami ucapkan terimakasih.<br><strong>See you there! 🚀✨</strong></p>
  </td></tr>

  <!-- FOOTER -->
  <tr><td bgcolor="#1b3d26" style="background:linear-gradient(160deg,#1b3d26,#25563a);border-radius:0 0 20px 20px;padding:18px 32px;text-align:center;">
    <p style="margin:0;color:#a8f0c0;font-size:11px;">Pesan ini dikirim otomatis &nbsp;·&nbsp; &copy; Dharma Group 2026</p>
  </td></tr>

</table>
</td></tr>
</table>
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
