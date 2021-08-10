<?php

namespace App\Repositories\Admin;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminRepository implements AdminRepositoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function getByEmail(string $email): Admin
    {
        $admin = Admin::where('email', $email)->first();

        if (is_null($admin)) {
            throw new ModelNotFoundException();
        }

        return $admin;
    }

    public function add(CreateAdminDTO $dto): Admin
    {
        $admin = new Admin();
        $admin->name = $dto->name;
        $admin->email = $dto->email;
        $admin->password = Hash::make($dto->password);
        $admin->save();

        return $admin;
    }
}
