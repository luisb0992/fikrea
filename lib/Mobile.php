<?php

namespace Fikrea;

use Illuminate\Http\Request;

/**
 * La clase Mobile
 *
 * Utilidades para la detección y manipulación de dispositivos móviles, smartphones y tablets
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 *
 * @example
 *
 * use Fikrea\Mobile;
 *
 * if (Mobile::isMobile()) {
 *      // El dispositivo es un móvil o una tablet
 * }
 *
 */

class Mobile extends AppObject
{
    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Comprueba si es un dispositivo móvil
     *
     * @return bool                             true si la petición se realiza desde un móvil o una tablet
     *                                          false en caso contrario
     */
    public static function isMobile(): bool
    {
        // Obtiene el agente de usuario
        $user_agent = request()->server('HTTP_USER_AGENT');

        // Comprueba el agente de usuario contra la expresión regular que contiene patrones conocidos
        // de agentes de usuario utilizados por dispositivos móviles y tablets
        return preg_match(
            "/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i",
            $user_agent
        );
    }

    /**
     * Comprueba si no es un dispositivo móvil
     *
     * @return bool                             true si la petición no se realiza desde un dispositivo móvil
     *                                          o una tablet
     *                                          false en caso contrario
     */
    public static function isNotMobile(): bool
    {
        return !self::isMobile();
    }
}
