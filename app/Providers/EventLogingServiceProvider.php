<?php

/**
 * ServiceProvider para los eventos que se producen en el proceso de login y registro
 * de los usuarios de la aplicación
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use App\Events\FailedLoginAttemptEvent;

class EventLogingServiceProvider extends ServiceProvider
{
    /**
     * Mapeo a cada listener
     *
     * @var array
     */
    protected $listen =
        [
            /**
             * Envío de un email para la verificación de la cuenta cuando un usuario se ha registrado
             *
             * Cuando se crea la clase Registered se dispara este evento que crea el email que
             * se envía al usuario para que se registre
             */
            Registered::class => [
                \App\Listeners\Login\SendConfirmationEmailOnRegister::class,
            ],

            /**
             * Envío de un email para el cambio de la contraseña de usuario
             *
             * Cuando se crea la clase PasswordReset se dispara este evento
             */
            PasswordReset::class => [
                \App\Listeners\Login\SendPasswordRecoveryEmail::class,
            ],

            /**
             * Envío de un email cuando un usuario ha introducido mal su clave
             * o alertando de un posible intento por parte de otro usuario
             *
             * Cuando se intenta acceder con credenciales incorrectas se dispara este evento
             */
            FailedLoginAttemptEvent::class => [
                \App\Listeners\Login\SendFailedLoginAttemptEmail::class,
            ],

        ];

    /**
     * Punto de inicio del Service Provider
     *
     * @return void
     */
    public function boot():void
    {
        parent::boot();
    }
}
