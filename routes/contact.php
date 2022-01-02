<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CustomerContactController;

/**
 * Rutas para el Contacto de Cliente
 *
 * /dashboard/...
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

/**
 * Todas las rutas que requieren idioma llevan el middleware 'language'
 */
Route::group(['middleware' => 'language'], function () {
    /**
     * Muestra el formulario de contacto
     * nombre: contact.show
     */
    Route::get('/', [CustomerContactController::class, 'show'])->name('contact.show');

    /**
     * Procesa el formulario de contacto
     * nombre: contact.save
     */
    Route::post('/save', [CustomerContactController::class, 'save'])->name('contact.save');
});
