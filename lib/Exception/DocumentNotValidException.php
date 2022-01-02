<?php

namespace Fikrea\Exception;

/**
 * La clase DocumentNotValidException
 * Se lanza cuando un documento no es válido para su procesamiento por la aplicación
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 */
class DocumentNotValidException extends BaseException
{
    /**
     * El nombre del archivo no válido
     *
     * @var string                              El nombre del archivo
     */
    protected $file;

    /**
     * El constructor
     *
     * @param string $file                      El archivo
     */
    public function __construct(string $file)
    {
        $this->file = $file;

        parent::__construct(
            "El archivo {$file} no puede ser procesado por la aplicación"
        );
    }
}
