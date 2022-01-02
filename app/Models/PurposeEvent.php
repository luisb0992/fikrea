<?php

/**
 * Modelo de propisito del evento
 *
 * Identifica cual es el proposito o finalidad del evento creado
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PurposeEvent extends Model
{
    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'purpose_events';

    /**
     * Los atributos asociados al modelo.
     *
     * @var array
     */
    protected $fillable = [
        'name',                 // Nombre del proposito o finalidad
        'description',          // breve dscripcion del proposito
        'is_active'             // el estadoa actual: true para que se tome en cuenta y false para ser ignorado
    ];

    /**
     * Devuelve los propositos del evento que estan disponibles
     *
     * @return Collection           El objeto eloquent
     */
    public static function isActive() : Collection
    {
        return self::where('is_active', 1)->get();
    }
}
