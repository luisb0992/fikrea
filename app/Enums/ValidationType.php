<?php

/**
 * Enumeración de los tipos de validación de un documento
 *
 * Las validaciones son cada una de las acciones que debe realizar un usuario para aprobar un documento
 * Cada uno de los tipos de validación sontempladas se contemplan aquí
 *
 * @author javieru  <javi@gestoy.com>
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Enums;

use BenSampo\Enum\Enum;
use Illuminate\Support\Facades\Lang;

final class ValidationType extends Enum
{
    /**
     * Validación mediante firma manuscrita digital
     */
    public const HAND_WRITTEN_SIGNATURE         =  1;

    /**
     * Validación adjuntando un archivo de audio (grabación de audio)
     */
    public const AUDIO_FILE_VERIFICATION        = 2;

    /**
     * Validación adjuntando un archivo de video (grabación de video)
     */
    public const VIDEO_FILE_VERIFICATION        = 3;

    /**
     * Validación adjuntando el pasaporte, documento o cédula de identidad,
     * carné o licencia de conducir y similares
     */
    public const PASSPORT_VERIFICATION          = 4;

    /**
     * Validación por captura de video de la pantalla
     */
    public const SCREEN_CAPTURE_VERIFICATION    = 5;

    /**
     * Validación mediante solicitud de un documento
     */
    public const DOCUMENT_REQUEST_VERIFICATION  = 6;

    /**
     * Validación mediante un formulario de datos establecido por el usuario
     * tanto particulares como puede ser empresariales
     */
    public const FORM_DATA_VERIFICATION         = 7;

    /**
     * Validación mediante edición de cajas de texto
     */
    public const TEXT_BOX_VERIFICATION         = 8;

    /**
     * Proporciona casting de la enumeración al tipo string
     * obteniendo la descripción del tipo de validación
     *
     * @return string                           El tipo de validación
     */
    public function __toString():string
    {
        switch ($this->value) {
            case self::HAND_WRITTEN_SIGNATURE:
                return Lang::get('Firma Manuscrita');
            case self::AUDIO_FILE_VERIFICATION:
                return Lang::get('Audio');
            case self::VIDEO_FILE_VERIFICATION:
                return Lang::get('Video');
            case self::PASSPORT_VERIFICATION:
                return Lang::get('Documento Identificativo');
            case self::FORM_DATA_VERIFICATION:
                return Lang::get('Verificación de Datos');
            case self::SCREEN_CAPTURE_VERIFICATION:
                return Lang::get('Captura de Pantalla');
            case self::DOCUMENT_REQUEST_VERIFICATION:
                return Lang::get('Solicitud de Documento');
            case self::TEXT_BOX_VERIFICATION:
                return Lang::get('Editor de Documento');
        }
    }
}
