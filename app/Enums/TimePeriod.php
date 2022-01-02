<?php

/**
 * Enumeración con los periodos de tiempo habituales
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Enums;

use BenSampo\Enum\Enum;
use Illuminate\Support\Facades\Lang;

final class TimePeriod extends Enum
{
    /**
     * Día
     *
     * @var int
     */
    public const DAY       =  1;

    /**
     * Semana
     *
     * @var int
     */
    public const WEEK      =  7;

    /**
     * Mes
     *
     * @var int
     */
    public const MONTH     =  30;

    /**
     * Año
     *
     * @var int
     */
    public const YEAR      =  365;

    /**
     * Proporciona casting de la enumeración al tipo string
     * obteniendo la descripción
     *
     * @return string                           La unidad de tiempo de la validez del documento
     */
    public function __toString():string
    {
        switch ($this->value) {
            case self::DAY:
                return Lang::get('días');
            case self::MONTH:
                return Lang::get('meses');
            case self::YEAR:
                return Lang::get('años');
        }
    }
}
