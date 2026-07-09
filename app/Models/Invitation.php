<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invitation extends Model
{
    protected $fillable = ['event_id', 'employee_npk', 'qr_code', 'is_confirmed'];

    protected $casts = ['is_confirmed' => 'boolean'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_npk', 'npk');
    }

    public function attendance(): HasOne
    {
        return $this->hasOne(Attendance::class);
    }

    public function sends(): HasMany
    {
        return $this->hasMany(InvitationSend::class);
    }
}
