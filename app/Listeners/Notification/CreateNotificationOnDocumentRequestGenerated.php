<?php

/**
 * Crea una notificación cuando se genera una URL para una solicitud de documentos
 * 
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Listeners\Notification;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Events\DocumentRequestUrlGeneratedEvent;
use Illuminate\Support\Facades\Lang;
use App\Models\Notification;

class CreateNotificationOnDocumentRequestGenerated
{
    /**
     * Crea una notificación para el creador/autor de la solicitud de documentos
     * cuando este crea una solicitud y genera una url para que esta sea atendida
     *
     * @param DocumentRequestUrlGeneratedEvent $event        El evento de notificación 
     *
     * @return void
     */
    public function handle($event)
    {
        // Obtiene la solicitud de documentos
        $request = $event->request;

        // Obtiene el usuario que ha renovado el documento
        $signer  = $event->signer;

        // Crea el título y mensaje de la notificación
        $title = Lang::get('Solicitud de documentos mediante compartición de URL');

        $href = route('dashboard.document.request.status', ['id' => $request->id]);

        $message = Lang::get(
            'Ha creado la solicitud de documentos "<a href=":href">:request</a>" mediante compartición del siguiente enlace.<br/>
                <b>:url</b>
                <br/>
                Puede copiar y pegar este enlace en su navegador para comprobar su funcionamiento',
            [
                'href'      => $href,
                'request'   => $request->name,
                'url'       => route('workspace.home', ['token'=>$signer->token]),
            ]
        );

        Notification::create(
            [
                'user_id'   => $request->user->id,
                'title'     => $title,
                'message'   => $message,
                'url'       => $href,
                'type'      => \App\Enums\NotificationTypeEnum::SUCCESSFULLY,
            ]
        );
    }
}
