<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\SubscriptionController;

/**
 * Rutas para el control de la subscripción
 *
 * /subscription/...
 *
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

/**
 * Todas las rutas que requieren idioma llevan el middleware 'language'
 */
Route::group(['middleware' => ['language']], function () {
    /**
     * Muestra la vista que informa que la subcripción debe ser renovada para seguir utilizando la aplicación
     *
     * nombre: subscription.must.be.renew
     */
     Route::get('mustberenew', [SubscriptionController::class, 'subscriptionMustBeRenew'])
           ->name('subscription.must.be.renew');

    /**
     * Muestra la vista para renovar la subscripción
     * El usuario debe selecionar el plan y el tiempo
     *
     * nombre: subscription.select
     */
     Route::get('renew', [SubscriptionController::class, 'select'])
         ->name('subscription.select')
         ->middleware('auth');

     /**
      * Obtiene la factura de la subscripción
      *
      * nombre: subscription.bill
      */
    Route::get('bill/{order}', [SubscriptionController::class, 'bill'])
        ->name('subscription.bill')
        ->middleware('auth');

    /**
     * Muestra la vista para pagar la subscripción
     *
     * nombre: subscription.payment
     */
     Route::post('payment', [SubscriptionController::class, 'pay'])
         ->name('subscription.payment')
         ->middleware('auth');

    /**
     * Webhook de PayPal que se invoca cuando un pago ha sido aprobado
     * por la plataforma
     *
     * nombre: subscription.payment.approved
     * middleware: 'payment', destinado a la comunicación con las plataformas de pago
     */
    Route::post('/payment/approved', [SubscriptionController::class, 'paymentApproved'])
        ->name('subscription.payment.approved')
        ->middleware('payment');

     /**
      * El pago se ha realizado con éxito
      *
      * nombre: subscription.payment.success
      */
     Route::get('payment/success', [SubscriptionController::class, 'paymentSuccess'])
          ->name('subscription.payment.success')
          ->middleware('auth');

     /**
      * El pago se ha cancelado
      *
      * nombre: subscription.payment.cancel
      */
     Route::get('payment/cancel', [SubscriptionController::class, 'paymentCancel'])
            ->name('subscription.payment.cancel')
            ->middleware('auth');
});
