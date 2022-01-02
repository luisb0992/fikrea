<?php

/**
 * ServiceProvider para los procesos de firma de documentos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use App\Events\DocumentRequestDone;
use App\Events\DocumentRequestCancel;
use App\Events\DocumentRequestFileExpiring;
use App\Events\DocumentRequestFileRenewed;
use App\Events\DocumentRequestUrlGeneratedEvent;

class EventDocumentRequestServiceProvider extends ServiceProvider
{
    /**
     * El mapeo a los listeners de la aplicación
     *
     * @var array
     */
    protected $listen = [

        /**
         * Una solicitud de documentos ha sido atendida por un usuario
         */
        DocumentRequestDone::class => [
            \App\Listeners\Notification\CreateNotificationOnDocumentRequestDone::class,
            \App\Listeners\Notification\SendEmailOnDocumentRequestDone::class
        ],

        /**
         * Una solicitud de documentos ha sido rechazada por un usuario
         */
        DocumentRequestCancel::class => [
            \App\Listeners\Notification\CreateNotificationOnDocumentRequestCancel::class
        ],

        /**
         * Un documento aportado por un firmante está a punto de expirar
         */
        DocumentRequestFileExpiring::class => [
            \App\Listeners\Notification\CreateNotificationToCreatorOnDocumentExpiring::class,
        ],


        /**
         * El firmante ha renovado documentos que ha aportado anteriormente
         * con fecha de vencimiento próximo
         */
        DocumentRequestFileRenewed::class => [
            \App\Listeners\Notification\CreateNotificationOnDocumentRequestRenewed::class,
            \App\Listeners\Notification\SendEmailOnDocumentRequestRenewed::class
        ],

        /**
         * Una solicitud de documentos ha sido enviada mediante generación de URL
         */
        DocumentRequestUrlGeneratedEvent::class => [
            \App\Listeners\Notification\CreateNotificationOnDocumentRequestGenerated::class
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
