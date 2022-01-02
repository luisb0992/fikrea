<?php

/**
 * Enumeración de los tipos de procesos que puede atender un firmante
 *
 * Puede ser:
 * 1 ) Proceso de validaciones
 * 2 ) Proceso de solicitud de documentos
 * 3 ) Proceso de certificación de formulario
 *
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Enums;

use BenSampo\Enum\Enum;

final class SignerProcesType extends Enum
{
    /**
     * Proceso de validaciones
     */
    const VALIDATION_PROCESS =   1;
    
    /**
     * Proceso de solicitud de documentos
     */
    const REQUEST_PROCESS 	 =   2;
    
    /**
     * Proceso de certificación de datos o formularios
     */
    const FORM_PROCESS 		 = 3;

    /**
     * Proporciona casting de la enumeración al tipo string
     * obteniendo la descripción del proceso
     *
     * @return string                           El proceso que atiende
     */
    public function __toString():string
    {
        switch ($this->value) {
            case self::VALIDATION_PROCESS:
                return Lang::get('Proceso de validaciones');
            case self::REQUEST_PROCESS:
                return Lang::get('Proceso de solicitud de documentos');
            case self::FORM_PROCESS:
                return Lang::get('Proceso de certificación de datos');
        }
    }
}
