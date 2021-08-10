<?php

namespace App\Http\Middleware;

use App\Exceptions\Http\UnauthorizedException;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JwtMiddleware extends BaseMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse
     * @throws UnauthorizedException
     */
    public function handle(Request $request, Closure $next): JsonResponse
    {
        try {
            $jwtSubject = $this->auth->parseToken()->authenticate();

            if (!$jwtSubject) {
                throw new UnauthorizedException();
            }
        } catch (\Throwable $e) {
            throw new UnauthorizedException();
        }

        return $next($request);
    }
}
