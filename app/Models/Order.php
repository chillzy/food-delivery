<?php

namespace App\Models;

use App\Exceptions\State\StateNotExistsException;
use App\Models\States\Order\OrderStatus;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $user_id
 * @property int $price
 * @property string $status
 * @property string $payment_type
 * @property string $cancel_reason
 * @property DateTime $created_at
 * @property string $updated_at
 *
 * @property User $user
 * @property Collection|OrderMeal[] $meals
 *
 * @mixin Builder
 */
class Order extends Model
{
    public const PAYMENT_TYPE_CARD_COURIER = 'CARD_COURIER';
    public const PAYMENT_TYPE_CASH = 'CASH';
    public const PAYMENT_TYPES = [
        self::PAYMENT_TYPE_CARD_COURIER,
        self::PAYMENT_TYPE_CASH,
    ];

    public $incrementing = false;

    protected $casts = [
        'created_at' => 'datetime',
    ];

    protected $keyType = 'string';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function meals(): HasMany
    {
        return $this->hasMany(OrderMeal::class);
    }

    /**
     * @return OrderStatus
     * @throws StateNotExistsException
     */
    public function getStatus(): OrderStatus
    {
        return OrderStatus::create($this);
    }
}
