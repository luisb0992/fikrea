<?php

/**
 * Enumeración de los tipos de notificaciones
 *
 * Las notificaciones pueden ser para mostrar el completamiento de acciones,
 * cancelación, o estados de alertas sobre estos procesos.
 *
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Enums;

use BenSampo\Enum\Enum;
use Illuminate\Support\Facades\Lang;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class NotificationTypeEnum extends Enum
{
    /**
     * Notificación de acción realizada con éxito
     */
    const SUCCESSFULLY = 1;

    /**
     * Notificación de acción cancelada
     */
    const CANCELLED = 2;

    /**
     * Notificación de acción en estado de alerta
     */
    const ATTENTION = 3;
}
