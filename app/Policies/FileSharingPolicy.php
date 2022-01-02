<?php

/**
 * FileSharingPolicy
 *
 * Política de acceso a los conjuntos de archivos compartidos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\Guest;
use App\Models\FileSharing;

class FileSharingPolicy
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
     * Determina la política para que un archivo pueda ser descargado por un usuario
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param FileSharing $fileSharing          El conjunto de archivos a descargar
     *
     * @return bool
     */
    public function download(?User $user, FileSharing $fileSharing): bool
    {
        $user ??= Guest::user();

        return  $user && $user->id === $fileSharing->user_id;
    }

    /**
     * Determina la política para que un usuario puede acceder a un archivo compartido
     * para su descarga
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param FileSharing $fileSharing          El archivo a compartir
     *
     * @return bool
     */
    public function share(?User $user, FileSharing $fileSharing): bool
    {
        $user ??= Guest::user();

        // Cualquiera puede acceder al conjunto de archivos compartidos
        return true;
    }

    /**
     * Determina la política para que un usuario puede acceder al historial del archivo compartido
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param FileSharing $fileSharing          El conjunto de archivos a descargar
     *
     * @return bool
     */
    public function history(?User $user, FileSharing $fileSharing): bool
    {
        $user ??= Guest::user();

        return  $user && $user->id === $fileSharing->user_id;
    }

    /**
     * Determina la política para que un usuario puede acceder al historial del archivo compartido
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param FileSharing $fileSharing          El conjunto de archivos a descargar
     *
     * @return bool
     */
    public function certificate(?User $user, FileSharing $fileSharing): bool
    {
        $user ??= Guest::user();

        return  $user && $user->id === $fileSharing->user_id;
    }

    /**
     * Determina la política para que un usuario puede eliminar la compartición
     *
     * @param User|null   $user
     * @param FileSharing $fileSharing
     * @return bool
     */
    public function destroy(?User $user, FileSharing $sharing): bool
    {
        $user ??= Guest::user();

        return  $user && $user->id === $sharing->user_id;
    }
}
