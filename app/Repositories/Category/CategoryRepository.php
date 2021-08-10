<?php

namespace App\Repositories\Category;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Models\Category;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function add(string $name): Category
    {
        $newCategory = new Category();
        $newCategory->name = $name;
        $newCategory->save();

        return $newCategory;
    }

    /**
     * {@inheritDoc}
     */
    public function get(int $id): Category
    {
        $category = Category::find($id);

        if (is_null($category)) {
            throw new ModelNotFoundException();
        }

        return $category;
    }

    /**
     * {@inheritDoc}
     */
    public function getWithMeals(int $id): Category
    {
        /** @var Category|null $category */
        $category = Category::with('meals')->find($id);

        if (is_null($category)) {
            throw new ModelNotFoundException();
        }

        return $category;
    }

    public function remove(Category $category): void
    {
        $category->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function list(
        int $limit = self::DEFAULT_LIST_LIMIT,
        int $offset = self::DEFAULT_LIST_OFFSET
    ) {
        return Category::withCount('meals')->limit($limit)->offset($offset)->get();
    }
}
