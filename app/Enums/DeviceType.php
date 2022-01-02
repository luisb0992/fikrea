<?php

/**
 * Enumeración de los tipos de dispositivos
 *
 * El dispositivo del firmante o usuario puede ser Tablet, Movil o Computer
 *
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Enums;

use BenSampo\Enum\Enum;
use Illuminate\Support\Facades\Lang;

final class DeviceType extends Enum
{
    /**
     * Dispositivo Desktop Computer
     */
    const COMPUTER_DEVICE = 1;

    /**
     * Dispositivo móvil o teléfonos
     */
    const MOBILE_DEVICE = 2;

    /**
     * Dispositivo Tablet
     */
    const TABLET_DEVICE = 3;

    /**
     * Proporciona casting de la enumeración al tipo string
     * obteniendo la descripción del tipo de dispositivo
     *
     * @return string                           El tipo de dispositivo
     */
    public function __toString():string
    {
        switch ($this->value) {
            case self::COMPUTER_DEVICE:
                return Lang::get('PC/Laptop');
            case self::MOBILE_DEVICE:
                return Lang::get('Dispositivo Móvil');
            case self::TABLET_DEVICE:
                return Lang::get('Tablet');
        }
    }
}
