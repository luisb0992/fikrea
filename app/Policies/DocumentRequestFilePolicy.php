<?php

/**
 * DocumentRequestFilePolicy
 *
 * Política de acceso a los archivos que forman parte de las solicitudes de documentos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\Guest;
use App\Models\DocumentRequestFile;

class DocumentRequestFilePolicy
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
     * Determina la política para que se pueda descargar un archivo de una solicitud de documentos
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param DocumentRequestFile $file         El archivo de la solicitud de documentos
     *
     * @return bool
     */
    public function download(?User $user, DocumentRequestFile $file): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $file->user_id;
    }
}
