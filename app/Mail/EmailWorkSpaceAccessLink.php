<?php

/**
 * Correo que envía un enlace para que un usuario pueda acceder a su
 * espacio de usuario (workspace)
 * y pueda firmar y validar un documento
 *
 * @author    javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

use App\Models\User;
use App\Models\Signer;
use App\Models\DocumentSharing;

class EmailWorkSpaceAccessLink extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * El usuario creador del documento
     *
     * @var User
     */
    public User $user;

    /**
     * El usuario firmante del documento
     *
     * @var Signer $signer
     */
    public Signer $signer;

    /**
     * La compartición
     *
     * @var DocumentSharing $sharing
     */
    public ? DocumentSharing $sharing;

    /**
     * El constructor
     *
     * @param User            $user    El usuario creador del documento
     * @param Signer          $signer  El usuario firmante que debe validar el documento
     * @param DocumentSharing $sharing La compartición
     */
    public function __construct(User $user, Signer $signer, DocumentSharing $sharing = null)
    {
        $this->user             = $user;
        $this->signer           = $signer;
        $this->sharing          = $sharing;
    }

    /**
     * Construye el mensaje
     *
     * @return self                             El propio objeto
     */
    public function build(): self
    {
        // Establece el asunto del mensaje según sea un proceso de validación
        // o un proceso de solicitud de documentación
        // o un proceso de verificación de datos
        if ($this->signer->validations()) {
            $subject = Lang::get(
                ':app le informa que :name :lastname <:email> 
                 ha compartido un documento con usted',
                [
                    'app'       => config('app.name'),
                    'name'      => $this->user->name,
                    'lastname'  => $this->user->lastname,
                    'email'     => $this->user->email,
                ]
            );
        } elseif ($this->signer->request()) {
            $subject = Lang::get(
                ':app le informa que :name :lastname <:email> 
                 le ha solicitado documentación',
                [
                    'app'       => config('app.name'),
                    'name'      => $this->user->name,
                    'lastname'  => $this->user->lastname,
                    'email'     => $this->user->email,
                ]
            );
        } elseif ($this->signer->verificationForm()) {
            $subject = Lang::get(':app le informa que :name :lastname <:email> le ha solicitado que certifique ciertos datos', [
                'app'       => config('app.name'),
                'name'      => $this->user->name,
                'lastname'  => $this->user->lastname,
                'email'     => $this->user->email,
            ]);
        }

        $this->subject($subject);

        return $this->view(
            'dashboard.mail.send-workspace-access',
            [
                'creator'   => $this->user,
                'signer'    => $this->signer,
            ]
        );
    }
}
