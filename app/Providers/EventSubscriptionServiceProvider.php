<?php

/**
 * ServiceProvider para los procesos de Subscripción
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventSubscriptionServiceProvider extends ServiceProvider
{
    /**
     * Mapeo a cada listener
     *
     * @var array
     */
    protected $listen =
        [
            /**
             * Crea la subscripción de prueba gratuita cuando una nueva cuenta de usuario ha sido creada
             * Crea unas notificaciones iniciales con ayudas sobre la aplicación
             */
            \App\Events\UserCreated::class => [
                \App\Listeners\Subscription\CreateTrialSubscription::class,
                \App\Listeners\Notification\CreateInitialNotifications::class
            ],
            /**
             * Envía un correo de confirmación al usuario cuando la subscripción ha sido renovada
             */
            \App\Events\SubscriptionRenewed::class => [
                \App\Listeners\Subscription\SendConfirmationEmailOnSubscriptioRenewed::class,
            ],
        ];

    /**
     * Punto de inicio del Service Provider
     *
     * @return void
     */
    public function boot():void
    {
        parent::boot();
    }
}
