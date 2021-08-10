<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property string $street
 * @property int $house
 * @property null|int $building
 * @property null|int $entrance
 * @property null|int $floor
 * @property null|int $apartment
 * @property null|string $intercom
 * @property null|string $comment
 * @property string $created_at
 * @property string $updated_at
 * @property null|string $deleted_at
 *
 * @property User $user
 *
 * @mixin Builder
 */
class UserAddress extends Model
{
    use SoftDeletes;

    protected $table = 'users_addresses';

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
