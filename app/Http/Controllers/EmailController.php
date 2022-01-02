<?php

/**
 * EmailController
 *
 * Controlador de envío de correos
 *
 * @author    javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

/**
 * Modelos utilizados
 */

use App\Models\User;
use App\Models\Guest;
use App\Models\Document;
use App\Models\DocumentRequest;
use App\Models\Signer;
use App\Models\Order;
use App\Models\Company;
use App\Models\Validation;
use App\Models\FileSharing;
use App\Models\FileSharingContact;
use App\Models\DocumentRequestFile;
use App\Models\VerificationForm;

/**
 * Correos utilizados
 */

use App\Mail\EmailResetPasswordRequest;
use App\Mail\EmailContact;
use App\Mail\EmailAccountConfirmation;
use App\Mail\EmailWorkSpaceAccessLink;
use App\Mail\EmailConfirmDocumentShared;
use App\Mail\EmailConfirmDocumentRequestShared;
use App\Mail\EmailConfirmVerificationFormShared;
use App\Mail\EmailSendFileSharing;
use App\Mail\EmailSessionTokenLink;
use App\Mail\EmailSubscriptionRenewSuccess;
use App\Mail\EmailValidationSuccess;
use App\Mail\EmailDocumentRequestSuccess;
use App\Mail\EmailNotifyExpirationFile;
use App\Mail\EmailFailedLoginAttempt;
use App\Mail\EmailShareBilling;
use App\Mail\EmailDocumentRequestRenewed;
use App\Mail\EmailSendDocumentSharing;
use App\Models\Contact;
use App\Models\DocumentSharing;
use App\Models\DocumentSharingContact;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    /**
     * Envía el correo de bienvenida al nuevo usuario registrado que incluye
     * un enlace que debe ser activado por el receptor del mensaje para terminar
     * de validar su cuenta en el sistema
     *
     * @param User $user El usuario registrado
     *
     * @return void
     */
    public static function sendWellcomeEmail(User $user): void
    {
        Mail::to($user->email)->send(new EmailAccountConfirmation($user));
    }

    /**
     * Envía correo con url de descarga para el documento
     *
     * @param FileSharing        $fileSharing La comparticion de archivos
     * @param FileSharingContact $contact     El contacto
     *
     * @return void
     */
    public static function sendFileSharingEmail(FileSharing $fileSharing, FileSharingContact $contact): void
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        Mail::to($contact->email)->send(new EmailSendFileSharing($user, $fileSharing, $contact));
    }

    /**
     * Envía el correo al usuario para que pueda cambiar su contraseña
     * de acceso en caso de haberla olvidado
     *
     * @param User $user El usuario que desea recuperar su contraseña
     *
     * @return void
     */
    public static function sendPasswordRecoveryEmail(User $user): void
    {
        Mail::to($user->email)->send(new EmailResetPasswordRequest($user));
    }

    /**
     * Envía un correo de petición de contacto al administrador
     *
     * Un usuario ha rellenado el formulario de contacto
     * y se envía un mensaje al administrador para que le responda
     *
     * @param object $contact El contacto
     *
     * @return void
     */
    public static function sendContactEmail(object $contact): void
    {
        // Obtiene la dirección de correo del administrador de contacto del sitio
        $admin = config('app.contact');

        Mail::to($admin)->send(new EmailContact($contact));
    }

    /**
     * Envía el correo con el acceso al espacio de trabajo
     *
     * @param User                                   $user    El usuario autor del documento
     * @param Signer                                 $signer  El usuario firmante del documento
     * @param DocumentRequestSharing|DocumentSharing $sharing La compartición
     *
     * @return void
     */
    public static function sendWorkSpaceAccessEmail(
        User $user, Signer $signer, $sharing=null
    ): void {
        Mail::to($signer->email)->send(new EmailWorkSpaceAccessLink($user, $signer, $sharing));
    }

    /**
     * Envía un correo al creador/autor del documento confirmándole que ha enviado
     * un documento para su firma y validación
     *
     * @param User     $creator  El usuario creador/autor
     * @param Document $document El documento
     *
     * @return void
     */
    public static function confirmDocumentShared(User $creator, Document $document): void
    {
        Mail::to($creator->email)->send(new EmailConfirmDocumentShared($creator, $document));
    }

    /**
     * Envía un correo al creador/autor del documento confirmándole que ha enviado
     * una solicitud de documentos
     *
     * @param User            $creator El usuario creador/autor
     * @param DocumentRequest $request La solicitud de documentos
     *
     * @return void
     */
    public static function confirmDocumentRequestShared(User $creator, DocumentRequest $request): void
    {
        Mail::to($creator->email)->send(new EmailConfirmDocumentRequestShared($creator, $request));
    }

    /**
     * Envía un correo al creador/autor del documento confirmándole que ha enviado
     * una solicitud de documentos
     *
     * @param User              $creator El usuario creador/autor
     * @param VerificationForm  $request La solicitud de documentos
     *
     * @return void
     */
    public static function confirmVerificationFormShared(User $creator, VerificationForm $verificationForm): void
    {
        Mail::to($creator->email)->send(new EmailConfirmVerificationFormShared($creator, $verificationForm));
    }

    /**
     * Envía el correo con el token de usuario invitado
     *
     * @param User $user El usuario
     *
     * @return void
     */
    public static function sendSessionTokenRecoveryEmail(User $user): void
    {
        Mail::to($user->email)->send(new EmailSessionTokenLink($user));
    }

    /**
     * Envía el correo de confirmación que la subscripción se ha renocado con éxito
     *
     * @param User  $user  El usuario
     * @param Order $order El pedido
     *
     * @return void
     */
    public static function subscriptionRenewSuccessEmail($user, Order $order): void
    {
        $email = $user->billing->email ? $user->billing->email : $user->email;
        Mail::to($email)->send(new EmailSubscriptionRenewSuccess($order));
    }

    /**
     * Envía un correo al creador/autor del documento que un firmanete ha efectuado
     * una validación sobre el mismo
     *
     * @param Validation $validation La validación
     *
     * @return void
     */
    public static function validationDone(Validation $validation): void
    {
        // El creador/autor del documento
        $creator = $validation->document->user;

        // Sólo si tiene en su configuración el envío de notificaciones activo
        // se envía el mensaje de correo
        if ($creator->config->notification->receive) {
            Mail::to($creator->email)->send(new EmailValidationSuccess($validation));
        }
    }

    /**
     * Envía un correo al creador/autor del documento que un firmanete ha efectuado
     * una validación sobre el mismo
     *
     * @param DocumentRequest $request La solicitud de documentos
     * @param Signer          $signer  El usuario "firmante"
     *
     * @return void
     */
    public static function requestDocumentDone(DocumentRequest $request, Signer $signer): void
    {
        // Si soy el creador del documento, no me envío la notificación
        if (!$signer->creator) {
            // El creador/autor de la solicitud de documentos
            $creator = $request->user;

            // Sólo si tiene en su configuración el envío de notificaciones activo
            // se envía el mensaje de correo
            if ($creator->config->notification->receive) {
                Mail::to($creator->email)->send(new EmailDocumentRequestSuccess($request, $signer));
            }
        }
    }

    /**
     * Envía un correo al creador/autor del documento que un firmanete ha efectuado
     * una renovación de un documento sobre el mismo
     *
     * @param DocumentRequest $request La solicitud de documentos
     * @param Signer          $signer  El usuario "firmante"
     *
     * @return void
     */
    public static function renewRequestDocumentDone(DocumentRequest $request, Signer $signer): void
    {
        // El creador/autor de la solicitud de documentos
        $creator = $request->user;

        // Sólo si tiene en su configuración el envío de notificaciones activo
        // se envía el mensaje de correo
        if ($creator->config->notification->receive) {
            Mail::to($creator->email)->send(new EmailDocumentRequestRenewed($request, $signer));
        }
    }

    /**
     * Envía el correo a un firmante con el acceso al espacio de trabajo
     * para que renove un documento que ha aportado
     *
     * @param Signer $signer El usuario firmante del documento
     *
     * @return void
     */
    public static function sendDocumentRequiredFileRevocationEmail(
        $signer
    ): void {
        Mail::to($signer->email)->send(
            new EmailNotifyExpirationFile($signer)
        );
    }

    /**
     * Envía el correo a un usuario con info sobre intento fallido de acceso a la app
     *
     * @param User    $user    El usuario
     * @param Request $request La petición
     *
     * @return void
     */
    public static function sendFailedLoginAttemptEmail(
        User $user,
        Request $request,
        $geoip
    ): void {
        Mail::to($user->email)->send(
            new EmailFailedLoginAttempt($user, $request, $geoip)
        );
    }

    /**
     * Envía el correo a la persona o empresa con la que compartira los datos de facturacion
     *
     * @param $company    los datos de facturacion
     * @param $email      El email al que se le enviara el correo
     * @param User $user  El usuario
     * @return void
     */
    public static function shareBillingEmail($company, $email, User $user)
    {
        Mail::to($email)->send(new EmailShareBilling($user, $company));
    }

    /**
     * Envía correo con url de descarga para el documento
     *
     * @param DocumentSharing           $document   El documento compartido
     * @param DocumentSharingContact    $contact    El contacto compartido
     *
     * @return void
     */
    public static function sendDocumentSharingEmail(
        DocumentSharing $documentSharing,
        DocumentSharingContact $documentSharingContact
    ): void {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        Mail::to($documentSharingContact->email)->send(
            new EmailSendDocumentSharing($user, $documentSharing, $documentSharingContact)
        );
    }
}
