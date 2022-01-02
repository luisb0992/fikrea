<?php

/**
 * Trait para obtener el dispositivo que utiliza el usuario en su conección
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers\Traits;

use App\Enums\DeviceType;

/**
 * Libreria MobileDetect
 */
use Detection\MobileDetect;

trait UserDeviceTrait
{
    /**
     * Obtiene el dispositivo que ha usado el cliente, usuario, firmante
     *
     * @return int               El tipo de dispositivo @see MobileDetect
     */
    public function getDevice() : int
    {
        $detect = new MobileDetect;

        return ($detect->isMobile()?
            ($detect->isTablet()?
                DeviceType::TABLET_DEVICE
                :
                DeviceType::MOBILE_DEVICE)
            :
            DeviceType::COMPUTER_DEVICE);
    }
}
