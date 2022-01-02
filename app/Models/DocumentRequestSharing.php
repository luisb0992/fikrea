<?php

/**
 * Modelo de Envíos realizados de una solicitud de documentos (Compartición)
 *
 * Cada vez que se realiza un envío de una solicitud de documentos,
 * se crea una compartición de solicitud
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class DocumentRequestSharing extends Model
{
    /**
     * Atributos del modelo
     *
     * @var array
     */
    protected $fillable =
        [
            'sent_at',              // Momento en el que se ha realizado el envío del documento a los firmantes
            'signers',              // Una lista de destinatarios/firmantes a los cuales se ha enviado el documento
            'type',                 // El tipo de compartición
        ];

    /**
     * Las conversiones de tipos
     *
     * @var array
     */
    protected $casts =
        [
            'sent_at'       => 'datetime',
            'visited_at'    => 'datetime',
        ];

    /**
     * No hay marcas de tiempo
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * El documento
     *
     * @return BelongsTo                        La solicitud de documentos relacionada con el envío o compartición
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(DocumentRequest::class);
    }

    /**
     * Obtiene los firmantes de la solicitud de documentos
     *
     * @return Collection                       Una colección de firmantes de la solicitud
     */
    public function getSignersAttribute(): Collection
    {
        $signers = json_decode($this->attributes['signers'], true);

        return collect(array_map(fn ($signer) => Signer::find($signer), $signers['signers']));
    }


    /**
     * Marca la compartición como visitada
     *
     * @return void
     */
    public function visited(): void
    {
        $this->visited_at = now();
        $this->save();
    }
}
