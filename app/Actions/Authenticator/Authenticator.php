<?php

namespace App\Actions\Authenticator;

use App\Exceptions\Repository\UnauthenticatedException;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Factory;

class Authenticator
{
    /**
     * @param LoginCredentialsDTO $loginCredentials
     * @return JwtTokenCredentials
     * @throws UnauthenticatedException
     */
    public function getToken(LoginCredentialsDTO $loginCredentials): JwtTokenCredentials
    {
        $token = Auth::attempt([
            'email' => $loginCredentials->email,
            'password' => $loginCredentials->password,
        ]);

        if (!$token) {
            throw new UnauthenticatedException();
        }

        /** @var Factory $jwtFactory */
        $jwtFactory = Auth::factory();

        return new JwtTokenCredentials($token, $jwtFactory->getTTL());
    }

    public function logout(): void
    {
        Auth::logout();
    }
}
