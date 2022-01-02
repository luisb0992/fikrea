<?php

/**
 * Modelo de Contacto
 *
 * Los contactos son guardados por el usuario de la aplicación a modo de agenda,
 * para poder recuperar su información más adelante
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    /**
     * Lista de atributos completables
     *
     * @var array
     */
    protected $fillable =
    [
        'name',                     // El nombre del contacto
        'lastname',                 // Los apellidos del contacto
                                    // De los siguientes, uno de los dos es obligatorio
        'email',                    // La dirección de correo electrónico del contacto
        'phone',                    // El teléfono del contacto
        'dni',                      // El número de documento indentificativo (DNI)
                                    // @link https://es.wikipedia.org/wiki/DNI_(Espa%C3%B1a)
        'company',                  // La compañía
        'position',                 // El cargo o puesto dentro de la compañía
    ];

    /**
     * Devuelve el nombre completo del contacto
     *
     * @return string                           El nombre completo del contacto si ha sido definido
     *                                          o la diercción de correo o el teléfono en caso contrario
     */
    public function getFullNameAttribute() : string
    {
        $fullname = trim("{$this->name} {$this->lastname}");

        return $fullname ?? $this->email ?? $this->phone;
    }

    /**
     * Obtiene un contacto por su dirección de correo o teléfono
     *
     * @param string $emailOrPhone              La dirección de correo o teléfono
     *
     * @return self                             Un contacto
     * @throws ModelNotFoundException           El contacto no existe
     */
    public static function findByEmailOrPhone(string $emailOrPhone): self
    {
        return self::where('email', $emailOrPhone)
                   ->orWhere('phone', $emailOrPhone)
                   ->firstOrFail();
    }
}
