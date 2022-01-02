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
use App\Models\Video;

class VideoPolicy
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
     * Determina la política para poder descargar una grabación de video asociada a la validación
     * de un documento
     *
     * @param User $user                        El usuario
     * @param Video $video                      La grabación de video
     *
     * @return bool
     */
    public function download(User $user, Video $video): bool
    {
        return $user && $user->id === $video->document->user->id;
    }
}
