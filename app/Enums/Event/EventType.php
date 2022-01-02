<?php

/**
 * Enumeración de los tipos de eventos en la app
 *
 * Los tipos de eventos determinan el comportamiento de un evento
 * en sus diferentes variantes
 *
 * @author LuisBarDev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Enums\Event;

use BenSampo\Enum\Enum;
use Illuminate\Support\Facades\Lang;

final class EventType extends Enum
{
    /**
     * Tipo votacion
     */
    public const VOTE                   = 1;

    /**
     * Tipo encuesta
     */
    public const SURVEY                 = 2;

    /**
     * Tipo recoleccion de firmas
     */
    public const SIGNATURE_COLLECTION   = 3;

    /**
     * Tipo encuesta y recoleccion d firmas
     */
    public const SURVEY_AND_SIGNATURE_COLLECTION   = 4;

    /**
     * Proporciona casting de la enumeración al tipo string
     * obteniendo la descripción del tipo de evento
     *
     * @return string                           tipo de evento
     */
    public function __toString():string
    {
        switch ($this->value) {
            case self::SURVEY:
                return Lang::get('Encuesta');
            case self::VOTE:
                return Lang::get('Votación');
            case self::SIGNATURE_COLLECTION:
                return Lang::get('Recolección de Firmas');
            case self::SURVEY_AND_SIGNATURE_COLLECTION:
                return Lang::get('Encuesta y Recolección de Firmas');
        }
    }
}
