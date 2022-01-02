<?php

/**
 * NotificationPolicy
 *
 * Política de acceso a las notificaciones del usuario
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\Guest;
use App\Models\Notification;

class NotificationPolicy
{
    use HandlesAuthorization;

    /**
     * El constructor
     */
    public function __construct()
    {
        //
    }

    /**
     * Determina la política para poder marcar como leída una notificación
     *
     * @param User|null    $user                El usuario o null si es el usuario invitado
     * @param Notification $notification        La notificación
     *
     * @return bool
     */
    public function read(?User $user, Notification $notification): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $notification->user->id;
    }
}
