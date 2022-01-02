<?php

/**
 * Modelo de captura de Pantalla
 *
 * Representa una captura de pantalla de video que valida un documento
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

class Capture extends Media
{
    /**
     * El nombre de la tabla que almacena las capturas de video
     *
     * @var string
     */
    protected $table = 'captures';
}
