<?php

namespace App\Repositories\User;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function add(CreateUserDTO $dto): User
    {
        $newUser = new User();
        $newUser->name = $dto->name;
        $newUser->email = $dto->email;
        $newUser->password = Hash::make($dto->password);
        $newUser->save();

        return $newUser;
    }

    public function remove(User $user): void
    {
        $user->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function get(int $id): User
    {
        $user = User::find($id);

        if (is_null($user)) {
            throw new ModelNotFoundException();
        }

        return $user;
    }
}
