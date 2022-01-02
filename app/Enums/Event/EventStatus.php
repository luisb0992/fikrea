<?php

/**
 * Enumeración de los estados de los eventos creados
 *
 * Los estados de los eventos determinan el comportamiento de un evento
 * en sus diferentes variantes
 *
 * @author LuisBarDev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Enums\Event;

use BenSampo\Enum\Enum;
use Illuminate\Support\Facades\Lang;

final class EventStatus extends Enum
{
    /**
     * Evento activo
     */
    public const ACTIVE_EVENT       = 1;

    /**
     * Evento programado
     */
    public const SCHEDULED_EVENT    = 2;

    /**
     * Evento cerrado
     */
    public const CLOSED_EVENT       = 3;

    /**
     * Evento eliminado
     */
    public const DELETED_EVENT      = 4;

    /**
     * Evento en estado de borrador
     */
    public const DRAFT_EVENT        = 5;

    /**
     * Proporciona casting de la enumeración al tipo string
     * obteniendo la descripción del estado del evento
     *
     * @return string                           estado del evento
     */
    public function __toString():string
    {
        switch ($this->value) {
            case self::ACTIVE_EVENT:
                return Lang::get('Evento activo');
            case self::SCHEDULED_EVENT:
                return Lang::get('Evento programado');
            case self::CLOSED_EVENT:
                return Lang::get('Evento cerrado');
            case self::DELETED_EVENT:
                return Lang::get('Evento eliminado');
            case self::DRAFT_EVENT:
                return Lang::get('Evento como borrador');
        }
    }
}
