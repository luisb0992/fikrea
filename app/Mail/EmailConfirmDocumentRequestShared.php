<?php

/**
 * Correo de confirmación que se ha compartido una solicitud de documentos con otros usuarios
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
use App\Models\DocumentRequest;

class EmailConfirmDocumentRequestShared extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * El usuario creador/autor del documento
     *
     * @var User
     */
    public User $creator;

    /**
     * El documento compartido
     *
     * @var DocumentRequest
     */
    public DocumentRequest $request;
    
    /**
     * El constructor
     *
     * @param User     $user                    El usuario creador/autor del documento
     * @param DocumentRequest $request          La solicitud de documentos
     */
    public function __construct(User $creator, DocumentRequest $request)
    {
        $this->creator = $creator;
        $this->request = $request;
    }

    /**
     * Construye el mensaje
     *
     * @return self                             El propio objeto
     */
    public function build(): self
    {
        $this->subject(
            Lang::get(
                'Ha enviado una nueva solicitud de documentos con :app',
                [
                    'app'   => config('app.name'),
                ]
            )
        );

        return $this->view(
            'dashboard.mail.confirm-document-request-shared',
            [
                'creator'   => $this->creator,
                'request'   => $this->request,
            ]
        );
    }
}
