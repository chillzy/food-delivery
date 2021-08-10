<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $price
 * @property int $category_id
 * @property string $name
 * @property bool $is_vegan
 * @property bool $is_spicy
 *
 * @property User $user
 *
 * @mixin Builder
 */
class Meal extends Model
{
    use SoftDeletes;

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
