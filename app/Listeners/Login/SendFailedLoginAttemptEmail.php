<?php

/**
 * Se ejecuta cuando se lanza el evento de un intento fallido a la app
 * 
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Listeners\Login;

use App\Events\FailedLoginAttemptEvent;

use App\Http\Controllers\EmailController;
use Illuminate\Http\Request;

use Fikrea\GeoIp;

class SendFailedLoginAttemptEmail
{
    /**
     * Envía correo de notificación al usuario del intento de acceso fallido
     *
     * @param FailedLoginAttemptEvent $event        El evento de alerta 
     *
     * @return void
     */
    public function handle(FailedLoginAttemptEvent $event)
    {
        // Usuario del evento
        $user = $event->user;

        // Los datos de la petición del evento
        $request = $event->request;

        $geoip = new GeoIp($request->ip());

        // Enviamos el correo al usuario
        EmailController::sendFailedLoginAttemptEmail($user, $request, $geoip);
    }
}
