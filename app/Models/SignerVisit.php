<?php

/**
 * Modelo de SignerVisit
 *
 * Representa una visita de un firmante a uno de sus espacios de trabajo
 * que puede ser el home de su workspace, o cualquiera de las áreas
 * relacionada con esa donde realizara el proceso de validación
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignerVisit extends Model
{
    /**
     * Atributos del modelo
     *
     * @var array
     */
    protected $fillable =
        [
            'request',                          // La url base de la sección visitada
            'document_id',                      // El id del documento
            'signer_id',                        // El id del usuario firmante
            'ip',                               // La ip del usuario firmante
            'user_agent',                       // El agente de usuario
            'latitude',                         // La longitud en el datum WGS84
            'longitude',                        // La longitude en el datum WGS84
            'starts_at',                        // El momento de inicio de la visita
            'ends_at',                          // El momento de finalización de la visita
            'device',                           // Dispositivo que ha usado el firmante
        ];

    /**
     * La conversión de tipos
     *
     * @var array
     */
    protected $casts =
        [
            'starts_at' => 'datetime',
            'ends_at'   => 'datetime',
        ];

    /**
     * Obtiene el documento de la visita
     *
     * @return HasOne                           El documento de la visita
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Obtiene el firmante de la visita
     *
     * @return HasOne                           El usuario firmante que realiza la visita
     */
    public function signer(): BelongsTo
    {
        return $this->belongsTo(Signer::class);
    }

    /**
     * Obtiene la duración de la visita
     *
     * @return int                              La duración de la visita en segundos
     *                                          o null si la duración de la visita es incierta
     */
    public function getDurationAttribute(): ?int
    {
        return $this->starts_at && $this->ends_at ? $this->ends_at->diffInSeconds($this->starts_at) : null;
    }
}
