<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slide extends Model
{
    protected $fillable = [
        'event_id', 'type', 'file_path', 'judul', 'caption', 'urutan', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
}
