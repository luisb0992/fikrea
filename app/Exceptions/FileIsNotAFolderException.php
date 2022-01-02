<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class FileIsNotAFolderException extends Exception
{
    protected $message;

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        // Inicializar el mensaje a mostrar
        $this->message = \Lang::get('Esto no es una carpeta.');
    }
}
