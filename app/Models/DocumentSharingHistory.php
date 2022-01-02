<?php

/**
 * Modelo DocumentSharingHistory
 *
 * Representa una visita sobre un conjunto de documentos compartidos
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Fikrea\GeoIp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentSharingHistory extends Model
{
    /**
     * La tbla asociada al modelo
     *
     * @var string
     */
    protected $table = 'document_sharing_histories';

    /**
     * Atributos del modelo
     *
     * @var array
     */
    protected $fillable = [
        'document_sharing_id',              // Id de la compartición del documento
        'document_sharing_contact_id',      // El id del contacto con el que se ha compartido
        'user_id',                          // Id del usuario que comparte el documento
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
    protected $casts = [
        'starts_at'     => 'datetime',
        'downloaded_at' => 'datetime',
    ];


    /**
     * Obtiene la posición desde la que se ha realizado la visita al documento compartido
     *
     * @return GeoIp                            Los detalles de la posición
     */
    public function getPositionAttribute(): GeoIp
    {
        return new GeoIp($this->ip);
    }

    /**
     * Obtiene el usuario que comparte el documento
     *
     * @return BelongsTo                     El usuario perteciente
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene la compartición de documento
     *
     * @return HasOne                           La compartición de documento
     */
    public function documentSharing(): BelongsTo
    {
        return $this->belongsTo(DocumentSharing::class);
    }

    /**
     * Obtiene el contaco
     *
     * @return HasOne                           El contacto con el que se ha compartido el documento
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(DocumentSharingContact::class, 'document_sharing_contact_id');
    }
}
