<?php

namespace App\Repositories\EmailVerification;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Models\EmailVerification;
use App\Models\User;

interface EmailVerificationRepositoryInterface
{
    public function addForUser(User $user): EmailVerification;

    /**
     * @param string $token
     * @return EmailVerification
     * @throws ModelNotFoundException
     */
    public function getByToken(string $token): EmailVerification;
}
