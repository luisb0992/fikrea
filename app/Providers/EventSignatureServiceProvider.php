<?php

/**
 * ServiceProvider para los procesos de firma de documentos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use App\Events\DocumentRequireToBeSignedEvent;
use App\Events\SignerValidationDone;
use App\Events\SignerValidationCancel;

class EventSignatureServiceProvider extends ServiceProvider
{
    /**
     * El mapeo a los listeners de la aplicación
     *
     * @var array
     */
    protected $listen = [
        /**
         * Solicitud de firma de un documento por parte de los firmantes
         */
        DocumentRequireToBeSignedEvent::class => [
            \App\Listeners\DocumentRequireToBeSigned::class,
        ],

        /**
         * Una validación ha sido realizada por un firmante
         */
        SignerValidationDone::class => [
            \App\Listeners\Notification\CreateNotificationOnSignerValidationDone::class,
            \App\Listeners\Notification\SendEmailOnSignerValidationDone::class
        ],

        /**
         * Una validación ha sido rechazada por un firmante
         */
        SignerValidationCancel::class => [
            \App\Listeners\Notification\CreateNotificationOnSignerValidationCancel::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot():void
    {
        parent::boot();
    }
}
