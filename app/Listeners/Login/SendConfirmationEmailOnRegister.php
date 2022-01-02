<?php

namespace App\Listeners\Login;

use App\Http\Controllers\EmailController;

class SendConfirmationEmailOnRegister
{
    /**
     * Acción que se ejecuta cuando se dispara cuando un usuario ha sido registrado
     *
     * Enviar email con el enlace para la validación del usuario
     *
     * @param Registered $event                  El evento
     *
     * @return void
     */
    public function handle($event)
    {
        EmailController::sendWellcomeEmail($event->user);
    }
}
