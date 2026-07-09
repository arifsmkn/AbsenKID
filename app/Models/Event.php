<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'nama', 'tahun', 'tema', 'deskripsi', 'logo', 'wallpaper', 'maps_embed', 'maps_url',
        'lokasi', 'tanggal', 'waktu_mulai', 'waktu_selesai', 'is_active', 'theme_config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'theme_config' => 'array',
        'tanggal' => 'date',
    ];

    public function slides(): HasMany
    {
        return $this->hasMany(Slide::class)->orderBy('urutan');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function doorprizes(): HasMany
    {
        return $this->hasMany(Doorprize::class)->orderBy('urutan');
    }

    public function doorprizeWinners(): HasMany
    {
        return $this->hasMany(DoorprizeWinner::class);
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo ? asset('storage/' . $this->logo) : asset('images/default-logo.png');
    }

    public function getWallpaperUrlAttribute(): ?string
    {
        return $this->wallpaper ? asset('storage/' . $this->wallpaper) : null;
    }
}
