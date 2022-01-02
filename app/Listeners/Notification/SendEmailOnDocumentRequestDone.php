<?php

/**
 * Envía un mensaje de correo al autor cuando un usuario ha respondido una solicitud de documentos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Listeners\Notification;

use App\Events\DocumentRequestDone;
use App\Http\Controllers\EmailController;

class SendEmailOnDocumentRequestDone
{
    /**
     * Envía un correo al usuario autor/creador de la solicitud de documentos
     * informándole que la solicitud ha sido realizada
     *
     * @param DocumentRequestDone $event        El evento de contestación de la solicitud de documentos
     *
     * @return void
     */
    public function handle(DocumentRequestDone $event):void
    {
        EmailController::requestDocumentDone($event->request, $event->signer);
    }
}
