<?php

/**
 * VideoPolicy
 *
 * Política de acceso a las grabaciones de Video
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\Stamp;

class StampPolicy
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
     * Determina la política para que un sello pueda ser eliminado por un usuario
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param Stamp $stamp                      El sello a estampar sobre el documento
     *
     * @return bool
     */
    public function delete(?User $user, Stamp $stamp): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $stamp->user_id;
    }
}
