<?php

/**
 * Enumeración de los motivos de por el cual se rechaza la solicitud de un documento
 *
 * Los motivos por el cual se rechaza el la solicitud de un documento
 * Cada uno de los motivos se contemplan aquí
 *
 * @author Jonathan Sanchez <jonathanch1991@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Enums;

use BenSampo\Enum\Enum;
use Illuminate\Support\Facades\Lang;

final class ReasonCancel extends Enum
{
    /**
     * Motivo por el cual se rechaza la solicitud
     */
    public const ANOTHER_TIME     = 1;

    /**
     * Motivo por el cual se rechaza la solicitud
     */
    public const TECHNICAL_PROBLEMS = 2;

    /**
     * Motivo por el cual se rechaza la solicitud
     */
    public const WHAT_REQUESTED   = 3;

    /**
     * Motivo por el cual se rechaza la solicitud
     */
    public const UNDERSTAND_REQUESTED = 4;

    /**
     * Motivo por el cual se rechaza la solicitud
     */
    public const DISAGREE        = 5;

    /**
     * Motivo por el cual se rechaza la solicitud
     */
    public const NOT_PERFORM     = 6;

    /**
     * Proporciona casting de la enumeración al tipo string
     * obteniendo la descripción del tipo de validación
     *
     * @return string                           El tipo de validación
     */
    public function __toString():string
    {
        switch ($this->value) {
            case self::ANOTHER_TIME:
                return Lang::get('No puedo realizar esta validación ahora mismo, lo intentaré en otro momento');
            case self::TECHNICAL_PROBLEMS:
                return Lang::get('No he logrado llevar a cabo esta validación por problemas técnicos
                 con la plataforma digital que proporciona el proceso');
            case self::WHAT_REQUESTED:
                return Lang::get('Carezco de esta información para llevar a cabo lo que se me pide');
            case self::UNDERSTAND_REQUESTED:
                return Lang::get('No he entendido lo que se me pide y/o la información que esta validación
                 tiene en su interior');
            case self::DISAGREE:
                return Lang::get('No estoy de acuerdo con esta validación y la rechazo');
            case self::NOT_PERFORM:
                return Lang::get('No quiero realizar esta validación');
        }
    }
}
