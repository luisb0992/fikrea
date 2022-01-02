<?php

/**
 * Evento UserCreated
 *
 * Evento cuando un nuevo usuario es creado, bien a través de su registro, bien desde una sesión de usuario invitado
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class UserCreated
{
    use SerializesModels;

    /**
     * El usuario autenticado
     *
     * @var Authenticatable
     */
    public $user;

    /**
     * El constructor
     *
     * @param Authenticatable $user
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}
