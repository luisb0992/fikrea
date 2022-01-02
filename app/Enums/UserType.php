<?php

/**
 * Enumeración de los tipos de usuarios (tipos de cuentas de usuario)
 *
 * Los usuarios deben definir si su cuenta es personal (por defecto) o de empresa
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Enums;

use BenSampo\Enum\Enum;
use Illuminate\Support\Facades\Lang;

final class UserType extends Enum
{
    /**
     * Cuenta personal
     */
    public const PERSONAL_ACCOUNT = 0;

    /**
     * Cuenta de empresa, para operar desde una compañía
     */
    public const BUSSINESS_ACCOUNT = 1;

    /**
     * Proporciona casting de la enumeración al tipo string
     * obteniendo la descripción del tipo de cuenta de usuario
     *
     * @return string                           El tipo de cuenta de usuario
     */
    public function __toString():string
    {
        switch ($this->value) {
            case self::PERSONAL_ACCOUNT:
                return Lang::get('Cuenta Personal');
            case self::BUSSINESS_ACCOUNT:
                return Lang::get('Cuenta de Empresa');
        }
    }
}
