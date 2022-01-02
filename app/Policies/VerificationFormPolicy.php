<?php

/**
 * VerificationFormPolicy
 *
 * Política de acceso a las solicitudes de documentos
 *
 * @author LuisBarDev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\Guest;
use App\Models\VerificationForm;

class VerificationFormPolicy
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
     * Determina la política para que se puede editar o crear una verificación de datos
     * independiente del documento
     *
     * @param User|null $user                       El usuario o null si es un usuario invitado
     * @param VerificationForm $verificationForm    La verificación de datos
     *
     * @return bool
     */
    public function edit(?User $user, VerificationForm $verificationForm): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $verificationForm->user_id;
    }

    /**
     * Determina la política para que se acceder a la vista de seleccion de usuarios
     * asignados a la verificación de datos
     *
     * @param User|null $user                       El usuario o null si es un usuario invitado
     * @param VerificationForm $verificationForm    La verificación de datos
     *
     * @return bool
     */
    public function signers(?User $user, VerificationForm $verificationForm): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $verificationForm->user_id;
    }

    /**
     * Determina la política para que se puede consultar el estado de la La verificación de datos
     * independiente del documento
     *
     * @param User|null $user                       El usuario o null si es un usuario invitado
     * @param VerificationForm $verificationForm    La verificación de datos
     *
     * @return bool
     */
    public function status(?User $user, VerificationForm $verificationForm): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $verificationForm->user_id;
    }

    /**
     * Determina la política para que se puede descargar y obtener el certificado de verificación
     *
     * @param User|null $user                       El usuario o null si es un usuario invitado
     * @param VerificationForm $verificationForm    La verificación de datos
     *
     * @return bool
     */
    public function certificate(?User $user, VerificationForm $verificationForm): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $verificationForm->user_id;
    }

    /**
     * Determina la política para que pueda acceder o no a visualizar el historial de visitas
     *
     * @param User|null $user                       El usuario o null si es un usuario invitado
     * @param VerificationForm $verificationForm    La verificación de datos
     *
     * @return bool
     */
    public function history(?User $user, VerificationForm $verificationForm): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $verificationForm->user_id;
    }
}
