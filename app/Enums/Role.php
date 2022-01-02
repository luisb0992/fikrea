<?php

/**
 * Enumeración con los roles de usuario de la aplicación
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Enums;

use BenSampo\Enum\Enum;

final class Role extends Enum
{
    /**
     * Rol de usuario registrado
     */
    public const USER           = 0;

    /**
     * Rol de administrador de la aplicación
     * lo que le da acceso al backend de la misma
     */
    public const ADMIN          = 1;
}
