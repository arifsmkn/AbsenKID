<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DoorprizeWinner extends Model
{
    protected $fillable = ['doorprize_id', 'event_id', 'employee_npk', 'won_at'];

    protected $casts = ['won_at' => 'datetime'];

    public function doorprize(): BelongsTo
    {
        return $this->belongsTo(Doorprize::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_npk', 'npk');
    }
}
