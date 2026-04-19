<?php

namespace App\Http\Middleware;

use App\Exceptions\ForbiddenException;
use App\Exceptions\UnauthorizedException;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActiveUserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if(!$user){
            throw new UnauthorizedException();
        }

        if($user->isActive()){
            throw new ForbiddenException('your account is inactive');
        }

        return $next($request);
    }
}
