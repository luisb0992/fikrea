<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LandingController;

/**
 * Rutas de la landing page, accesibles a través de la url:
 *
 * /landing/...
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

/**
 * Todas las rutas que requieren idioma llevan el middleware 'language'
 */
Route::group(['middleware' => 'language'], function () {

    /**
     * Página landing de la aplicación
     * nombre: landing.home
     */
    Route::get('/', [LandingController::class, 'index'])->name('landing.home');
});
