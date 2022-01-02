<?php

/**
 * Evento DocumentRequestUrlGeneratedEvent
 *
 * Evento cuando se genera url para solicitar documentación
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Events;

use Illuminate\Queue\SerializesModels;

use App\Models\DocumentRequest;
use App\Models\Signer;

class DocumentRequestUrlGeneratedEvent
{
    use SerializesModels;

    /**
     * La solicitud de documentos
     *
     * @var DocumentRequest
     */
    public DocumentRequest $request;

    /**
     * El usuario fake(generado por la app) que responde a la solicitud de documentos
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
