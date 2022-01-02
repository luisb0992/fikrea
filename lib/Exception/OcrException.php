<?php

namespace Fikrea\Exception;

/**
 * La clase OcrException
 * Se lanza cuando un documento no ha podido ser procesado mediante reconocimiento óptico de carácteres (OCR)
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 */
class OcrException extends BaseException
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
