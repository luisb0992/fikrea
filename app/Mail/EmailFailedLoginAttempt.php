<?php

/**
 * Correo que se envía al usuario cuando ha metido mal las credenciales de
 * acceso a la app
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

use App\Models\User;
use Illuminate\Http\Request;

use Fikrea\GeoIp;

class EmailFailedLoginAttempt extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * El usuario 
     *
     * @var User
     */
    public User $user;

    /**
     * Los datos de la solicitud
     *
     * @var Request
     */
    public Request $request;

    /**
     * Los datos de la geolocalización aproximada según ip
     *
     * @var geoip
     */
    public GeoIp $geoip;

    /**
     * El constructor
     *
     * @param User    $user    El usuario
     * @param Request $request La solicitud
     */
    public function __construct(User $user, Request $request, GeoIp $geoip)
    {
        $this->user     = $user;
        $this->request  = $request;
        $this->geoip    = $geoip;
    }

    
    /**
     * Construye el mensaje
     *
     * @return self                             El propio objeto
     */
    public function build():self
    {
        $this->subject(
            Lang::get(
                ':app le informa de un intento fallido para acceder a su cuenta en nuestra aplicación',
                [
                    'app'               => config('app.name')
                ]
            )
        );

        return $this->view('dashboard.mail.failed-login-attempt');
    }
}
