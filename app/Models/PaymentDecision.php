<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentDecision extends Model
{
    public const ACTION_VERIFIED = 'verified';

    public const ACTION_REJECTED = 'rejected';

    public const ACTION_NOT_RECEIVED = 'not_received';

    public const ACTION_RESERVATION_CONFIRMED = 'reservation_confirmed';

    protected $fillable = [
        'payment_id',
        'administrator_id',
        'action',
        'reason',
    ];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function administrator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administrator_id');
    }
}
