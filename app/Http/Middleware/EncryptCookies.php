<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array
     */
    protected $except =
        [
            // Cookie cuando el usuario invitado ha procedido ha cambiar su perfil
            'user-has-changed-profile',
        ];
}
