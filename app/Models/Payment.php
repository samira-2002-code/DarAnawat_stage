<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    public const STATUS_AWAITING_TRANSFER = 'awaiting_transfer';

    public const STATUS_REPORTED = 'reported';

    public const STATUS_VERIFIED = 'verified';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_NOT_RECEIVED = 'not_received';

    protected $fillable = [
        'reservation_id',
        'amount_mad',
        'status',
        'transfer_reported_at',
    ];

    protected function casts(): array
    {
        return [
            'amount_mad' => 'integer',
            'transfer_reported_at' => 'datetime',
        ];
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(PaymentReceipt::class);
    }

    public function decisions(): HasMany
    {
        return $this->hasMany(PaymentDecision::class);
    }
}
