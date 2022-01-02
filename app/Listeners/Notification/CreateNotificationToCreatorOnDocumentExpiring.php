<?php

/**
 * Crea una notificación cuando se encuentra un documento al expirar
 * 
 * Este chequeo se realiza mediante el comando check:expiring-aported-files
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Listeners\Notification;

use Illuminate\Support\Facades\Lang;

use App\Events\DocumentRequestFileExpiring;
use App\Models\Notification;

class CreateNotificationToCreatorOnDocumentExpiring
{
    /**
     * Crea una notificación para el creador/autor de la solicitud de documentos
     * a la que pertenece el documento que expira pronto
     *
     * @param DocumentRequestFileExpiring $event        El evento de notificación 
     *
     * @return void
     */
    public function handle(DocumentRequestFileExpiring $event)
    {
        // Obtiene la solicitud de documentos
        $request = $event->request;

        // Obtiene el usuario que ha renovado el documento
        $signer  = $event->signer;
 
        // Crea el título y mensaje de la notificación
        $title = Lang::get(
            'Documento aportado por :name :lastname <a href="mailto::email">:email</a>
            próximo a vencer',
            [
                'request'    => $request->name,
                'name'       => $signer->name,
                'lastname'   => $signer->lastname,
                'email'      => $signer->email,
            ]
        );

        $href = route('dashboard.document.request.status', ['id' => $request->id]);

        $message = Lang::get(
            'En la solicitud de documentos <a href=":href">:request</a> existen documentos aportados por usuario :name :lastname
            <a href="mailto::email">:email</a> próximos a expirar según su fecha de expiración',
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
                'type'      => \App\Enums\NotificationTypeEnum::ATTENTION,
            ]
        );
    }
}
