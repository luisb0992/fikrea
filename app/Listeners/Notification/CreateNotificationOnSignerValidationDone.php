<?php

/**
 * Crea una notificación cuando un firmante realiza una validación
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Listeners\Notification;

use Illuminate\Support\Facades\Lang;

use App\Events\SignerValidationDone;
use App\Models\Notification;
use App\Enums\ValidationType;

class CreateNotificationOnSignerValidationDone
{
    /**
     * Crea una notificación para el creador/autor del documento
     *
     * @param SignerValidationDone $event        El evento de validación realizada por el firmante
     *
     * @return void
     */
    public function handle(SignerValidationDone $event):void
    {
        // Obtiene la validación
        $validation = $event->validation;

        // Crea el título y mensaje de la notificación

        $title = Lang::get(
            'La validación de :validation ha sido realizada por :name :lastname
            <a href="mailto::email">:email</a>.',
            [
                'validation'    => (string) ValidationType::fromValue($validation->validation),
                'name'          => $validation->signer->name,
                'lastname'      => $validation->signer->lastname,
                'email'         => $validation->signer->email,
            ]
        );

        $message = Lang::get(
            'El usuario :name :lastname <a href="mailto::email">:email</a> 
            ha realizado la validación de <a href="'
            .route('dashboard.document.status', [$validation->document->id])
            .'">:validation</a> solicitada sobre el documento :document.',
            [
                'name'          => $validation->signer->name,
                'lastname'      => $validation->signer->lastname,
                'email'         => $validation->signer->email,
                'validation'    => (string) ValidationType::fromValue($validation->validation),
                'document'      => $validation->document->name,
            ]
        );

        Notification::create(
            [
                'user_id'   => $validation->document->user->id,
                'title'     => $title,
                'message'   => $message,
                'url'       => route('dashboard.document.status', ['id' => $validation->document->id]),
                'type'      => \App\Enums\NotificationTypeEnum::SUCCESSFULLY,
            ]
        );
    }
}
