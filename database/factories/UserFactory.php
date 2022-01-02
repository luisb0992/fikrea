<?php

/**
 * Factoria de Usuarios
 *
 * Crea un usuario que utilizamos en la aplicación como invitado
 * para poder operar dentro de la aplicación sin registro previo
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */


namespace Database\Factories;

use App\Models\User;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * El nombre del modelo de usuario
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define el modelo de usuario
     *
     * @return array
     */
    public function definition()
    {
        return
            [
                'name'              => $this->faker->name,                      // Define un nombre
                'email'             => $this->faker->unique()->safeEmail,       // Define una dirección de correo segura
                'email_verified_at' => null,                                    // El usuario no está verificado
                                                                                // Define una contraseña inicial
                'password'          => '$2y$04$QlOdUD7LSqr49DvYINxP8.b0X.BOqvQ9jrSdITimBP9.lLRuH78h.',
            ];
    }
}
