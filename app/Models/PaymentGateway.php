<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $id
 * @property mixed $gateway_no
 * @property mixed $name
 * @property mixed $slug
 * @property mixed $merchant_code
 * @property mixed $terminal_id
 * @property mixed $secret_key
 * @property mixed $username
 * @property mixed $password
 * @property mixed $logo
 * @property mixed $is_primary
 * @property mixed $status
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class PaymentGateway extends Model
{
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'gateway_no',
        'name',
        'slug',
        'merchant_code',
        'terminal_id',
        'secret_key',
        'username',
        'password',
        'is_primary',
        'status',
    ];
}
