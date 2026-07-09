<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $primaryKey = 'npk';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'npk', 'nama', 'subco', 'jabatan', 'ukuran_baju', 'email', 'no_telpon',
    ];

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'employee_npk', 'npk');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'employee_npk', 'npk');
    }

    public function doorprizeWins(): HasMany
    {
        return $this->hasMany(DoorprizeWinner::class, 'employee_npk', 'npk');
    }

    public function confirmations(): HasMany
    {
        return $this->hasMany(AttendanceConfirmation::class, 'employee_npk', 'npk');
    }

    public function scanNotifications(): HasMany
    {
        return $this->hasMany(ScanNotification::class, 'employee_npk', 'npk');
    }
}
