<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Ensure Sanctum's stateful middleware is first for all API routes
        // This enables session support for stateful requests from frontend
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        // Create a middleware group for API routes that need explicit session (auth routes)
        // This group includes session middleware for login/register/logout routes
        // EnsureFrontendRequestsAreStateful is already applied to all API routes above
        // We add session middleware here so routes can use $request->session()
        $middleware->group('api.session', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Session\Middleware\StartSession::class,
        ]);

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'data' => null,
                    'errors' => ['message' => 'Unauthenticated.'],
                    'meta' => null,
                ], 401);
            }
        });

        $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'data' => null,
                    'errors' => $e->errors(),
                    'meta' => null,
                ], 422);
            }
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'data' => null,
                    'errors' => ['message' => 'Resource not found.'],
                    'meta' => null,
                ], 404);
            }
        });

        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'data' => null,
                    'errors' => ['message' => 'This action is unauthorized.'],
                    'meta' => null,
                ], 403);
            }
        });
    })->create();
