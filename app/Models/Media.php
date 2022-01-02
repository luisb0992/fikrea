<?php

/**
 * Modelo de archivo Multimedia
 *
 * Representa un documento Multimedia, que puede ser una grabación de audio o de video
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    /**
     * Lista de atributos completables
     *
     * @var array
     */
    protected $fillable =
    [
        'user_id',                   // El id del usuario propietario del archivo
        'signer_id',                 // El id del firmante
        'document_id',               // El id del documento
        'path',                      // La ruta del archivo multimedia relativa a su carpeta de almacenamiento
        'type',                      // El tipo mime del archivo
        'size',                      // El tamaño en bytes
        'duration',                  // La duración del archivo multimedia en formato mm:ss
        'ip',                        // La dirección IP desde la que se ha efectuado la grabación
        'user_agent',                // El agente de usuario desde el que se ha hecho la grabación
        'latitude',                  // La latitud desde la que se ha hecho la grabación
        'longitude',                 // La longitud desde la que se ha hecho la grabación
        'device',                    // Dispositivo del usuario
    ];

    /**
     * Retorna el tipo MIME del documento
     *
     * @return BelongsTo
     */
    public function mimeType(): BelongsTo
    {
        return $this->belongsTo(MediaType::class, 'media_type', 'type');
    }

    /**
     * Obtiene el usuario propietario del archivo
     *
     * @return BelongsTo                        El usuario propietario del archivo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene el firmante
     *
     * @return BelongsTo                        El firmante
     */
    public function signer(): BelongsTo
    {
        return $this->belongsTo(Signer::class);
    }

    /**
     * Obtiene el documento
     *
     * @return BelongsTo                        El documento
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
