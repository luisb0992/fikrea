<?php

/**
 * Correo que envía un enlace para que un usuario recuperar una sesión de invitado anterior
 * y, de este modo, pueda acceder a toda la información que manejo en aquellaq sesión
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

use App\Models\User;

class EmailSessionTokenLink extends Mailable
{
    use Queueable, SerializesModels;
    
    /**
     * El usuario
     *
     * @var User
     */
    public User $user;

    /**
     * El constructor
     *
     * @param User    $user                     El usuario creador del documento
     */
    public function __construct(User $user)
    {
        $this->user             = $user;
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
                ":app le envía su solicitud se acceso a una sesión de usuario invitado anterior",
                [
                    'app'     => config('app.name'),
                ]
            )
        );

        return $this->view(
            'dashboard.mail.send-session-token',
            [
                'oldUser'      => $this->user,
            ]
        );
    }
}
