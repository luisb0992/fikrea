<?php

namespace Fikrea\Exception;

/**
 * La clase DocumentTooBigException
 * Se lanza cuando un documento tiene demasiadas páginas para ser procesado
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 */
class DocumentTooBigException extends BaseException
{
    /**
     * El constructor
     *
     * @param string $file                      El archivo
     */
    public function __construct(string $file)
    {
        parent::__construct(
            "El archivo {$file} es demasiado grande para ser procesado"
        );
    }
}
