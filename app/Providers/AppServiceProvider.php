<?php

/**
 * Configura los servicios de la aplicación
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\App;
use Illuminate\Pagination\Paginator;

use App\Models\Guest;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registra los servicios de la aplicación
     *
     * @return void
     */
    public function register()
    {
        // Comparte el usuario en todas las vistas de blade
        // El usuario registrado o el usuario invitado, según sea el caso
        View::composer('*', fn ($view) => $view->with('user', Auth::user() ?? Guest::user()));

        // Usa los estilos CSS de Bootstrap para el paginador
        Paginator::useBootstrap();

        // Inyecta las dependencias en el contenedor de inversión de control
        $this->injectDependencies();
    }

    /**
     * Inyecta las dependencias en el contendor de inversión de control IoC
     *
     * @return void
     */
    public function injectDependencies():void
    {
        /**
         * Inyecta el servicio de envío de SMS de Altiria a la intrefaz SMS
         */
        App::singleton('Sms', function () {
            return new \Fikrea\Altiria;
        });
    }
}
