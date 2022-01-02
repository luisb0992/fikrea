<?php

/**
 * Middleware que comprueba si el uusuario actual posee el rol de administrador
 * lo que le da acceso al backend
 *
 * @author    javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */


namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;

class AdminOnly extends Middleware
{
    /**
     * Maneja la solicitud de entrada
     *
     * @param \Illuminate\Http\Request $request   La solicitud
     * @param \Closure                 $next      Una clausura que pasa la
     *                                            solicitud al siguiente Middleware
     * @param string[]                 ...$guards La guarda
     *
     * @return Middleware   El proximo middleware
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // Obtiene el usuario
        $user = Auth::user();

        // Si el usuario no posee el rol de Administrador deniega el acceso
        if (!$user || $user->isNotAdmin()) {
            abort(403);
        }

        return $next($request);
    }
}
