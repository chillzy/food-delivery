<?php

namespace App\Repositories\Admin;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Models\Admin;

interface AdminRepositoryInterface
{
    /**
     * @param string $email
     * @return Admin
     * @throws ModelNotFoundException
     */
    public function getByEmail(string $email): Admin;

    public function add(CreateAdminDTO $dto): Admin;
}
