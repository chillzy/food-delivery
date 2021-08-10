<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $order_id
 * @property int $meal_id
 * @property int $meal_quantity
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Order $order
 * @property Meal $meal
 *
 * @mixin Builder
 */
class OrderMeal extends Model
{
    protected $table = 'orders_meals';

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class);
    }
}
