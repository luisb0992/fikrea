<?php

/**
 * CapturaPolicy
 *
 * Política de acceso a las capturas de pantalla
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\Capture;

class CapturePolicy
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
     * Determina la política para poder descargar una captura de pantalla asociada a la validación
     * de un documento
     *
     * @param User $user                        El usuario
     * @param Capture                           La captura de pantalla
     *
     * @return bool
     */
    public function download(User $user, Capture $capture): bool
    {
        return $user && $user->id === $capture->document->user->id;
    }
}
