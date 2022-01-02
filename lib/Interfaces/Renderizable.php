<?php

namespace Fikrea\Interfaces;

/**
 * Interface Renderizable
 *
 * Renderiza una página utilizando un motor de vistas a implementar
 *
 * @copyright 2021 Retail Servicios Externos SL
 * @author javieru <javi@gestoy.com>
 */
interface Renderizable
{
    /**
     * Renderiza una página utilizando una plantilla
     *
     * @param array  $param                         Los parámetros que debe recibir la vista
     *                                              para realizar las oprtundas sustituciones
     * @return mixed
     */
    public function render(array $param = []);
}
