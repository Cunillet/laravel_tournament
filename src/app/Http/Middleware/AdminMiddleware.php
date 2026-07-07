<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() === null || $request->user()->role !== 0) {
            abort(403, 'Acceso no autorizado. Solo administradores.');
        }

        return $next($request);
    }
}
