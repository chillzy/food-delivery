<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $email_verified_at
 * @property string $created_at
 * @property string $updated_at
 * @property null|string $deleted_at
 *
 * @property EmailVerification $emailVerification
 * @property Collection|UserAddress[] $addresses
 *
 * @mixin Builder
 */
class User extends Authenticatable implements JWTSubject
{
    use SoftDeletes, Notifiable;

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return HasOne|EmailVerification
     */
    public function emailVerification(): HasOne
    {
        return $this->hasOne(EmailVerification::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }
}
