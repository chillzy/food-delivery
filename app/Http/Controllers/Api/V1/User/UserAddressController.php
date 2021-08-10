<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\CreateOrUpdateUserAddressRequest;
use App\Http\Resources\V1\UserAddressResource;
use App\Models\UserAddress;
use App\Repositories\UserAddress\UserAddressDTO;
use App\Repositories\UserAddress\UserAddressRepositoryInterface;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserAddressController extends Controller
{
    private UserAddressRepositoryInterface $userAddressRepository;

    public function __construct(UserAddressRepositoryInterface $userAddressRepository)
    {
        $this->userAddressRepository = $userAddressRepository;
    }

    public function create(CreateOrUpdateUserAddressRequest $request): UserAddressResource
    {
        $dto = (new UserAddressDTO($request->street, $request->house))
            ->withBuilding($request->building)
            ->withEntrance($request->entrance)
            ->withFloor($request->floor)
            ->withApartment($request->apartment)
            ->withIntercom($request->intercom)
            ->withComment($request->comment);

        $createdUserAddress = $this->userAddressRepository->add($dto, $request->user());

        return new UserAddressResource($createdUserAddress);
    }

    /**
     * @param int $id
     * @return UserAddressResource
     * @throws AuthorizationException
     */
    public function get(int $id): UserAddressResource
    {
        $address = $this->getAddress($id);

        $this->authorize('get', $address);

        return new UserAddressResource($address);
    }

    /**
     * @param int $id
     * @param CreateOrUpdateUserAddressRequest $request
     * @return UserAddressResource
     * @throws AuthorizationException
     */
    public function update(int $id, CreateOrUpdateUserAddressRequest $request): UserAddressResource
    {
        $address = $this->getAddress($id);

        $this->authorize('update', $address);

        $dto = (new UserAddressDTO($request->street, $request->house))
            ->withBuilding($request->building)
            ->withEntrance($request->entrance)
            ->withFloor($request->floor)
            ->withApartment($request->apartment)
            ->withIntercom($request->intercom)
            ->withComment($request->comment);

        $updatedUserAddress = $this->userAddressRepository->update($address, $dto);

        return new UserAddressResource($updatedUserAddress);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function delete(int $id): JsonResponse
    {
        $address = $this->getAddress($id);

        $this->authorize('delete', $address);

        $this->userAddressRepository->remove($address);

        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }

    public function list(Request $request): AnonymousResourceCollection
    {
        $addresses = $this->userAddressRepository->listForUser($request->user());

        return UserAddressResource::collection($addresses);
    }

    private function getAddress(int $id): UserAddress
    {
        try {
            $address = $this->userAddressRepository->get($id);
        } catch (ModelNotFoundException $exception) {
            throw new NotFoundHttpException();
        }

        return $address;
    }
}
