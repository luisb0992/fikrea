<?php

/**
 * Modelo de grabación de Audio
 *
 * Representa una grabación de audio que valida un documento
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

class Audio extends Media
{
    /**
     * El nombre de la tabla que almacena las grabaciones de audio
     *
     * @var string
     */
    protected $table = 'audios';
}
