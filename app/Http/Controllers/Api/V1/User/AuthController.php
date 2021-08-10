<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Actions\Authenticator\Authenticator;
use App\Actions\Authenticator\LoginCredentialsDTO;
use App\Exceptions\Http\ForbiddenException;
use App\Exceptions\Http\UnauthorizedException;
use App\Exceptions\Repository\UnauthenticatedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private Authenticator $authenticator;

    public function __construct(Authenticator $authenticator)
    {
        $this->authenticator = $authenticator;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $loginCredentialsDTO = new LoginCredentialsDTO($request->email, $request->password);

        try {
            $tokenCredentials = $this->authenticator->getToken($loginCredentialsDTO);

            /** @var User $user */
            $user = Auth::user();
            if (!$user->hasVerifiedEmail()) {
                throw new ForbiddenException();
            }
        } catch (UnauthenticatedException $e) {
            throw new UnauthorizedException();
        }

        return new JsonResponse([
            'accessToken' => $tokenCredentials->token,
            'tokenType' => $tokenCredentials->type,
            'expiresIn' => $tokenCredentials->timeToLive,
        ]);
    }

    public function logout(): JsonResponse
    {
        $this->authenticator->logout();

        return new JsonResponse(['message' => 'Successfully Logged Out']);
    }
}
