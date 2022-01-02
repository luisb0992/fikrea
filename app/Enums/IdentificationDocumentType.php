<?php

/**
 * Enumeración con los tipos de documentos de identificación
 * con los cuales un usuario puede realizar una validación
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Enums;

use BenSampo\Enum\Enum;

final class IdentificationDocumentType extends Enum
{
    /**
     * Otros documentos
     */
    public const ANOTHER        = 0;

    /**
     * Documento Nacional de Identidad (DNI) (ES)
     */
    public const DNI            = 1;

    /**
     * Documento de identidad de extrenjero (NIE) (ES)
     */
    public const NIE            = 2;

    /**
     * Pasaporte
     */
    public const PASSPORT       = 3;

    /**
     * Permiso de conducción
     */
    public const DRIVER_LICENSE = 4;
}
