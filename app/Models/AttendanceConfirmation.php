<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceConfirmation extends Model
{
    protected $fillable = ['event_id', 'employee_npk', 'status', 'confirmed_at'];

    protected $casts = ['confirmed_at' => 'datetime'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_npk', 'npk');
    }

    public function isHadir(): bool
    {
        return $this->status === 'hadir';
    }
}
