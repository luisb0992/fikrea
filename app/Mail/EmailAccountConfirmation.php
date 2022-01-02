<?php

/**
 * Correo de confirmación de la cuenta de usuario
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

class EmailAccountConfirmation extends Mailable
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
     * @param User  $user                       El usuario
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
                'Bienvenido a :app',
                [
                    'app'   => config('app.name'),
                ]
            )
        );

        return $this->view(
            'landing.mail.account-confirmation',
            [
                'account' => $this->user,
                'code'    => $this->user->validation_code,
            ]
        );
    }
}
