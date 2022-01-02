<?php

/**
 * ContactPolicy
 *
 * Política de acceso a los contactos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\Guest;
use App\Models\Contact;

class ContactPolicy
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
     * Determina la política para que un contacto pueda ser visualizado por un usuario
     *
     * @param User|null $user                   El usuario o null para el usuario invitado
     * @param Contact   $contact                El contacto a visualizar
     *
     * @return bool
     */
    public function view(?User $user, Contact $contact): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $contact->user_id;
    }

    /**
     * Determina la política para que un contacto pueda ser actualizado por un usuario
     *
     * @param User|null $user                   El usuario o null para el usuario invitado
     * @param Contact   $contact                El contacto a actualizar
     *
     * @return bool
     */
    public function update(?User $user, Contact $contact): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $contact->user_id;
    }

    /**
     * Determina la política para que un contacto pueda ser eliminado por un usuario
     *
     * @param User|null $user                   El usuario o null para el usuario invitado
     * @param Contact   $contact                El contacto a eliminar
     *
     * @return bool
     */
    public function delete(?User $user, Contact $contact): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $contact->user_id;
    }
}
