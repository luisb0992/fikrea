<?php

/**
 * Modelo de Documento Requerido
 *
 * Un documento requerido en una solicitud de documentos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RequiredDocument extends Model
{
    public $table = 'required_documents';

    /**
     * Lista de atributos de un documento requerido en una solicitud de documentos
     *
     * @var array
     */
    protected $fillable =
        [
            'document_request_id',              // El id de la solicitud de documentos
            'name',                             // El nombre del documento solicitado
            'comment',                          // El comentario del documento solicitado
            'type',                             // El tipo mime del documento solicitado
                                                // o null sino se esteblece ningún tipo concreto
            'issued_to',                        // La fecha de expedición del dpcumento
                                                // o null para no establecer ninguna fecha de expedición
            'validity',                         // La validez del documento requerido desde su
                                                // fecha de expedición
            'validity_unit',                    // La unidad temporal en la que se expresa el periodo
                                                // de validación, 1 = días, 30 = meses, 365 = años
                                                // o null para no estabelecer periodo de validez alguno
            'maxsize',                          // El tamaño máximo del archivo que se solicita en bytes
                                                // o null si no se establece limitación de tamaño alguno
            'has_expiration_date',              // Si el firmante debe introducir la fecha de vencimiento
            'notify',                           // Si se envian notificaciones cuando el documento este al expirar
        ];

    /**
     * Las conversiones de tipos
     *
     * @return array
     */
    protected $casts =
        [
            'issued_to' =>  'date',
        ];

    /**
     * No hay marcas de tiempo
     *
     * @var bool
     */
    public $timestamps = false;

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
     * @return BelongsTo                        Una solicitud de documentos
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(DocumentRequest::class);
    }

    /**
     * Los documentos aportados, pueden ser muchos en el caso de que
     * el que aportó inicialmente haya expirado y lo renueve
     *
     * @return HasMany              Los archivos que se han aportado
     */
    public function files(): HasMany
    {
        return $this->hasMany(DocumentRequestFile::class);
    }

    /**
     * Último documento aportado que se toma como válido en sus validaciones y demás
     *
     * Los documentos aportados, pueden ser muchos en el caso de que
     * el que aportó inicialmente haya expirado y lo renueve
     *
     * @return DocumentRequestFile|null    El documento aportado que se tiene en uso
     */
    public function file()
    {
        return $this->files->last() ?? null;
    }
}
