<?php

namespace App\Models;

use App\Enums\Status;
use App\Traits\Strings;
use Database\Factories\UserFactory;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property mixed $id
 * @property mixed $uuid
 * @property mixed $username
 * @property mixed $first_name
 * @property mixed $last_name
 * @property mixed $national_code
 * @property mixed $mobile
 * @property mixed $mobile_verified_at
 * @property mixed $email
 * @property mixed $email_verified_at
 * @property mixed $password
 * @property mixed $invite_code
 * @property mixed $referrer_id
 * @property mixed $tag
 * @property mixed $status
 * @property mixed $created_at
 * @property mixed $updated_at
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, Strings;

    public const TAG_LEAD = 0;
    public const TAG_FRESHMAN = 1;
    public const TAG_REGULAR = 2;
    public const TAG_PERMANENT = 3;
    public const TAG_VIP = 4;
    public const TAG_SUSPICIOUS = 5;
    public const TAG_BLACKLISTED = 6;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
        'national_code',
        'mobile',
        'mobile_verified_at',
        'email',
        'email_verified_at',
        'password',
        'invite_code',
        'referrer_id',
        'tag',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    protected $appends = ['full_name', 'tag_label', 'status_label'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mobile_verified_at' => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getTagLabelAttribute(): string
    {
        return match ((int) $this->tag) {
            self::TAG_LEAD => 'LEAD',
            self::TAG_FRESHMAN => 'FRESHMAN',
            self::TAG_REGULAR => 'REGULAR',
            self::TAG_PERMANENT => 'PERMANENT',
            self::TAG_VIP => 'VIP',
            self::TAG_SUSPICIOUS => 'SUSPICIOUS',
            self::TAG_BLACKLISTED => 'BLACKLISTED',
            default => 'UNKNOWN',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        if (!empty($this->status) && Status::tryFrom($this->status)) {
            return Status::from($this->status)->label();
        }

        return __('enums.status.unknown');
    }

    public function setPasswordAttribute($password): void
    {
        $this->attributes['password'] = bcrypt($password);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($model) {
            $model->uuid = $model->uuid ?? (string)Str::uuid();

            if (empty($model->invite_code)) {
                do {
                    $inviteCode = $model->generateRandomPhrase(5, true, false, false);
                } while (self::query()->where('invite_code', $inviteCode)->exists());

                $model->invite_code = $inviteCode;
            }
        });
    }
}
