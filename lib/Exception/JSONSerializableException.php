<?php

namespace Fikrea\Exception;

/**
 * La interface de JSONSerializableException
 * Permite serializar las excepciones a JSON
 *
 * La principal utilidad es la posibilidad de poder gestionar errores en el cliente producidos en
 * llamadas a través de AJAX
 *
 * @package exception
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 *
 */
interface JSONSerializableException
{
    /**
     * Serializa una excepción a JSON
     *
     * @return string                        Una cadena con la excepción serializada(JSON)
     */
    public function toJSON():string;
}
