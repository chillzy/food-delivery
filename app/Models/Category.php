<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property null|string $deleted_at
 *
 * @property Meal[]|Collection $meals
 *
 * Virtual properties:
 * @property null|int $meals_count
 *
 * @mixin Builder
 */
class Category extends Model
{
    use SoftDeletes;

    public function meals(): HasMany
    {
        return $this->hasMany(Meal::class);
    }
}
