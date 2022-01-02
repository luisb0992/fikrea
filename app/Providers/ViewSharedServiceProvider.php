<?php

/**
 * ServiceProvider para la compartición de datos entre vistas
 *
 * Permite inyectar datos comunes a todas las vistas
 * Para ello se utiliza el método View::share
 *
 * @link https://laravel.com/docs/8.x/views#sharing-data-with-all-views
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ViewSharedServiceProvider extends ServiceProvider
{
    /**
     * Registra el Service Provider
     *
     * @return void
     */
    public function register():void
    {
        //
    }

    /**
     * Punto de entrada para la configuración del Service Provider
     *
     * @return void
     */
    public function boot():void
    {
        // Comparte las variables dadas con todas las vistas
        // View::share(...);
    }
}
