<?php

/**
 * Define el comando:
 *
 * Php artisan check:expiring-aported-files
 *
 * Chequea por los documentos que han aportado los firmantes
 * y si los mismos están a menos de 7 días de expirar basados
 * en la fecha de vencimiento que introdujeron al aportarlos
 * a petición del usuario, siempre notificamos al firmante
 * y al usuario si este ha marcado dicha opción al requerir
 * el documento.
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos SL
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\EmailController;
use App\Http\Controllers\SmsController;

/**
 * Modelos requeridos
 */
use App\Models\DocumentRequestFile;
use App\Models\DocumentRequest;

/**
 * Evento requerido
 */
use App\Events\DocumentRequestFileExpiring;

class CheckForExpiringAportedDocuments extends Command
{
    /**
     * Signatura y nombre del comando
     *
     * @var string
     */
    protected $signature = 'check:expiring-aported-files';

    /**
     * La descripción del comando
     *
     * @var string
     */
    protected $description = 'Chequea por los documentos que están a pocos días de expirar';

    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ejecuta el comando de consola
     *
     * @return void
     */
    public function handle() : void
    {
        $this->checkForDocuments();
    }

    /**
     * Chequea todos los documentos que se han aportado
     *
     * @return void
     */
    protected function checkForDocuments() : void
    {
         /**
         * Para realizar la búsqueda de los documentos que deben renovarse debemos:
         * - Para cada solicitud de documentos que se haya realizado
         * - Chequear si tiene documentos al expirar mediante expiringDocuments()
         */
        foreach (DocumentRequest::all() as $key => $documentRequest) {
            $expiring = $documentRequest->expiringDocuments();
            if ($expiring->count()) {
                // Para cada App\Models\RequiredDocument
                foreach ($expiring as $index => $requiredDocument) {
                    // Necesito el firmante del documento
                    $signer = $requiredDocument->file()->signer;
                    if ($signer->email) {
                        // Si se ha proporcionado el correo del firmante se notifica por email
                        EmailController::sendDocumentRequiredFileRevocationEmail(
                            $signer
                        );
                    } elseif ($signer->phone) {
                        // Si no se ha proporcionado un correo, pero si su teléfono, se notifica por SMS
                        SMSController::sendDocumentRequiredFileRevocationSms($signer);
                    }

                    // Si el creador ha seleccionado que quiere ser notificado se le notifica
                    if ($requiredDocument->notify) {
                        event(new DocumentRequestFileExpiring($documentRequest, $signer));
                    }
                }
            }
        }
    }
}
