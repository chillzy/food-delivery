<?php

namespace App\Repositories\UserAddress;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Collection;

interface UserAddressRepositoryInterface
{
    public function add(UserAddressDTO $dto, User $user): UserAddress;

    /**
     * @param int $id
     * @return UserAddress
     * @throws ModelNotFoundException
     */
    public function get(int $id): UserAddress;

    public function update(UserAddress $address, UserAddressDTO $dto): UserAddress;

    public function remove(UserAddress $address): void;

    /**
     * @param User $user
     * @return Collection|UserAddress[]
     */
    public function listForUser(User $user);
}
