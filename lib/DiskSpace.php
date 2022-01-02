<?php

/**
 * La Clase DiskSpace
 *
 * Representa el espacio en disco ocupado por todos los archivos de un usuario
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 *
 */

namespace Fikrea;

class DiskSpace extends AppObject
{
    /**
     * El espacio total disponible en base a la subscripción realizada
     *
     * @var int
     */
    public int $available;

    /**
     * El espacio total ocupado
     *
     * @var int
     */
    public int $used;

    /**
     * El espacio ocupado por los archivo
     *
     * @var int
     */
    public int $usedByFiles;

    /**
     * El espacio ocupado por los documentos
     *
     * @var int
     */
    public int $usedByDocuments;

    /**
     * El espacio ocupado por los archivos subidos por los firmantes
     *
     * @var int
     */
    public int $usedByUploads;

    /**
     * El espacio libre
     *
     * @var int
     */
    public int $free;

    /**
     * El constructor
     *
     * @param array                             Una lista de valores con el espacio de disoo ocupado:
     *                                              available       : (int) El espacio disponible en la subscripción
     *                                              used            : (int) El espacio usado
     *                                              usedByFiles     : (int) El espacio usado por los archivos
     *                                              usedByDocuments : (int) El espacio usado por los documentos
     *                                              usedByUploads   : (int) El espacio usado por los documentos subidos
     *                                              free            : (int) El espacio libre
     * No se debe asumir que available = used + free, pues el espacio available puede cambiar al hacerlo la
     * subscripción y puede ocurrir que used > available
     */
    public function __contruct(array $diskSpace)
    {
        parent::__construct($diskSpace);
    }

    /**
     * El porcentaje de espacio de disco usado
     *
     * @return int                              El espacio usado en porcentaje
     */
    public function getUsedPercentage():int
    {
        $used = intval($this->used * 100 / $this->available);
        return $used > 100 ? 100 : $used;
    }

    /**
     * El porcentaje de espacio de disco libre
     *
     * @return int                              El espacio usado en porcentaje
     */
    public function getFreePercentage():int
    {
        return intval($this->free * 100 / $this->available);
    }
}
