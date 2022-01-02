<?php

/**
 * PassportPolicy
 *
 * Política de acceso a los documentos identificativos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\Passport;

class PassportPolicy
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
     * Determina la política para poder descargar un documento identificativo asociado a la validación
     * de un documento
     *
     * @param User $user                        El usuario
     * @param Passport $passport                El documento identificativo
     *
     * @return bool
     */
    public function download(User $user, Passport $passport): bool
    {
        return $user &&  $user->id === $passport->document->user->id;
    }
}
