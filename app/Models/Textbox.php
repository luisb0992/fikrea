<?php

/**
 * Modelo de Textbox
 *
 * Representa una caja de texto sobre el documento
 * que debe ser cumplimentado
 *
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Textbox extends Model
{
    /**
     * Atributos del documento
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
            'page',             // La página
            'x',                // La posición x de la firma dentro de la página
            'y',                // La posición y de la firma dentro de la página
            'text',             // El texto a cumplimentar
            'options',          // Opciones para tipo de caja de texto select
            'type',             // El tipo de input que se debe mostrar en la caja
                                // 1 - iniciales
                                // 2 - nombre completo
                                // 3 - número de identificación
                                // 4 - texto libre
                                // 5 - casilla de verificación
                                // 6 - lista de opciones
            'code',             // Un id único para cada firma
            'title',            // Título de la caja de texto para el firmante externo
            'signed',           // Si la caja ha sido completada o no
            'signDate',         // La fecha de la firma
            'ip',               // La dirección ip desde la que se ha aportado la info
            'user_agent',       // Agente de usuario utilizado
            'latitude',         // La latitud en el momento de la firma,  datum WGS84
            'longitud',         // La longitud en el momento de la firma, datum WGS84
            'device',           // Dispositivo que ha usado el firmante
            'width',            // Ancho de la caja, se puede modificar
            'height',           // Alto de la caja, NO SE PUEDE MODIFICAR
            'rules',            // Reglas de restricciones o limitaciones de los textos
            'shiftX',           // Posición relativa de la caja de texto con el div que lo contiene según left
            'shiftY',           // Posición relativa de la caja de texto con el div que lo contiene según top
            'fitMaxLength',     // Ajustar la cantidad máxima de caracteres al ancho de la caja de texto
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
     * Obtiene el documento de la caja de texto
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
     * Obtiene la caja de texto por el código identificador único dado
     *
     * @param string $code
     * @return TextBox
     */
    public static function findByCode(string $code): TextBox
    {
        return TextBox::where('code', $code)->first();
    }
}
