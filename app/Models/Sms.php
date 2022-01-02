<?php

/**
 * Modelo de Sms
 *
 * Representa un mensaje sms
 * que se envía a un firmante que no tiene email
 *
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Sms extends Model
{
    /**
     * Nombre de la tabla
     *
     * @var string
     */
    public $table = "smses";

    /**
     * Uso de columnas de tiempo created_at y updated_at
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Atributos del Sms
     *
     * @var array
     */
    protected $fillable =
        [
            'signer_id',        // El firmante
            'text',             // El texto
            'sended_at',        // Momento en que se envía
        ];

    /**
     * Obtiene el firmante
     *
     * @return BelongsTo                        El firmante
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Signer::class);
    }

    /**
     * Devuelve el modelo padre sendable (Signer o DocumentSharingContact)
     *
     * @return MorphTo                        Modelo padre
     */
    public function sendable() : MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Devuelve la cantidad de sms facturados en el api
     * segun el texto enviado en el mensaje
     *
     * @return int                        Cantidad de sms o partes
     */
    public function getPiecesAttribute() : int
    {
        $pieces = 1;
        while ($pieces * 160 < strlen($this->text)) {
            $pieces ++;
        }
        return $pieces;
    }
}
