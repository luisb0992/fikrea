<?php

/**
 * DocumentPolicy
 *
 * Política de acceso a los documentos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\Guest;
use App\Models\Document;

class DocumentPolicy
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
     * Determina la política para que un documento pueda ser visualizado por un usuario
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param Document $document                El documento a visualizar
     *
     * @return bool
     */
    public function view(?User $user, Document $document): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $document->user_id;
    }

    /**
     * Determina la política para que un documento pueda ser actualizado por un usuario
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param Document $document                El documento a actualizar
     *
     * @return bool
     */
    public function update(?User $user, Document $document): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $document->user_id;
    }

    /**
     * Determina la política para que un documento pueda ser eliminado por un usuario
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param Document $document                El documento a eliminar
     *
     * @return bool
     */
    public function delete(?User $user, Document $document): bool
    {
        $user ??= Guest::user();
        
        return $user && $user->id === $document->user_id;
    }

    /**
     * Determina la política para que un documento eliminado pueda ser recuperado por un usuario
     *
     * @param User|null $user                   El usuario o null si es un uusuario invitado
     * @param Document $document                El documento a recuperar
     *
     * @return bool
     */
    public function recover(?User $user, Document $document): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $document->user_id;
    }

    /**
     * Determina la política para que un documento pueda ser decargado por un usuario
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param Document $document                El documento a descargar
     *
     * @return bool
     */
    public function download(?User $user, Document $document): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $document->user_id;
    }

    /**
     * Determina la política para que la firma de un documento pueda ser
     * configurada por un usuario
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param Document $document                El documento a firmar
     *
     * @return bool
     */
    public function config(?User $user, Document $document): bool
    {
        $user ??= Guest::user();

        // El documento debe ser del usuario y no haber sido ya enviado a firmar
        return $user && $user->id === $document->user_id && !$document->sent;
    }

    /**
     * Determina la política para que el estado de validación de un documento
     * pueda ser consultado por un usuario
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param Document $document                El documento a firmar
     *
     * @return bool
     */
    public function status(?User $user, Document $document): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $document->user_id;
    }

    /**
     * Determina la política para que la descarga de un certificado de validación de un documento
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param Document $document                El documento a firmar
     *
     * @return bool
     */
    public function certificate(?User $user, Document $document): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $document->user_id;
    }

    /**
     * Determina la política para visualizar el historico de visitas de los firmantes sobre un documento
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param Document $document                El documento a visualizar
     *
     * @return bool
     */
    public function history(?User $user, Document $document): bool
    {
        $user ??= Guest::user();

        return $user->id === $document->user_id;
    }

    /**
     * Determina la política para que un documento pueda ser validado por un usuario
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param Document $document                El documento a validar
     *
     * @return bool
     */
    public function validated(?User $user, Document $document): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $document->user_id;
    }

    /**
     * Determina la política para visualizar la vista de creacion/seleccion de formulario de datos especificos
     *
     * @param User|null $user                   El usuario o null si es un usuario invitado
     * @param Document $document                El documento a validar
     *
     * @return bool
     */
    public function formData(?User $user, Document $document): bool
    {
        $user ??= Guest::user();

        return $user && $user->id === $document->user_id;
    }
}
