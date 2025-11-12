<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSeller
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isSeller()) {
            if ($request->expectsJson()) {
                return new JsonResponse([
                    'data' => null,
                    'errors' => ['message' => 'Only sellers can access this resource.'],
                    'meta' => null,
                ], 403);
            }

            abort(403, 'Only sellers can access this resource.');
        }

        return $next($request);
    }
}

