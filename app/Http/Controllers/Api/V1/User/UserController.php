<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Actions\User\UserRegistrar;
use App\Actions\User\UserVerifier;
use App\Exceptions\Repository\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CreateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Repositories\User\CreateUserDTO;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class UserController extends Controller
{
    /**
     * @param CreateUserRequest $request
     * @param UserRegistrar $registrar
     * @return UserResource
     * @throws Throwable
     */
    public function create(
        CreateUserRequest $request,
        UserRegistrar $registrar
    ): UserResource {
        $dto = new CreateUserDTO(
            $request->name,
            $request->email,
            $request->password
        );

        $createdUser = $registrar->register($dto);

        return new UserResource($createdUser);
    }

    public function verify(string $verificationToken, UserVerifier $userVerifier): JsonResponse
    {
        try {
            $userVerifier->verifyByToken($verificationToken);
        } catch (ModelNotFoundException $exception) {
            throw new NotFoundHttpException();
        }

        return new JsonResponse();
    }
}
