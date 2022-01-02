<?php

/**
 * Correo para recuperar la contraseña de usuario
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

/**
 * Modelos utilizados
 */
use App\Models\User;

class EmailResetPasswordRequest extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * El usuario
     *
     *@var User
     */
    public User $user;

    /**
     * El constructor
     *
     * @param User $user                        El usuario
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
                ':app le envía una solicitud de recuperación de contraseña',
                [
                    'app' => config('app.name'),
                ]
            )
        );

        return $this->view('landing.mail.reset-password-request', [
            'forgetfulUser'  => $this->user,
            'token'          => $this->user->validation_code,
        ]);
    }
}
