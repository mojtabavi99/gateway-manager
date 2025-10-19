<?php

namespace App\Models;

use App\Enums\Gateway;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property mixed $id
 * @property mixed $bonded_transaction_id
 * @property mixed $user_id
 * @property mixed $gateway
 * @property mixed $payment_id
 * @property mixed $referral_code
 * @property mixed $amount
 * @property mixed $status
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class Transaction extends Model
{
    public const PAYMENT_METHOD_ONLINE = 'online';
    public const PAYMENT_METHOD_CASH = 'cash';
    public const PAYMENT_METHOD_INSTALLMENT = 'installment';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'bonded_transaction_id',
        'user_id',
        'gateway',
        'payment_id',
        'referral_code',
        'amount',
        'status',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
