<?php

/**
 * Modelo FileSharingHistory
 *
 * Representa una visita sobre un conjunto de archivos compartidos
 *
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Fikrea\GeoIp;

class FileSharingHistory extends Model
{
    /**
     * Atributos del modelo
     *
     * @var array
     */
    protected $fillable =
        [
            'file_sharing_id',                  // Id de la compartición de archivo
            'user_id',                          // Id del usuario que comparte el archivo
            'file_sharing_contact_id',          // El id del contacto con el que se ha compartido
            'ip',                               // La dirección ip
            'user_agent',                       // El agente de usuario
            'starts_at',                        // Inicio de la visita
            'downloaded_at',                    // La fecha de descarga de la visita
        ];

    /**
     * La conversión de tipos
     *
     * @var array
     */
    protected $casts =
        [
            'starts_at'     => 'datetime',
            'downloaded_at' => 'datetime',
        ];


    /**
     * Obtiene la posición desde la que se ha realizado la visita al archivo compartido
     *
     * @return GeoIp                            Los detalles de la posición
     */
    public function getPositionAttribute(): GeoIp
    {
        return new GeoIp($this->ip);
    }

    /**
     * Obtiene la compartición de archivo
     *
     * @return HasOne                           La compartición de archivo
     */
    public function fileSharing(): BelongsTo
    {
        return $this->belongsTo(FileSharing::class);
    }

    /**
     * Obtiene el contaco
     *
     * @return HasOne                           El contacto con el que se ha compartido el archivo
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(FileSharingContact::class, 'file_sharing_contact_id');
    }
}
