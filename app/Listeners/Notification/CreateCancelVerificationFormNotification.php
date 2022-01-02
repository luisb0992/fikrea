<?php

/**
 * Crea una notificación cuando un usuario rechaza una verificacion de datos
 *
 * @author LuisBarDev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Listeners\Notification;

use App\Events\VerificationFormCancelEvent;
use App\Models\Notification;
use Illuminate\Support\Facades\Lang;

class CreateCancelVerificationFormNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Crea la notificacion de cancelacion del proceso
     *
     * @param  VerificationFormCancelEvent  $event          El evento de la verificacion de datos
     * @return void
     */
    public function handle(VerificationFormCancelEvent $event)
    {
        // Obtiene la verificación de datos
        $verificationForm = $event->verificationForm;

        // Obtiene el usuario que a rechazado la verificación de datos
        $signer  = $event->signer;

        // Crea el título
        $title = Lang::get('La certificación de datos :verificationForm ha sido rechazada por :name :lastname
             <a href="mailto::email">:email</a>', [
            'verificationForm'  => $verificationForm->name,
            'name'              => $signer->name,
            'lastname'          => $signer->lastname,
            'email'             => $signer->email,
        ]);

        // mensaje de la notificación
        $message = Lang::get('El usuario :name :lastname <a href="mailto::email">:email</a>
             ha rechazado su verificación de datos :verificationForm', [
            'name'                  => $signer->name,
            'lastname'              => $signer->lastname,
            'email'                 => $signer->email,
            'verificationForm'      => $verificationForm->name,
        ]);

        // crear la notificación
        Notification::create([
            'user_id'   => $verificationForm->user->id,
            'title'     => $title,
            'message'   => $message,
            'url'       => route('dashboard.verificationform.status', ['id' => $verificationForm->id]),
            'type'      => \App\Enums\NotificationTypeEnum::CANCELLED,
            'reason_cancel_request_id' => $signer->reason_cancel_request_id
        ]);
    }
}
