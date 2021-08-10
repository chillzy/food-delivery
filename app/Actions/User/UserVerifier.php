<?php

namespace App\Actions\User;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Repositories\EmailVerification\EmailVerificationRepositoryInterface;

class UserVerifier
{
    private EmailVerificationRepositoryInterface $emailVerificationRepository;

    public function __construct(EmailVerificationRepositoryInterface $emailVerificationRepository)
    {
        $this->emailVerificationRepository = $emailVerificationRepository;
    }

    /**
     * @param string $verificationToken
     * @throws ModelNotFoundException
     */
    public function verifyByToken(string $verificationToken): void
    {
        $emailVerification = $this->emailVerificationRepository->getByToken($verificationToken);

        $emailVerification->user->markEmailAsVerified();
    }
}
