<?php

/**
 * Enumeración de las diferentes redes sociales disponibles para compartir en fikrea
 *
 * Las redes sociales descritas son las que dispone la app y en donde se pueden compartir
 * archivos o documentos por medio de ellas
 *
 * @author LuisBarDev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Enums;

use BenSampo\Enum\Enum;
use Illuminate\Support\Facades\Lang;

final class SocialMedia extends Enum
{
    /**
     * Red social facebook
     */
    public const FACEBOOK   = 1;

    /**
     * Red social twitter
     */
    public const TWITTER    = 2;

    /**
     * Red social linkedin
     */
    public const LINKEDIN   = 3;

    /**
     * Red social whatsapp
     */
    public const WHATSAPP   = 4;

    /**
     * Proporciona casting de la enumeración al tipo string
     * obteniendo la descripción de la red social
     *
     * @return string       El nombre de la red social
     */
    public function __toString():string
    {
        switch ($this->value) {
            case self::FACEBOOK:
                return Lang::get('Facebook');
            case self::TWITTER:
                return Lang::get('Twitter');
            case self::LINKEDIN:
                return Lang::get('Linkedin');
            case self::WHATSAPP:
                return Lang::get('Whatsapp');
        }
    }
}
