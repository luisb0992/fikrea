<?php

/**
 * SmsController
 *
 * Controlador de envío de SMS
 *
 * Los mensajes de SMS no se envían en entorno de desarrolllo
 * pero en su lugar puede consularse el archivo de registro
 * sms.log en la carpeta de almacenamiento de logs donde se registra
 * la información correspondiente
 *
 * @link https://es.wikipedia.org/wiki/Servicio_de_mensajes_cortos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers;

use App\Models\DocumentSharingContact;
use App\Models\FileSharingContact;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

/**
 * Modelos utilizados
 */
use App\Models\Sms;
use App\Models\User;
use App\Models\Guest;
use App\Models\ShortLink;
use App\Models\Signer;
use App\Models\DocumentSharing;
use App\Models\DocumentRequestSharing;

/**
 * Enums
 */
use App\Enums\SignerProcesType;

class SmsController extends Controller
{
    /**
     * Envía un SMS con el acceso al espacio de trabajo
     *
     * @param User                                   $user    El usuario autor/creador del documento
     * @param Signer                                 $signer  El usuario firmante del documento
     * @param DocumentRequestSharing|DocumentSharing $sharing La compartición
     *
     * @return void
     */
    public static function sendWorkSpaceAccessSms(
        User $user,
        Signer $signer,
        $sharing = null
    ): void {
        // El proceso que debe atender el firmante
        $signerProcess = $signer->signerProcess();

        // La url para acceder al home del área de trabajo
        $url = url(route('workspace.home', [
            'token'     => $signer->token,
            'sharing'   => $sharing ? $sharing->id : null,
        ]));

        // Creo el short link de la url
        $short = ShortLink::create([
            'code'  => sha1($url),
            'link'  => $url,
        ]);

        // Con ese código sha1 que se ha generado creo la ruta intermedia
        // más corta para acceder a la ruta original
        $url = url(route('redirect.sms.url', [
            'short'     => $short->code,
        ]));

        // El contenido del mensaje SMS según el tipo de proceso
        switch ($signerProcess) {
            case SignerProcesType::REQUEST_PROCESS:
                $subject = Lang::get(':app. :usuario le ha enviado una solicitud de documentos.
                    Para responder a la misma acceda a la siguiente dirección :url', [
                        'app'       => config('app.name'),
                        'usuario'   => "{$user->name} {$user->lastname}",
                        'url'       => $url,
                    ]);
                $subject = Lang::get('
                    :app. Solicitud de documentos pendiente en :url', [
                        'app'       => config('app.name'),
                        'url'       => $url,
                    ]);
                break;

