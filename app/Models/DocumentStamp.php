<?php

/**
 * Modelo de Sello estampado sobre un documento concreto
 *
 * Tras la elección de un sello entre la lista de sellos disponibles,
 * este modelo representa su situación sobre el documento
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentStamp extends Model
{
    /**
     * Lista de atributos del modelo
     *
     * @var array
     */
    protected $fillable =
        [
            'document_id',              // El id del documento sobre el que se estampa el sello
            'stamp',                    // La imagen del sello que se estampa en el documento
            'page',                     // La página del documento donde se sitúa el sello
            'x',                        // La coordenada x del situación del sello en la paǵina
            'y',                        // La coordenada y del situación del sello en la paǵina
        ];

    /**
     * Obtiene el documento donde se ha estampado el sello
     *
     * @return BelongsTo                        El Documento
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
