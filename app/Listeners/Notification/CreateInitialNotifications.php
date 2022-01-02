<?php

/**
 * Listener para crear unas notificaciones iniciales con ayudas sobre el funcionamiento de la aplicación
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Listeners\Notification;

use Illuminate\Support\Facades\Lang;
use App\Events\UserCreated;

class CreateInitialNotifications
{
    /**
     * Crea las notificaciones iniciales que se muestran al usuario en su página de inicio de la
     * zona de usuario o dashboard
     *
     * @param UserCreated $event                El evento de creación del nuevo usuario
     *
     * @return void
     */
    public function handle(UserCreated $event):void
    {
        // Obtiene el usuario creado
        $user = $event->user;

        // Crea unas notificaciones iniciales en la página de inicio de su zona de usuario o dashboard
        // para orientar al usuario
        $user->notifications()->createMany(
            [
                //
                // Ayuda para subir los archivos
                //
                [
                    'title'     => Lang::get('Ya puede subir y compartir sus archivos'),
                    'message'   =>
                        Lang::get('Suba su archivos y compártalos rápidamente o inicie con ellos el proceso de firma'),
                    'url'       => route('dashboard.file.upload'),
                ],

                //
                // Ayuda para crear una solicitud de documentos
                //
                [
                    'title'     => Lang::get('Solicite los documentos que necesita para su trabajo'),
                    'message'   =>
                        Lang::get('Puede crear una solicitud de documentos a sus empleados, amigos, colaboradores,...'),
                    'url'       => route('dashboard.document.request.edit'),
                ],
            ]
        );
    }
}
