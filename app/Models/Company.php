<?php

/**
 * Modelo de Empresa
 *
 * Es usado para la generación de la facturas, es decir, para asociar un usuario a una compañía
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Company extends Model
{
    /**
     * Lista de atributos completables
     *
     * @var array
     */
    protected $fillable =
    [
        'user_id',                  // El id del usuario asociado a la cmpañía
        'name',                     // El nombre o razón social de la compañía
        'cif',                      // El código de indentifcación fiscal o CIF
                                    // @link https://es.wikipedia.org/wiki/C%C3%B3digo_de_identificaci%C3%B3n_fiscal
        'address',                  // La dirección postal de la compaía
        'phone',                    // El teléfono de la compañía
        'city',                     // La localidad/ciudad
        'province',                 // La provincia/región/estado
        'country',                  // El país
        'code_postal',              // El codigo postal
        'dial_code',                // Codigo prefijo del pais
        'email',                    // Email alternativo para las notificaciones de factura
    ];

    /**
     * Obtiene el usuario asociado a la compaía
     *
     * @return BelongsTo                        El usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
