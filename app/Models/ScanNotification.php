<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanNotification extends Model
{
    protected $fillable = ['event_id', 'employee_npk', 'attendance_id', 'scanned_at'];

    protected $casts = ['scanned_at' => 'datetime'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_npk', 'npk');
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }
}
