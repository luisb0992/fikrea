<?php

/**
 * Modelo de ReasonCancelRequest
 *
 * Representa los motivos por el cual se cancela la solicitud de documento requerido
 *
 * @author Jonathan Sanchez <jonathanch1991@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReasonCancelRequest extends Model
{
    /**
     * Lista de atributos ReasonCancelRequest
     *
     * @var array
     */
    protected $fillable =
    [

        'reason',                     // El nombre del motivo por el cual se cancela la solicitud del documento

    ];
    /**
     * Obtiene los documentos
     *
     * @return HasMany                una ReasonCancelRequest puede tener muchos documentos solicitados
     */
    public function documentRequests()
    {
        return $this->hasMany('App\Models\DocumentRequest');
    }
}
