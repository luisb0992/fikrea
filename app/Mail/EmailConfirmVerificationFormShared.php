<?php

/**
 * Correo de confirmación que se ha compartido una verificación de datos con otros usuarios
 *
 * @author LuisBarDev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

use App\Models\User;
use App\Models\VerificationForm;

class EmailConfirmVerificationFormShared extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * El usuario creador/autor de la verificación
     *
     * @var User
     */
    public User $creator;

    /**
     * La verificación compartida
     *
     * @var VerificationForm
     */
    public VerificationForm $verification;

    /**
     * El constructor
     *
     * @param User     $user                    El usuario creador/autor
     * @param VerificationForm $verification         La verificación de datos
     */
    public function __construct(User $creator, VerificationForm $verification)
    {
        $this->creator      = $creator;
        $this->verification = $verification;
    }

    /**
     * Construye el mensaje
     *
     * @return self                             El propio objeto
     */
    public function build(): self
    {
        $subject = Lang::get('Ha enviado una nueva certificación de datos con :app', [
            'app'   => config('app.name')
        ]);

        $this->subject($subject);

        return $this->view('dashboard.mail.confirm-verificationform-shared', [
            'creator'           => $this->creator,
            'verificationForm'  => $this->verification,
        ]);
    }
}
