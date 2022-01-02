<?php

/**
 * Correo que se envía al autor de un documento confirmando qu un usuario ha completado una solicitud de documentos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

use App\Models\DocumentRequest;
use App\Models\Signer;

class EmailDocumentRequestSuccess extends Mailable
{
    use Queueable, SerializesModels;
    
    /**
     * La solicitud de documentos
     *
     * @var DocumentRequest
     */
    public DocumentRequest $request;

    /**
     * El usuario "firmante" que ha completado la solicitud de documentos
     *
     * @var Signer
     */
    public Signer $signer;

    /**
     * El constructor
     *
     * @param DocumentRequest $request          La solicitud de documentos
     * @param Signer          $signer           El usuario que ha completado la solicitud
     */
    public function __construct(DocumentRequest $request, Signer $signer)
    {
        $this->request = $request;
        $this->signer  = $signer;
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
                ':app le informa que :name :lastname <:email>
                 ha completado una solicitud de documentos',
                [
                    'app'       => config('app.name'),
                    'name'      => $this->signer->name,
                    'lastname'  => $this->signer->lastname,
                    'email'     => $this->signer->email,
                ]
            )
        );

        return $this->view(
            'dashboard.mail.confirm-document-request-done',
            [
                'request'      => $this->request,
                'signer'       => $this->signer,
            ]
        );
    }
}
