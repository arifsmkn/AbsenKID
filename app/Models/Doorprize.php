<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doorprize extends Model
{
    protected $fillable = ['event_id', 'nama_hadiah', 'gambar', 'jumlah', 'urutan', 'type'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function winners(): HasMany
    {
        return $this->hasMany(DoorprizeWinner::class);
    }

    public function getGambarUrlAttribute(): ?string
    {
        return $this->gambar ? asset('storage/' . $this->gambar) : null;
    }
}
