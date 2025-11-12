<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isAdmin()) {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'data' => null,
                    'errors' => ['message' => 'Only administrators can access this resource.'],
                    'meta' => null,
                ], 403);
            }

            abort(403, 'Only administrators can access this resource.');
        }

        return $next($request);
    }
}

