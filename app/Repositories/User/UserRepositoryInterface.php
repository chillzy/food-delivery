<?php

namespace App\Repositories\User;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Models\User;

interface UserRepositoryInterface
{
    public function add(CreateUserDTO $dto): User;

    public function remove(User $user): void;

    /**
     * @param int $id
     * @return User
     * @throws ModelNotFoundException
     */
    public function get(int $id): User;
}
