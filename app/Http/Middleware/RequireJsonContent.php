<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireJsonContent
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isJson()) {
            return new JsonResponse(
                ['message' => 'Requests must use application/json content.'],
                Response::HTTP_UNSUPPORTED_MEDIA_TYPE,
            );
        }

        return $next($request);
    }
}
