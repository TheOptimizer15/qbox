<?php

use App\Exceptions\ApiException;
use App\Http\Middleware\AcceptJsonMiddleware;
use App\Http\Middleware\AuthorizationMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'authorize' => AuthorizationMiddleware::class,
        ]);
        $middleware->prepend([AcceptJsonMiddleware::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ApiException $exception, Request $request) {
            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $exception->getMessage(),
                    'message_code' => $exception->getMessageCode(),
                ], $exception->getStatusCode());
            }
        });

        $exceptions->render(function(ValidationException $exception, Request $request){
            $payload = [
                'success' =>  false,
                'message' => 'the data submitted does not match the required form',
                'message_code' => 'VALIDATION_ERROR',
                'errors' =>$exception->errors()
            ];
            if($request->is('api/*') || $request->wantsJson()){
                return response()->json($payload, 422);
            }
        });

        $exceptions->render(function(AuthenticationException $exception, Request $request){
               $payload = [
                'success' =>  false,
                'message' => 'unauthenticated',
                'message_code' => 'UNAUTHENTICATED_ACCESS',
            ];
            if($request->is('api/*') || $request->wantsJson()){
                return response()->json($payload, 401);
            }
        });

        $exceptions->render(function (Throwable $exception, Request $request) {
            $payload = [
                'success' => false,
                'message' => app()->isProduction() ? 'an unknow error occured' : $exception->getMessage(),
                'message_code' => 'INTERNAL_SERVER_ERROR',
            ];

            if(!app()->isProduction()){
                $payload['trace'] = $exception->getTrace();
            }

            if ($request->is('api/*') || $request->wantsJson()) {
                return response()->json($payload, 500);
            }
        });

    })->create();
