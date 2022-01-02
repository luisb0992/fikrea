<?php

/**
 * Correo de envío de la dirección de descarga de un conjunto de archivos compartidos
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
 * Modelos Requeridos
 */

use App\Models\User;
use App\Models\DocumentSharing;
use App\Models\DocumentSharingContact;

class EmailSendDocumentSharing extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * El usuario
     *
     * @var User
     */
    public User $user;

    /**
     * La compartición de documentos
     *
     * @var string
     */
    public DocumentSharing $documentSharing;

    /**
     * La dirección de correo del destinatario
     *
     * @var string
     */
    public DocumentSharingContact $contact;

    /**
     * El constructor
     *
     * @param User $user                                El usuario
     * @param DocumentSharing $fileharing               La compartición de documentos
     * @param DocumentSharingContact $contact           El contacto con el que se comparte
     */
    public function __construct(User $user, DocumentSharing $documentSharing, DocumentSharingContact $contact)
    {
        $this->user             = $user;
        $this->documentSharing  = $documentSharing;
        $this->contact          = $contact;
    }

    /**
     * Construye el mensaje
     *
     * @return self                             El propio objeto
     */
    public function build(): self
    {
        $this->subject(
            Lang::get(':app. El usuario :name :lastname :email ha compartido unos documentos con usted', [
                'app'           => config('app.name'),
                'name'          => $this->user->name,
                'lastname'      => $this->user->lastname,
                'email'         => $this->user->email,
            ])
        );

        return $this->view('dashboard.mail.document-sharing', [
            'documentSharing'   => $this->documentSharing,
            'contact'           => $this->contact,
        ]);
    }
}
