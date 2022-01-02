<?php

/**
 * Modelo de Envíos realizados de una verificación de datos (Compartición)
 *
 * Cada vez que se realiza un envío de una verificación de datos,
 * se crea una compartición de solicitud
 *
 * @author LuisBarDev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class VerificationFormSharing extends Model
{
    /**
     * Atributos del modelo
     *
     * @var array
     */
    protected $fillable = [
        'verification_form_id',     // llave que relacion la comparticion con la verificación
        'sent_at',                  // Momento en el que se ha realizado el envío de la verificación a los firmantes
        'type',                     // El tipo de envio
        'signers',                  // Una lista de destinatarios/firmantes a los cuales se ha enviado la verificación
    ];

    /**
     * Las conversiones de tipos
     *
     * @var array
     */
    protected $casts = [
        'sent_at'       => 'datetime',
        'visited_at'    => 'datetime',
    ];

    /**
     * La verificación de datos
     *
     * @return BelongsTo                        La verificación de datos
     *                                          relacionada con el envío o compartición
     */
    public function verificationForm(): BelongsTo
    {
        return $this->belongsTo(VerificationForm::class);
    }

    /**
     * Obtiene los "firmantes" de la verificación de datos
     *
     * @return Collection                       Una colección de "firmantes" de la
     *                                          verificación de datos
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
