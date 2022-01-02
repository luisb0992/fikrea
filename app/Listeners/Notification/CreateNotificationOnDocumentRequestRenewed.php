<?php

/**
 * Crea una notificación cuando un usuario responde a una renovación
 * de un documento aportado
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Listeners\Notification;

use App\Events\DocumentRequestFileRenewed;
use Illuminate\Support\Facades\Lang;
use App\Models\Notification;

class CreateNotificationOnDocumentRequestRenewed
{
    /**
     * Crea una notificación para el creador/autor de la solicitud de documentos
     *
     * @param DocumentRequestDone $event        El evento de respuesta a la renovación de un documento aportado
     *
     * @return void
     */
    public function handle(DocumentRequestFileRenewed $event)
    {
        info("creando notificación al renovar documento");
        // Obtiene la solicitud de documentos
        $request = $event->request;

        // Obtiene el usuario que ha renovado el documento
        $signer  = $event->signer;
 
        // Crea el título y mensaje de la notificación
        $title = Lang::get(
            'Se ha renovado un documento en solicitud :request por :name :lastname 
             <a href="mailto::email">:email</a>',
            [
                'request'    => $request->name,
                'name'       => $signer->name,
                'lastname'   => $signer->lastname,
                'email'      => $signer->email,
            ]
        );

        $href = route('dashboard.document.request.status', ['id' => $request->id]);

        $message = Lang::get(
            'El usuario :name :lastname <a href="mailto::email">:email</a> 
             ha renovado documentos en la solicitud <a href=":href">:request</a>',
            [
                'name'       => $signer->name,
                'lastname'   => $signer->lastname,
                'email'      => $signer->email,
                'request'    => $request->name,
                'href'       => $href,
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
