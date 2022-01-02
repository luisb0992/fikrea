<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BackendController;
use App\Http\Controllers\SearchController;

/**
 * Rutas para el backend o zona del usuario administrador del sitio
 *
 * /admin/...
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

/**
 * Todas las rutas que requieren idioma llevan el middleware 'language'
 * Para la manipulación de archivos se necesita subscripción
 */
Route::group(['middleware' => ['language', 'subscription']], function () {
     /**
     * Muestra la página de inicio del backend del administrador
     *
     * nombre: backend.home
     */
     Route::get('/', [BackendController::class, 'home'])
        ->name('backend.home');

     /**
     * Muestra la lista de todos los usuarios incluyendo invitados
     *
     * nombre: backend.users.list
     */
     Route::get('/users/list', [BackendController::class, 'users'])
          ->name('backend.users.list');

     /**
     * Muestra la lista de todos los usuarios registrados
     *
     * nombre: backend.registered.list
     */
     Route::get('/registered/list', [BackendController::class, 'registered'])
        ->name('backend.registered.list');

     /**
     * Muestra la lista de todos los clientes
     *
     * nombre: backend.clients.list
     */
     Route::get('/clients/list', [BackendController::class, 'clients'])
         ->name('backend.clients.list');

     /**
     * Deshabilita la cuenta de usuario
     *
     * nombre: backend.user.disable
     */
     Route::get('/user/disable/{id}', [BackendController::class, 'disableAccount'])
          ->name('backend.user.disable');

     /**
     * Habilita la cuenta de usuario
     *
     * nombre: backend.user.enable
     */
     Route::get('/user/enable/{id}', [BackendController::class, 'enableAccount'])
          ->name('backend.user.enable');

     /**
     * Muestra la lista de todas las subscripciones
     *
     * nombre: backend.subscriptions.list
     */
     Route::get('/subscriptions/list', [BackendController::class, 'subscriptions'])
          ->name('backend.subscriptions.list');

    /**
     * Crea las subscripciones
     *
     * nombre: backend.subscriptions.subscriptionCreate
     */
     Route::get('/subscriptions/create', [BackendController::class, 'subscriptionCreate'])
          ->name('backend.subscriptions.subscriptionCreate');

    /**
     * Guarda las subscripciones creadas
     *
     * nombre: backend.subscriptions.subscriptionStore
     */
     Route::post('/subscriptions/create', [BackendController::class, 'subscriptionStore'])
          ->name('backend.subscriptions.subscriptionStore');

     /**
     * Edita los datos de una subscripción
     *
     * nombre: backend.subscription.edit
     */
     Route::get('/subscription/edit/{id}', [BackendController::class, 'editSubscription'])
          ->name('backend.subscription.edit');

     /**
     * Guarda los datos de una subscripción
     *
     * nombre: backend.subscription.save
     */
     Route::post('/subscription/save/{id}', [BackendController::class, 'saveSubscription'])
          ->name('backend.subscription.save');

     /**
     * Muestra la lista de todas las facturas
     *
     * nombre: backend.orders.list
     */
     Route::get('/orders/list', [BackendController::class, 'orders'])
         ->name('backend.orders.list');

    /**
     * Muestra la lista de todas los planes
     *
     * nombre: backend.plans.plans
     */
     Route::get('/plans/list', [BackendController::class, 'plans'])
         ->name('backend.plans.plans');

    /**
     * Crea los planes
     *
     * nombre: backend.plans.createPlans
     */
     Route::get('/plans/create', [BackendController::class, 'createPlans'])
         ->name('backend.plans.createPlans');

    /**
     * Guarda los planes creados
     *
     * nombre: backend.plans.storePlans
     */
     Route::post('/plans', [BackendController::class, 'storePlans'])
         ->name('backend.plans.storePlans');

    /**
     * Edita los planes
     *
     * nombre: backend.plans.editPlans
     */
     Route::get('/plans/edit/{id}', [BackendController::class, 'editPlans'])
         ->name('backend.plans.editPlans');

    /**
     * Actualiza los planes
     *
     * nombre: backend.plans.updatePlans
     */
     Route::post('/plans/edit/{id}', [BackendController::class, 'updatePlans'])
         ->name('backend.plans.updatePlans');

    /**
     * Eliminar los planes
     *
     * nombre: backend.plans.deletePlans
     */
     Route::get('/plans/delete/{id}', [BackendController::class, 'deletePlans'])
         ->name('backend.plans.deletePlans');

    /***********************************************************************************
     *  Listado de smses en la app
    /***********************************************************************************/

    /**
     * Muestra la lista de todos los sms
     *
     * nombre: backend.sms.list
     */
     Route::get('/sms/list', [BackendController::class, 'smses'])
         ->name('backend.sms.list');

    /***********************************************************************************
     *  Búsqueda de usuarios
    /***********************************************************************************/

    /**
     * Busca los usuarios en base al texto de consulta suministrado
     * nombre: dashboard.search.find.user
     */
    Route::get('/search/user/{query}', [SearchController::class, 'findUser'])
         ->name('backend.search.find.user');
});
