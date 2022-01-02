<?php

namespace Fikrea\Exception;

/**
 * La clase FileException
 * Se lanza cuando se produce un error al tratar de acceder a un archivo
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 */
class FileException extends BaseException
{
    /**
     * El constructor
     *
     * @param string $message                   El mensaje
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
