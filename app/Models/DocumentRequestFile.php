<?php

/**
 * Modelo de archivo de documento solicitado
 *
 * Representa un documento subido por un usuario para atender a una solicitud de documentos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentRequestFile extends Model
{
    /**
     * La tabla
     *
     * @var string
     */
    protected $table = 'document_request_files';

    /**
     * Lista de atributos del modelo
     *
     * @var array
     */
    protected $fillable =
        [
            'user_id',                          // El usuario
            'document_request_id',              // La solicitud de documentos
            'required_document_id',             // El documento requerido
            'signer_id',                        // El usuario "firmante" que adjunta el documento
            'name',                             // El nombre del archivo
            'path',                             // La ruta del archivo
            'type',                             // El tipo de archivo
            'size',                             // El tamaño del documento en bytes
            'issued_to',                        // La fecha de expedición del documento
                                                // o null si no se ha determinado
            'expiration_date',                  // La fecha de vencimiento del documento
            'ip',                               // La dirección IP
            'user_agent',                       // El agente de usuario
            'latitude',                         // La latitud
            'longitude',                        // La longitud
            'device',                           // Dispositivo que ha usado el firmante
        ];

    /**
     * Las conversiones de tipo
     *
     * @var array
     */
    protected $casts =
        [
            'issued_to' => 'datetime',
            'expiration_date' => 'date',
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
     * La solicitud de documentos
     *
     * @return BelongsTo                        La solicitud de documentos
     */
    public function documentRequest(): BelongsTo
    {
        return $this->belongsTo(DocumentRequest::class);
    }

    /**
     * El usuario creador/autor de la solicitud
     *
     * @return BelongsTo                        El usuario creador/autor de la solicitud
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * El documento requerido
     *
     * @return BelongsTo                        El documento requerido
     */
    public function requiredDocument(): BelongsTo
    {
        return $this->belongsTo(RequiredDocument::class);
    }

    /**
     * El usuario "firmante" que sube el documento para completar la solicitud de documentos
     *
     * @return HasOne                           El usuario "firmante"
     */
    public function signer(): BelongsTo
    {
        return $this->belongsTo(Signer::class);
    }

    /**
     * Devuelve si el documento tiene fecha de vencimiento y la misma está próxima a vencer
     *
     * @return boolean
     */
    public function isNearToExpire(): bool
    {
        return  now() <= $this->expiration_date && $this->expiration_date->diffInDays(now()) <= 7;
    }
}
