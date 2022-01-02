<?php

/**
 * Modelo de usuario invitado
 *
 * El usuario invitado es aquel que no encuentra registrado en el sistema
 * Por tanto, no posee cuenta en el sistema
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Auth;

use App\Events\UserCreated;

/**
 * Excepciones requeridas
 */
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Guest extends Model
{
    /**
     * Obtiene el usuario invitado
     *
     * El usuario invitado no está registrado en el sistema y opera con un "token de usuario invitado"
     * que permite identificarlo de forma unívoca
     *
     * @return User |null                       Un usuario o null
     *
     * @example
     *
     * Cuando tenemos un usuario autenticado, podemos obtenerlo con:
     *
     * $user = Auth::user();
     *
     * @link https://laravel.com/docs/8.x/authentication#retrieving-the-authenticated-user
     *
     * Si el usuario no está autenticado, se obtiene null.
     *
     * El usuario no autenticado, se denomina usuario invitado. Puede comprobarse que tenemos
     * un usuario invitado con:
     *
     * if (Auth::guest()) {
     *     // Es un usuario invitado
     * }
     *
     * Para cada usuario invitado creamos una cuenta con un "token de usuario invitado" o
     * "guest_token", que se utiliza para identificarlo de forma unívoca
     *
     * Para obtener el usuario invitado:
     *
     * $user = Guest::user();
     *
     * Si el usuario estuviese registrado, se obtendría igualmente los datos del usuario registrado.
     *
     * Alternativamente a este método directo, es más elocuente:
     *
     * $user = Auth::user() ?? Guest::user();
     *
     * Para obtener el usuario registrado o, si no se autenticado, el usuario invitado.
     *
     */
    public static function user(): ?User
    {
        // Si es un usuario invitado
        if (Auth::guest()) {
            //
            // Obtiene el token del usuario invitado
            //
            // Los usuarios invitados se identifican a través de la cookie de sesión
            // Puede ser null, si todavía no se ha registrado la sesión
            //
            $guestToken = Cookie::get(config('session.cookie'));

            if (!$guestToken) {
                // Si aún no hay una sesión creada
                return null;
            }

            // Obtenemos el usuario
            try {
                $user = User::findByGuestToken($guestToken);
            } catch (ModelNotFoundException $e) {
                // Si no hay usuario con el token dado, debe crearse un nuevo usuario
                // desde la factoria de usuarios. Esto proporciona el nombre "Invitado"
                // y una dirección de correo ficticia que el usuario debe modificar el acceder
                // al dashboard

                // Aquí está pasando que ocasinalmente se genera un error
                // 1062 Duplicate entry 'xxxxxxx@example.com' for key 'users.users_email_unique'
                // porque se genera un usuario con correo que ya ha sido insertado en la base de datos,
                // en este caso se toma el usuario ya creado
                $user = User::factory()->make();
                $newUser = true;                    // Si se ha creado un nuevo usuario

                $userInDatabase = User::where('email', $user->email)->first();
                if ($userInDatabase) {
                    $user = $userInDatabase;
                    $newUser = false;
                }

                // Cambia el nombre del usuario invitado y el idioma
                $user->name   = Lang::get('Usuario sin Registro');
                $user->locale = app()->getLocale();

                // Fija el token del usuario invitado
                $user->guest_token = $guestToken;

                $user->save();

                // Se lanza el evento solo cuando se crea el usuario nuevo, en ocasiones
                // se toma un usuario ya creado anteriormente
                if ($newUser) {
                    event(new UserCreated($user));
                }
            }

            // Devuelve el usuario invitado
            return $user;
        } else {
            // Devuelve el usuario registrado
            return Auth::user();
        }
    }
}
