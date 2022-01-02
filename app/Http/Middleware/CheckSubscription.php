<?php

/**
 * Middleware que comprueba si la susbcripción está activa
 *
 * Representa una subscripción vinculada a un plan de precios
 *
 * @author    javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */


namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;

use App\Models\Guest;

class CheckSubscription extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request   La solicitud
     * @param \Closure                 $next      Una clausura que pasa la
     *                                            solicitud al siguiente Middleware
     * @param string[]                 ...$guards La guarda
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        // Si el usuario no tiene aún iniciada la sesión
        if (!$user) {
            return redirect()->route('landing.home');
        }

        // Si la subscripción del usuario no está activa (está terminada o cancelada), se redirige a la vista
        // donde se informa que la subscripción debe ser renovada para continuar haciendo uso de la aplicación
        if (!$user->subscription->active()) {
            return redirect()->route('subscription.must.be.renew');
        }

        return $next($request);
    }
}
