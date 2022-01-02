<?php

/**
 * Evento DocumentRequestFileExpiring
 *
 * Evento cuando un documento aportado por un formante está a punto de expirar
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Events;

use Illuminate\Queue\SerializesModels;

use App\Models\DocumentRequest;
use App\Models\Signer;

class DocumentRequestFileExpiring
{
    use SerializesModels;

    /**
     * La solicitud de documentos
     *
     * @var DocumentRequest
     */
    public DocumentRequest $request;

    /**
     * El usuario que responde a la solicitud de documentos
     *
     * @var Signer
     */
    public Signer $signer;

    /**
     * El constructor
     *
     * @param DocumentRequest $request          La solicitud de documentos
     * @param Signer          $signer           El usuario "firmante" que responde a la solicitud de documentos
     */
    public function __construct(DocumentRequest $request, Signer $signer)
    {
        $this->request = $request;
        $this->signer  = $signer;
    }
}
