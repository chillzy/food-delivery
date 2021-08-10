<?php

namespace App\Repositories\Meal;

class ListMealsDTO
{
    public int $limit;
    public int $offset;
    public ?int $categoryId;
    public ?bool $isVegan;
    public ?bool $isSpicy;

    public function __construct(
        int $limit = MealRepositoryInterface::DEFAULT_LIST_LIMIT,
        int $offset = MealRepositoryInterface::DEFAULT_LIST_OFFSET,
        ?int $categoryId = null,
        ?bool $isVegan = null,
        ?bool $isSpicy = null
    ) {
        $this->limit = $limit;
        $this->offset = $offset;
        $this->categoryId = $categoryId;
        $this->isVegan = $isVegan;
        $this->isSpicy = $isSpicy;
    }
}
