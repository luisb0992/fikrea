<?php

/**
 * Modelo de firma
 *
 * Representa una firma en un documento
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sign extends Model
{
    /**
     * Atributos completables
     *
     * @var array
     */
    protected $fillable =
    [
        'signer_id',        // El id del firmante

        'signer',           // El firmante. Puede ser: Apellido y nombre, Dirección de Correo o Teléfono
        // en este orden de prioridad según los datos que se hayan proporcionado
        // para el firmante. El email o el teléfono, uno de los dos, son los únicos
        // atributos obligatorios
        'creator',          // Si es el creador/autor del documento
        'page',             // La paǵina
        'x',                // La posición x de la firma dentro de la página
        'y',                // La posición y de la firma dentro de la página
        'sign',             // La firma
        'code',             // Un id único para cada firma
        'signed',           // Si la firma ha sido realizada o no
        'signDate',         // La fecha de la firma
        'ip',               // La dirección ip desde la que se ha firmado
        'user_agent',       // Agente de usuario utilizado para firmar
        'latitude',         // La latitud en el momento de la firma,  datum WGS84
        'longitud',         // La longitud en el momento de la firma, datum WGS84
        'device',           // Dispositivo que ha usado el firmante
    ];

    /**
     * Conversiones de tipos
     *
     * @var array
     */
    protected $casts =
    [
        'signed'    => 'boolean',
        'creator'   => 'boolean',
        'signDate'  => 'datetime',
    ];

    /**
     * No hay marcas de tiempo
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Obtiene el documento de la firma
     *
     * @return BelongsTo                        El documento relacionado
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
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
     * Obtiene la firma de código identificador único dado
     *
     * @param string $code
     * @return Sign
     */
    public static function findByCode(string $code): Sign
    {
        return Sign::where('code', '=', $code)->first();
    }
}
