<?php

/**
 * Enumeración de los tipos de formulario de datos especificos
 *
 * Los tipos de formulario en para un documento, sea validacion o requerimientos de datos
 * llegan a ser del tipo particular o empresarial
 *
 * @author LuisBarDev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Enums;

use BenSampo\Enum\Enum;
use Illuminate\Support\Facades\Lang;

final class FormType extends Enum
{
    /**
     * Formulario del tipo particular, para personas naturales
     */
    public const PARTICULAR_FORM    = 1;

    /**
     * Formulario del tipo empresarial, para ambiente juridico
     */
    public const BUSINESS_FORM      = 2;

    /**
     * Proporciona casting de la enumeración al tipo string
     * obteniendo la descripción del tipo de formulario
     *
     * @return string                           El tipo de formulario de datos
     */
    public function __toString():string
    {
        switch ($this->value) {
            case self::PARTICULAR_FORM:
                return Lang::get('Formulario particular');
            case self::BUSINESS_FORM:
                return Lang::get('Formulario empresarial');
        }
    }
}
