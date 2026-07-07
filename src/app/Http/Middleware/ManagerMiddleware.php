<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ManagerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null || ($user->role !== 0 && $user->role !== 1)) {
            abort(403, 'Acceso no autorizado. Se requieren permisos de administrador o gestor.');
        }

        return $next($request);
    }
}
