<?php

/**
 * EventPolicy
 *
 * Política de acceso a las eventos del usuario
 *
 * @author LuisBarDev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\Guest;
use App\Models\Event;

class EventPolicy
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
     * Determina la política para que se puede editar o crear un evento
     *
     * @param User|null $user       El usuario o null si es un usuario invitado
     * @param Event $event          El evento
     *
     * @return bool
     */
    public function edit(?User $user, Event $event): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $event->user_id;
    }

    /**
     * Determina la política para que se puede crear un censo de partipantes para el evento
     *
     * @param User|null $user       El usuario o null si es un usuario invitado
     * @param Event $event          El evento
     *
     * @return bool
     */
    public function census(?User $user, Event $event): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $event->user_id;
    }

    /**
     * Determina la política para que se puede crear un formulario de preguntas y respuestas
     *
     * @param User|null $user       El usuario o null si es un usuario invitado
     * @param Event $event          El evento
     *
     * @return bool
     */
    public function builderQuestionAnswers(?User $user, Event $event): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $event->user_id;
    }
}
