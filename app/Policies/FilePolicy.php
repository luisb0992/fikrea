<?php

/**
 * FilePolicy
 *
 * Política de acceso a los archivos subidos
 *
 * @author    javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\Guest;
use App\Models\File;

class FilePolicy
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
     * Determina la política para que un archivo pueda ser eliminado por un usuario
     *
     * @param User|null $user El usuario o null si es un usuario invitado
     * @param File      $file El archivo a eliminar
     *
     * @return bool
     */
    public function delete(?User $user, File $file): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $file->user_id;
    }

    /**
     * Determina la política para que un archivo pueda ser llevado al proceso de firma
     *
     * @param User|null $user El usuario o null si es un usuario invitado
     * @param File      $file El archivo a eliminar
     *
     * @return bool
     */
    public function sign(?User $user, File $file): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $file->user_id;
    }

    /**
     * Determina la política para que un archivo pueda ser descargado por un usuario
     *
     * @param User|null $user El usuario o null si es un usuario invitado
     * @param File      $file El archivos a descargar
     *
     * @return bool
     */
    public function download(?User $user, File $file): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $file->user_id;
    }

    /**
     * Determina la política para que un usuario puede acceder a un archivo compartido
     * para su descarga
     *
     * @param User|null $user El usuario o null si es un usuario invitado
     * @param File      $file El archivo a compartir
     *
     * @return bool
     */
    public function share(?User $user, File $file): bool
    {
        $user ??= Guest::user();

        // Caulquiera puede acceder al archivo compartido
        return true;
    }

    /**
     * Determina la política para que un usuario puede actualizar el nombre y el location de archivo
     *
     * @param User|null $user El usuario o null si es un usuario invitado
     * @param File      $file El archivo a modificar
     *
     * @return bool
     */
    public function update(?User $user, File $file): bool
    {
        $user ??= Guest::user();

        // Caulquiera puede acceder al archivo compartido
        return $user && $user->id === $file->user_id;
    }

    /**
     * Determina la política para que un usuario pueda acceder al historial de un fichero
     *
     * @param User|null $user
     * @param File      $file
     * @return bool
     */
    public function history(?User $user, File $file): bool
    {
        $user ??= Guest::user();

        // Cualquiera puede acceder al historial del archivo
        return true;
    }

    /**
     * Determina la política para que un usuario puede acceder al historial del archivo compartido
     *
     * @param User|null $user El usuario o null si es un usuario invitado
     * @param File      $file El conjunto de archivos a descargar
     *
     * @return bool
     */
    public function certificate(?User $user, File $file): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $file->user_id;
    }
}
