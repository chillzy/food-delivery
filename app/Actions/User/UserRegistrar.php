<?php

namespace App\Actions\User;

use App\Models\User;
use App\Notifications\VerifyEmail;
use App\Repositories\User\CreateUserDTO;
use App\Repositories\EmailVerification\EmailVerificationRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Throwable;

class UserRegistrar
{
    private UserRepositoryInterface $userRepository;
    private EmailVerificationRepositoryInterface $emailVerificationRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        EmailVerificationRepositoryInterface $emailVerificationRepository
    ) {
        $this->userRepository = $userRepository;
        $this->emailVerificationRepository = $emailVerificationRepository;
    }

    /**
     * @param CreateUserDTO $createUserDTO
     * @return User
     * @throws Throwable
     */
    public function register(CreateUserDTO $createUserDTO): User
    {
        DB::beginTransaction();
        try {
            $newUser = $this->userRepository->add($createUserDTO);

            $newEmailVerification = $this->emailVerificationRepository->addForUser($newUser);

            Notification::send([$newUser], new VerifyEmail($newEmailVerification));

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }

        return $newUser;
    }
}
