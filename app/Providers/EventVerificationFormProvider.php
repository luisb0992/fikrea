<?php

/**
 * ServiceProvider para los procesos de certificación y verificación de datos
 * relacionados a un proceso de documento o fuera del mismo
 *
 * @author LuisBarDev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Providers;

/**
 * Eventos
 */
use App\Events\VerificationFormCancelEvent;
use App\Events\VerificationFormDoneEvent;

/**
 * Listeners
 */
use App\Listeners\Notification\CreateCancelVerificationFormNotification;
use App\Listeners\Notification\CreateDoneVerificationFormNotification;

/**
 * Clases necesarias
 */
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
// use Illuminate\Support\ServiceProvider;

class EventVerificationFormProvider extends EventServiceProvider
{
    /**
     * Listeners para el proceso d verificación de datos
     *
     * @var array
     */
    protected $listen = [

        /**
         * la verificación de datos ha sido rechazada por el usuario
         *
         * Event => Listeners
         */
        VerificationFormCancelEvent::class => [
            CreateCancelVerificationFormNotification::class
        ],

        /**
         * La verificación de datos ha sido procesada por el usuario
         *
         * Event => Listeners
         */
        VerificationFormDoneEvent::class => [
            CreateDoneVerificationFormNotification::class
        ],
    ];

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
