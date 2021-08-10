<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class JsonifyResponse
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('accept', 'application/json', true);

        return $next($request);
    }
}
