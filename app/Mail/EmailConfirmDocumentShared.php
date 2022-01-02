<?php

/**
 * Correo de confirmación que se ha compartido un documento para su firma y validación
 * con otros usuarios
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
use App\Models\Document;

class EmailConfirmDocumentShared extends Mailable
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
     * @var Document
     */
    public Document $document;

    /**
     * El constructor
     *
     * @param User     $user                    El usuario creador/autor del documento
     * @param Document $document                El documento compartido
     */
    public function __construct(User $creator, Document $document)
    {
        $this->creator  = $creator;
        $this->document = $document;
    }

    /**
     * Construye el mensaje
     *
     * @return self                             El propio objeto
     */
    public function build(): self
    {
        $this->subject(
            Lang::get('Ha compartido un nuevo documento con :app', [
                'app'   => config('app.name'),
            ])
        );

        return $this->view('dashboard.mail.confirm-document-shared', [
            'creator'   => $this->creator,
            'document'  => $this->document,
        ]);
    }
}
