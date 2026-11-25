<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'stripe_payment_intent_id', 'amount_cents', 'currency', 'status', 'failure_code',
    ];

    protected function casts(): array
    {
        return ['amount_cents' => 'integer'];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
