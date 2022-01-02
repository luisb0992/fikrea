<?php

/**
 * Enumeración de los tipos de compartición de documentos
 *
 * Los documentos se pueden compartir de manera manual y de forma automática
 * Las comparticiones manuales las crea el usuario (reenviando la petición),
 * las automáticas son enviadas por el sistema cuando se crean las peticiones o
 * cuando se realizan envíos de recordatorio programados
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Enums;

use BenSampo\Enum\Enum;
use Illuminate\Support\Facades\Lang;

final class DocumentSharingType extends Enum
{
    /**
     * Compartición realizada de forma automática
     * Es el sistema el que ha generado el envío del documento a firmar
     */
    public const AUTO = 0;

    /**
     * Compartición realizada de forma manual
     * Es el propio usuario el que ha enviado (reenviado) el documento a firmar
     */
    public const MANUAL = 1;

    /**
     * Proporciona casting de la enumeración al tipo string
     * obteniendo la descripción del tipo de compartición de documentos
     *
     * @return string                           El tipo de compartición de documento
     */
    public function __toString():string
    {
        switch ($this->value) {
            case self::AUTO:
                return Lang::get('Automático');
            case self::MANUAL:
                return Lang::get('Manual');
        }
    }
}
