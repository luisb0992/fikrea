<?php

namespace App\Listeners\Notification;

use App\Events\VerificationFormDoneEvent;
use App\Models\Notification;
use Illuminate\Support\Facades\Lang;

class CreateDoneVerificationFormNotification
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
     * Crea la notificacion cuando el usuario realiza la verificación de datos
     *
     * @param  VerificationFormDoneEvent  $event        El evento
     * @return void
     */
    public function handle(VerificationFormDoneEvent $event)
    {
        // Obtiene la verificación de datos
        $verificationForm = $event->verificationForm;

        // Obtiene el usuario que hja respondido a la verificación de datos
        $signer  = $event->signer;

        // Crea el título
        $title = Lang::get('La certificación de datos <b>:verificationForm</b> ha sido realizada por :name :lastname
             <a href="mailto::email">:email</a>', [
            'verificationForm'  => $verificationForm->name,
            'name'              => $signer->name,
            'lastname'          => $signer->lastname,
            'email'             => $signer->email,
        ]);

        // Crea el mensaje
        $message = Lang::get('El usuario :name :lastname <a href="mailto::email">:email</a>
             ha respondido a su certificación de datos :verificationForm', [
            'name'              => $signer->name,
            'lastname'          => $signer->lastname,
            'email'             => $signer->email,
            'verificationForm'  => $verificationForm->name,
        ]);

        // crea la notificación
        Notification::create([
            'user_id'   => $verificationForm->user->id,
            'title'     => $title,
            'message'   => $message,
            'url'       => route('dashboard.verificationform.status', ['id' => $verificationForm->id]),
            'type'      => \App\Enums\NotificationTypeEnum::SUCCESSFULLY
        ]);
    }
}
