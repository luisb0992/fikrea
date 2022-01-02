<?php

/**
 * AudioPolicy
 *
 * Política de acceso a las grabaciones de Audio
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\Audio;

class AudioPolicy
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
     * Determina la política para poder descargar una grabación de audio asociada a la validación
     * de un documento
     *
     * @param User $user                        El usuario
     * @param Audio $audio                      La grabación de audio
     *
     * @return bool
     */
    public function download(User $user, Audio $audio): bool
    {
        return $user && $user->id === $audio->document->user->id;
    }
}