            case SignerProcesType::FORM_PROCESS:
                $subject = Lang::get(
                    ':app. :usuario le ha enviado un formulario que requiere su certificación o verificación.
                    Para ello acceda a la siguiente dirección :url',
                    [
                        'app'       => config('app.name'),
                        'usuario'   => "{$user->name} {$user->lastname}",
                        'url'       => $url,
                    ]
                );
                $subject = Lang::get('
                    :app. Certificación de datos pendiente en :url', [
                        'app'       => config('app.name'),
                        'url'       => $url,
                    ]);
                break;

            default:
                # VALIDATION_PROCESS
                $subject = Lang::get(':app. :usuario le ha enviado un documento que requiere su firma y validación.
                    Para ello acceda a la siguiente dirección :url', [
                        'app'       => config('app.name'),
                        'usuario'   => "{$user->name} {$user->lastname}",
                        'url'       => $url,
                    ]);
                $subject = Lang::get('
                    :app. Proceso de validaciones pendiente en :url', [
                        'app'       => config('app.name'),
                        'url'       => $url,
                    ]);
                break;
        }

        // Envía el SMS al usuario firmante
        $sended = App::make('Sms')->send(
            $signer->phone,             // El teléfono del destinatario
            $subject,                   // El mensaje informativo
        );

        // Registro el en db
        $signer->smses()->create([
            'text'      => $subject,                // El texto
            'sended_at' => $sended ? now() : null,  // Momento en que se envía
        ]);

        // Muestro el log del envío
        info("Sending SMS");
        info("To: $signer->phone");
        info("Subject: $subject");
    }

    /**
     * Envía un SMS con el enlace para la descarga del conjunto de archivos compartido
     *
     * @param User               $user    El usuario autor/creador del documento
     * @param FileSharingContact $contact El contacto
     *
     * @return void
     */
    public static function sendFileSharingSms(User $user, FileSharingContact $contact): void
    {
        $subject = Lang::get(
            ':app. :usuario ha compartido unos archivos con usted.
              Para acceder a estos acceda a la siguiente dirección :url',
            [
                'app'       => config('app.name'),
                'usuario'   => "{$user->name} {$user->lastname}",
                'url'       => url(route('workspace.set.share', ['token' => $contact->token])),
            ]
        );
        $subject = Lang::get('
            :app. Han compartido archivos con usted en :url', [
                'app'       => config('app.name'),
                'usuario'   => "{$user->name} {$user->lastname}",
                'url'       => url(route('workspace.set.share', ['token' => $contact->token])),
            ]);

        // Envía el SMS al destinatario cuyo teléfono se suministra
        $sended = App::make('Sms')->send(
            $contact->phone,            // El teléfono del destinatario
            $subject,                   // El contenido del mensaje SMS
        );

        // Registro el en db
        $contact->smses()->create([
            'text'      => $subject,                // El texto
            'sended_at' => $sended ? now() : null,  // Momento en que se envía
        ]);

        // Muestro el log del envío
        info("Sending SMS");
        info("To: $contact->phone");
        info("Subject: $subject");
    }

    /**
     * Envía SMS a un firmante con el enlace para acceder al espacio de trabajo
     * y que renove un documento que ha aportado
     *
     * @param Signer $signer El usuario firmante del documento
     *
     * @return void
     */
    public static function sendDocumentRequiredFileRevocationSms(
        $signer
    ): void {
        // Envía el SMS al destinatario cuyo teléfono se suministra
        $subject = Lang::get(
            ':app le informa que un documento que usted ha aportado vence próximamente.
            Para renovar este documento aceda a la siguiente dirección :url',
            [
                'app'  => config('app.name'),
                'url'  => url(
                    route('workspace.document.request.renew', ['token' => $signer->token])
                ),
            ]
        );
        $subject = Lang::get(
            ':app. Tiene documentos cerca de expirar, puede renovarlos en :url',
            [
                'app'  => config('app.name'),
                'url'  => url(
                    route('workspace.document.request.renew', ['token' => $signer->token])
                ),
            ]
        );

        $sended = App::make('Sms')->send(
            $signer->phone,             // El teléfono del firmante
            $subject,                   // El contenido del mensaje SMS
        );

        // Registro el en db
        $signer->smses()->create([
            'text'      => $subject,                // El texto
            'sended_at' => $sended ? now() : null,  // Momento en que se envía
        ]);

        // Muestro el log del envío
        info("Sending SMS");
        info("To: $signer->phone");
        info("Subject: $subject");
    }

    /**
     * Envía un SMS con el enlace para la descarga del conjunto de documentos compartidos
     *
     * @param User                      $user    El usuario autor/creador del documento
     * @param DocumentSharingContact    $contact El contacto
     *
     * @return void
     */
    public static function sendDocumentSharingSms(User $user, DocumentSharingContact $contact): void
    {
        // Envía el SMS al destinatario cuyo teléfono se suministra
        $subject = Lang::get(':app. :usuario le ha enviado un enlace para compartir una serie de documentos con usted.
              Para ello acceda a la siguiente dirección :url', [
            'app'       => config('app.name'),
            'usuario'   => "{$user->name} {$user->lastname}",
            'url'       => url(route('workspace.set.share', ['token' => $contact->token])),
        ]);

        $sended = App::make('Sms')->send(
            $contact->phone,            // El teléfono del destinatario
            $subject,                   // El contenido del mensaje SMS
        );

        // Registro el en db
        $contact->smses()->create([
            'text'      => $subject,                // El texto
            'sended_at' => $sended ? now() : null,  // Momento en que se envía
        ]);

        // Muestro el log del envío
        info("Sending SMS");
        info("To: $contact->phone");
        info("Subject: $subject");
    }
}
