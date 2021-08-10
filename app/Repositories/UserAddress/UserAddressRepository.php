<?php

namespace App\Repositories\UserAddress;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Models\User;
use App\Models\UserAddress;

class UserAddressRepository implements UserAddressRepositoryInterface
{
    public function add(UserAddressDTO $dto, User $user): UserAddress
    {
        $newAddress = new UserAddress();

        $this->fillAddress($newAddress, $dto);

        $newAddress->user_id = $user->id;
        $newAddress->save();

        return $newAddress;
    }

    /**
     * {@inheritDoc}
     */
    public function get(int $id): UserAddress
    {
        $address = UserAddress::find($id);

        if (is_null($address)) {
            throw new ModelNotFoundException();
        }

        return $address;
    }

    public function update(UserAddress $address, UserAddressDTO $dto): UserAddress
    {
        $address = $this->fillAddress($address, $dto);
        $address->save();

        return $address;
    }

    private function fillAddress(UserAddress $address, UserAddressDTO $dto): UserAddress
    {
        $address->street = $dto->street;
        $address->house = $dto->house;
        $address->building = $dto->building;
        $address->entrance = $dto->entrance;
        $address->floor = $dto->floor;
        $address->apartment = $dto->apartment;
        $address->intercom = $dto->intercom;
        $address->comment = $dto->comment;

        return $address;
    }

    public function remove(UserAddress $address): void
    {
        $address->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function listForUser(User $user)
    {
        return $user->addresses->all();
    }
}
