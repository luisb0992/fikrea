<?php

/**
 * Crea una notificación cuando un firmante rechaza una validación
 *
 * @author Jonathan Sanchez <jonathanch1991@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Listeners\Notification;

use Illuminate\Support\Facades\Lang;

use App\Events\SignerValidationCancel;
use App\Models\Notification;
use App\Enums\ValidationType;

class CreateNotificationOnSignerValidationCancel
{
    /**
     * Crea una notificación para el creador/autor del documento
     *
     * @param SignerValidationCancel $event        El evento de validación rechazado por el firmante
     *
     * @return void
     */
    public function handle(SignerValidationCancel $event):void
    {
        // Obtiene la validación
        $validation = $event->validation;

        // Crea el título y mensaje de la notificación

        $title = Lang::get(
            'La validación de :validation ha sido rechazada por :name :lastname
             <a href="mailto::email">:email</a>',
            [
                'validation'    => (string) ValidationType::fromValue($validation->validation),
                'name'          => $validation->signer->name,
                'lastname'      => $validation->signer->lastname,
                'email'         => $validation->signer->email,
            ]
        );

        $href = route('dashboard.document.status', [$validation->document->id]);

        $message = Lang::get(
            'El usuario :name :lastname <a href="mailto::email">:email</a> 
             ha rechazado la validación de <a href=":href">:validation</a> solicitada sobre el documento :document.',
            [
                'name'          => $validation->signer->name,
                'lastname'      => $validation->signer->lastname,
                'email'         => $validation->signer->email,
                'validation'    => (string) ValidationType::fromValue($validation->validation),
                'document'      => $validation->document->name,
                'href'          => $href,
            ]
        );

        Notification::create(
            [
                'user_id'   => $validation->document->user->id,
                'title'     => $title,
                'message'   => $message,
                'url'       => $href,
                'type'      => \App\Enums\NotificationTypeEnum::CANCELLED,
                'reason_cancel_request_id' => $validation->signer->reason_cancel_request_id
            ]
        );
    }
}
