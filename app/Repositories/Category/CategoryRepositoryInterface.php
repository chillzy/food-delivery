<?php

namespace App\Repositories\Category;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Models\Category;
use Illuminate\Support\Collection;

interface CategoryRepositoryInterface
{
    public const DEFAULT_LIST_LIMIT = 50;
    public const DEFAULT_LIST_OFFSET = 0;

    public function add(string $name): Category;

    /**
     * @param int $id
     * @return Category
     * @throws ModelNotFoundException
     */
    public function get(int $id): Category;

    /**
     * @param int $id
     * @return Category
     * @throws ModelNotFoundException
     */
    public function getWithMeals(int $id): Category;

    public function remove(Category $category): void;

    /**
     * @param int $limit
     * @param int $offset
     * @return Collection|Category[]
     */
    public function list(
        int $limit = self::DEFAULT_LIST_LIMIT,
        int $offset = self::DEFAULT_LIST_OFFSET
    );
}
