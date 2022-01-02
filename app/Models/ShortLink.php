<?php

/**
 * Modelo de ShortLink
 *
 * Representa un link reducido
 *
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortLink extends Model
{
	/**
     * Atributos del Sms
     *
     * @var array
     */
    protected $fillable =
        [
            'code',        		// El código único de la url
            'link',             // El url que representa
        ];

    /**
     * Obtiene la llave para el model binding
     *
     * @return string
     */
    public function getRouteKeyName() : string
    {
        return 'code';
    }
}
