<?php

/**
 * Modelo FileSharingHistory
 *
 * Representa un conjunto de archivos seleccionados que se desean compartir con
 * uno o más destinatarios
 *
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipientContact extends Model
{

    /**
     * Atributos del modelo
     *
     * @var array
     */
    protected $fillable =
        [
            'name',         // Nombre del destinatario
            'lastname',     // Apellidos del destinatario
            'email',        // Correo del destinatario
            'phone',        // Teléfono del destinatario
            'dni',          // DNI del destinatario
            'company',      // Compañía del destinatario
            'position',     // Cargo del destinatario
            'token',        // Token del contacto
        ];

    /**
     * Obtiene el modelo asociado al registro
     *
     * @return HasOne                  El modelo
     */
    public function history()
    {
        return $this->morphTo();
    }

    /**
     * Devuelve el nombre completo del contacto
     *
     * @return string               El nombre completo
     */
    public function getFullNameAttribute() : string
    {
        return "{$this->name} {$this->lastname}";
    }
}
