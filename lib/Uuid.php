<?php

namespace Fikrea;

/**
 * La clase Uuid
 *
 * Manipula identificadores únicos globalmente (GUID)
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 *
 * @example
 *
 * use Fikrea\Guid;
 *
 * $giud = Guid::generate();
 *
 */

class Uuid extends AppObject
{
    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Genera un identificador único globalmente (GUID)
     *
     * Un GUID es generado de la misma manera que el DCE UUID,
     * excepto que la convención de Microsoft encierra un GUID entre llaves
     *
     * @return string                           Un identificador único globalmente
     *                                          o GUID (GUID: Globally Unique Identifier)
     */
    final public static function create(): string
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        // La salida es igual a la proporcionada por la función com_create_guid()
        // de la extensión uuid de PECL
        return sprintf(
            '%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(32768, 49151),
            mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(0, 65535)
        );
    }
}
