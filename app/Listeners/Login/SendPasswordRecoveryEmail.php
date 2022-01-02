<?php

namespace App\Listeners\Login;

use App\Http\Controllers\EmailController;

class SendPasswordRecoveryEmail
{
    /**
     * Acción que se ejecuta cuando se dispara cuando un usuario ha solicitado cambiar su contraseña
     *
     * Enviar email con el enlace para el cambio de la contraseña
     *
     * @param Registered $event                  El evento
     *
     * @return void
     */
    public function handle($event)
    {
        EmailController::sendPasswordRecoveryEmail($event->user);
    }
}
