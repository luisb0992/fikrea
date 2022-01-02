<?php

/**
 * Envía un mensaje de correo al autor cuando un usuario ha respondido una solicitud de documentos
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Listeners\Notification;

use App\Events\DocumentRequestFileRenewed;
use App\Http\Controllers\EmailController;

class SendEmailOnDocumentRequestRenewed
{
    /**
     * Envía un correo al usuario autor/creador de la solicitud de documentos
     * informándole que el firmante ha renovado un documento aportado
     *
     * @param DocumentRequestFileRenewed $event        El evento de renovación del documento
     *
     * @return void
     */
    public function handle(DocumentRequestFileRenewed $event)
    {
        EmailController::renewRequestDocumentDone($event->request, $event->signer);
    }
}
