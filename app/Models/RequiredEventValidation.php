<?php

/**
 * Modelo de validaciones requeridas para un evento
 *
 * determina las validaciones que debe cumplir una persona antes
 * de proceder al evento
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequiredEventValidation extends Model
{
    /**
     * La tabla asociada al modelo
     *
     * @var string
     */
    protected $table = 'required_event_validations';

    /**
     * Los atributos asociados al modelo.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',             // El evento perteneciente
        'name',                 // nombre
        'lastname',             // apellido
        'dni',                  // identificacion
        'email',                // correo
        'telefono',             // telefono o movil
        'address',              // direccion o localidad
        'postal_code',          // codigo postal
        'photo_facial',         // foto junto al documento identificativo
        'id_facial'             // foto de perfil de la persona

        // la mayoria de los campos son false por defecto
        // excepto: photo_facial, id_facial
    ];

    /**
     * Devuelve el evento perteneciente
     *
     * @return BelongsTo|null           El evento o null si no posee
     */
    public function event(): ?BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
