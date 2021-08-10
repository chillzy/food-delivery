<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\ListMealsRequest;
use App\Http\Resources\V1\MealResource;
use App\Models\Meal;
use App\Repositories\Meal\ListMealsDTO;
use App\Repositories\Meal\MealRepositoryInterface;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MealController extends Controller
{
    private MealRepositoryInterface $mealRepository;

    public function __construct(MealRepositoryInterface $mealRepository)
    {
        $this->mealRepository = $mealRepository;
    }

    public function get(int $id): MealResource
    {
        return new MealResource($this->getMeal($id));
    }

    public function list(ListMealsRequest $request): ResourceCollection
    {
        $dto = new ListMealsDTO(
            $request->limit,
            $request->offset,
            $request->categoryId,
            $request->isVegan,
            $request->isSpicy
        );

        $meals = $this->mealRepository->list($dto);

        return MealResource::collection($meals);
    }

    private function getMeal(int $id): Meal
    {
        try {
            $meal = $this->mealRepository->get($id);
        } catch (ModelNotFoundException $exception) {
            throw new NotFoundHttpException();
        }

        return $meal;
    }
}
