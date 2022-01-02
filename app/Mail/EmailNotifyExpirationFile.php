<?php

/**
 * Correo que se envía al firmante de un documento confirmando que uno de sus documentos
 * aportados está a menos de 7 días de vencer.
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;
use Carbon\Carbon;

use App\Models\Signer;
use App\Models\DocumentRequest;

class EmailNotifyExpirationFile extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * El firmante
     *
     * @var User
     */
    public Signer $signer;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Signer $signer)
    {
        $this->signer = $signer;
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
                ':app le informa que un documento que usted ha aportado vence próximamente',
                [
                    'app'               => config('app.name')
                ]
            )
        );

        return $this->view(
            'dashboard.mail.notify-expiration-of-document-request-file', [
                'documentRequest'   => $this->signer->request(),
            ]
        );
    }
}
