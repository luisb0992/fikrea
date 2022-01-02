<?php

/**
 * Envía un mensaje de correo al autor cuando un firmante ha realizado una validación
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Listeners\Notification;

use App\Events\SignerValidationDone;
use App\Http\Controllers\EmailController;

class SendEmailOnSignerValidationDone
{
    /**
     * Envía un correo al usuario autor/creador del documento
     * informándole que una validación ha sido realizada
     *
     * @param SignerValidationDone $event        El evento de validación realizada por el firmante
     *
     * @return void
     */
    public function handle(SignerValidationDone $event)
    {
        EmailController::validationDone($event->validation);
    }
}
