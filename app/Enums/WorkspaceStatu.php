<?php

/**
 * Enumeración de los tipos de statu en la validación de un documento
 *
 * Los status son cada una de las acciones que define el proceso de solicitud de un documento
 * Cada uno de los status se contemplan aquí
 *
 * @author Jonathan Sanchez <jonathanch1991@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Enums;

use BenSampo\Enum\Enum;
use Illuminate\Support\Facades\Lang;

final class WorkspaceStatu extends Enum
{
    /**
     * Status validación
     */
    public const PENDIENTE      =  1;

    /**
     * Status validación
     */
    public const CANCELADO      =  2;

    /**
     * Status validación
     */
    public const REALIZADO      =  3;

    /**
     * Proporciona casting de la enumeración al tipo string
     * obteniendo la descripción del tipo de validación
     *
     * @return string                           El tipo de validación
     */
    public function __toString():string
    {
        switch ($this->value) {
            case self::PENDIENTE:
                return Lang::get('Pendiente');
            case self::CANCELADO:
                return Lang::get('Cancelado');
            case self::REALIZADO:
                return Lang::get('Realizado');
        }
    }
}
