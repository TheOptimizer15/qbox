<?php

namespace App\Http\Middleware;

use App\Exceptions\ForbiddenException;
use App\Exceptions\UnauthorizedException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$role): Response
    {
        $user = $request->user();

        if (! $user) {
            throw new UnauthorizedException('authentication required');
        }

        if (! in_array($user->role->value, $role)) {
            throw new ForbiddenException('you are not allowed to access this resource');
        }

        return $next($request);
    }
}
