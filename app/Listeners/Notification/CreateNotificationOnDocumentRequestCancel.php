<?php

/**
 * Crea una notificación cuando un usuario rechaza una solicitud de documentos
 *
 * @author Jonathan Sanchez <jonathanch1991@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Listeners\Notification;

use Illuminate\Support\Facades\Lang;

use App\Events\DocumentRequestCancel;
use App\Models\Notification;

class CreateNotificationOnDocumentRequestCancel
{
    /**
     * Crea una notificación para el creador/autor de la solicitud de documentos
     *
     * @param DocumentRequestDone $event        El evento de respuesta a la solicitud de documentos
     *
     * @return void
     */
    public function handle(DocumentRequestCancel $event):void
    {
        // Obtiene la solicitud de documentos
        $request = $event->request;

        // Obtiene el usuario que a rechazado la solicitud de documentos
        $signer  = $event->signer;
 
        // Crea el título y mensaje de la notificación
        $title = Lang::get(
            'La solicitud de documentos :request ha sido rechazada por :name :lastname 
             <a href="mailto::email">:email</a>',
            [
                'request'   => $request->name,
                'name'       => $signer->name,
                'lastname'   => $signer->lastname,
                'email'      => $signer->email,
            ]
        );

        $href = $signer->validations() ?
                    route('dashboard.document.status', [$signer->document->id])
                    :
                    route('dashboard.document.request.status', [$request->id]);

        $message = Lang::get(
            'El usuario :name :lastname <a href="mailto::email">:email</a> 
             ha rechazado su solicitud de documentos  <a href=":href">:request</a>.',
            [
                'name'       => $signer->name,
                'lastname'   => $signer->lastname,
                'email'      => $signer->email,
                'request'    => $request->name,
                'href'       => $href
            ]
        );

        Notification::create(
            [
                'user_id'   => $request->user->id,
                'title'     => $title,
                'message'   => $message,
                'url'       => $href,
                'type'      => \App\Enums\NotificationTypeEnum::CANCELLED,
                'reason_cancel_request_id' => $signer->reason_cancel_request_id
            ]
        );
    }
}
