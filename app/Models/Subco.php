<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subco extends Model
{
    protected $fillable = ['nama', 'singkatan', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function employees()
    {
        return Employee::where('subco', $this->nama);
    }
}
