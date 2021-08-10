<?php

namespace App\Repositories\Meal;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Models\Meal;
use Illuminate\Support\Collection;

interface MealRepositoryInterface
{
    public const DEFAULT_LIST_LIMIT = 50;
    public const DEFAULT_LIST_OFFSET = 0;

    public function add(MealDTO $dto): Meal;

    /**
     * @param int $id
     * @return Meal
     * @throws ModelNotFoundException
     */
    public function get(int $id): Meal;

    /**
     * @param int[] $ids
     * @param string|null $keyBy
     * @return Collection|Meal[]
     */
    public function getByIds(array $ids, string $keyBy = null);

    /**
     * @param Meal $meal
     * @param MealDTO $dto
     * @return Meal
     */
    public function update(Meal $meal, MealDTO $dto): Meal;

    public function remove(Meal $meal): void;

    /**
     * @param ListMealsDTO $dto
     * @return Collection|Meal[]
     */
    public function list(ListMealsDTO $dto);
}
