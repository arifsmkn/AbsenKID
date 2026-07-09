<?php

namespace App\Events;

use App\Models\Attendance;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GuestScanned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Attendance $attendance) {}

    public function broadcastOn(): array
    {
        return [new Channel('attendance.' . $this->attendance->event_id)];
    }

    public function broadcastWith(): array
    {
        return [
            'npk' => $this->attendance->employee->npk,
            'nama' => $this->attendance->employee->nama,
            'subco' => $this->attendance->employee->subco,
            'jabatan' => $this->attendance->employee->jabatan,
            'scanned_at' => $this->attendance->scanned_at->format('H:i:s'),
        ];
    }
}
