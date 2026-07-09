<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationSend extends Model
{
    protected $fillable = [
        'invitation_id', 'employee_npk', 'channel',
        'target', 'status', 'error_message', 'sent_at',
    ];

    protected $casts = ['sent_at' => 'datetime'];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_npk', 'npk');
    }
}
