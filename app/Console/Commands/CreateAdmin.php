<?php

namespace App\Console\Commands;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Models\Admin;
use App\Repositories\Admin\AdminRepositoryInterface;
use App\Repositories\Admin\CreateAdminDTO;
use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    protected $signature = 'admin:create {name} {email} {password}';

    private AdminRepositoryInterface $adminRepository;

    public function __construct(AdminRepositoryInterface $adminRepository)
    {
        parent::__construct();

        $this->adminRepository = $adminRepository;
    }

    public function handle(): void
    {
        $email = $this->argument('email');

        $existingAdmin = $this->getAdmin($email);
        if ($existingAdmin) {
            $this->error("Admin with email [{$email}] already exists");

            return;
        }

        $dto = new CreateAdminDTO($this->argument('name'), $email, $this->argument('password'));

        $this->adminRepository->add($dto);
    }

    private function getAdmin(string $email): ?Admin
    {
        try {
            $existingAdmin = $this->adminRepository->getByEmail($email);
        } catch (ModelNotFoundException $exception) {
            $existingAdmin = null;
        }

        return $existingAdmin;
    }
}
