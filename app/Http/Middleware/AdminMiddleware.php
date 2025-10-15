<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function __invoke(Request $request, Closure $next): Response
    {
        abort_if(! $request->user() || ! $request->user()->isAdmin() || ! $request->user()->is_active, 403, 'Admin access required.');

        /** @var Response $response */
        $response = $next($request);

        return $response;
    }
}
