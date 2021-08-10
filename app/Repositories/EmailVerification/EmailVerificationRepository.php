<?php

namespace App\Repositories\EmailVerification;

use App\Exceptions\Repository\ModelNotFoundException;
use App\Models\EmailVerification;
use App\Models\User;
use Illuminate\Support\Str;

class EmailVerificationRepository implements EmailVerificationRepositoryInterface
{
    public function addForUser(User $user): EmailVerification
    {
        $emailVerification = new EmailVerification();
        $emailVerification->user_id = $user->id;
        $emailVerification->verification_token = Str::uuid();
        $emailVerification->save();

        return $emailVerification;
    }

    /**
     * {@inheritDoc}
     */
    public function getByToken(string $token): EmailVerification
    {
        $emailVerification = EmailVerification::where('verification_token', $token)->first();

        if (is_null($emailVerification)) {
            throw new ModelNotFoundException();
        }

        return $emailVerification;
    }
}
