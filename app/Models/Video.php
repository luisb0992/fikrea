<?php

/**
 * Modelo de grabación de Video
 *
 * Representa una grabación de video que valida un documento
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

class Video extends Media
{
    /**
     * El nombre de la tabla que almacena las grabaciones de video
     *
     * @var string
     */
    protected $table = 'videos';
}
