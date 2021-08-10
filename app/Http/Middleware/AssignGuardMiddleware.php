<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignGuardMiddleware
{
    public function handle(Request $request, Closure $next, string $guard = null): JsonResponse
    {
        if (!is_null($guard)) {
            Auth::shouldUse($guard);
        }

        return $next($request);
    }
}
