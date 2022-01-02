<?php

namespace Fikrea\Interfaces;

interface Sms
{
    /**
     * Envia el SMS
     *
     * @param string $destination                El número de destino, varios destinos separados por comas
     *                                           o un array de destinos
     * @param string $message                    El mensaje a enviar
     *
     * @return bool                              Devuelve true si el envío del SMS es satisfactorio
     *                                           false en caso contrario
     */
    public function send(string $destination, string $message):bool;
}
