<?php

/**
 * DocumentRequestPolicy
 *
 * Política de acceso a las solicitudes de documentos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\Guest;
use App\Models\DocumentRequest;

class DocumentRequestPolicy
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
     * Determina la política para que se puede editar una solicitud de documentos
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param DocumentRequest $documentRequest  La solicitud de documentos
     *
     * @return bool
     */
    public function edit(?User $user, DocumentRequest $documentRequest): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $documentRequest->user_id;
    }

    /**
     * Determina la política para que se puedan asignar personas "firmantes" a una solicitud de documentos
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param DocumentRequest $documentRequest  La solicitud de documentos
     *
     * @return bool
     */
    public function signers(?User $user, DocumentRequest $documentRequest): bool
    {
        $user ??= Guest::user();

        return $user->id === $documentRequest->user_id;
    }

    /**
     * Determina la política para que se puede consultar el estado de la solicitud
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param DocumentRequest $documentRequest  La solicitud de documentos
     *
     * @return bool
     */
    public function status(?User $user, DocumentRequest $documentRequest): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $documentRequest->user_id;
    }

    /**
     * Determina la política para que se puedan descargar los archivos de la solicitud de documentos
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param DocumentRequest $documentRequest  La solicitud de documentos
     *
     * @return bool
     */
    public function download(?User $user, DocumentRequest $documentRequest): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $documentRequest->user_id;
    }

    /**
     * Determina la política para que se puedan renovar archivos en la solicitud de documentos
     *
     * Cuando en la solicitud no hay documentos al expirar
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param DocumentRequest $documentRequest  La solicitud de documentos
     *
     * @return bool
     */
    public function renew(?User $user, DocumentRequest $documentRequest): bool
    {
        return $documentRequest->expiringDocuments()->count() || false;
    }

    /**
     * Determina la política para que el usuario pueda generar el certificado
     * del proceso de solicitud de documentos
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param DocumentRequest $documentRequest  La solicitud de documentos
     *
     * @return bool
     */
    public function certificate(?User $user, DocumentRequest $documentRequest): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $documentRequest->user_id;
    }
}
