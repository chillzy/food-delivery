<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CreateOrUpdateMealRequest;
use App\Http\Requests\V1\ListMealsRequest;
use App\Http\Resources\V1\MealResource;
use App\Models\Meal;
use App\Repositories\Meal\ListMealsDTO;
use App\Repositories\Meal\MealDTO;
use App\Repositories\Meal\MealRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MealController extends Controller
{
    private MealRepositoryInterface $mealRepository;

    public function __construct(MealRepositoryInterface $mealRepository)
    {
        $this->mealRepository = $mealRepository;
    }

    public function create(CreateOrUpdateMealRequest $request): MealResource
    {
        $dto = new MealDTO(
            $request->price,
            $request->categoryId,
            $request->name,
            $request->isVegan,
            $request->isSpicy
        );

        $createdMeal = $this->mealRepository->add($dto);

        return new MealResource($createdMeal);
    }

    public function get(int $id): MealResource
    {
        return new MealResource($this->getMeal($id));
    }

    public function update(int $id, CreateOrUpdateMealRequest $request): MealResource
    {
        $meal = $this->getMeal($id);

        $dto = new MealDTO(
            $request->price,
            $request->categoryId,
            $request->name,
            $request->isVegan,
            $request->isSpicy
        );

        $updatedMeal = $this->mealRepository->update($meal, $dto);

        return new MealResource($updatedMeal);
    }

    public function delete(int $id): JsonResponse
    {
        $meal = $this->getMeal($id);

        $this->mealRepository->remove($meal);

        return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
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
