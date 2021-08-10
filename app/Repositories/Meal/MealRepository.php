<?php

namespace App\Repositories\Meal;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Models\Meal;

class MealRepository implements MealRepositoryInterface
{
    public function add(MealDTO $dto): Meal
    {
        $meal = new Meal();
        $meal->price = $dto->price;
        $meal->category_id = $dto->categoryId;
        $meal->name = $dto->name;
        $meal->is_vegan = $dto->isVegan;
        $meal->is_spicy = $dto->isSpicy;
        $meal->save();

        return $meal;
    }

    /**
     * {@inheritDoc}
     */
    public function get(int $id): Meal
    {
        $meal = Meal::find($id);

        if (is_null($meal)) {
            throw new ModelNotFoundException();
        }

        return $meal;
    }

    /**
     * {@inheritDoc}
     */
    public function getByIds(array $ids, string $keyBy = null)
    {
        $meals = Meal::whereIn('id', $ids)->get();

        if (!is_null($keyBy)) {
            return $meals->keyBy($keyBy);
        }

        return $meals;
    }

    /**
     * {@inheritDoc}
     */
    public function update(Meal $meal, MealDTO $dto): Meal
    {
        $meal->price = $dto->price;
        $meal->category_id = $dto->categoryId;
        $meal->name = $dto->name;
        $meal->is_vegan = $dto->isVegan;
        $meal->is_spicy = $dto->isSpicy;
        $meal->save();

        return $meal;
    }

    public function remove(Meal $meal): void
    {
        $meal->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function list(ListMealsDTO $dto)
    {
        $meals = Meal::query();

        if (!is_null($dto->categoryId)) {
            $meals = $meals->where('category_id', $dto->categoryId);
        }

        if (!is_null($dto->isSpicy)) {
            $meals = $meals->where('is_spicy', $dto->isSpicy);
        }

        if (!is_null($dto->isVegan)) {
            $meals = $meals->where('is_vegan', $dto->isVegan);
        }

        return $meals->limit($dto->limit)->offset($dto->offset)->get();
    }
}
