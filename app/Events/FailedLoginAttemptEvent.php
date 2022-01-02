<?php

/**
 * Evento FailedLoginAttemptEvent
 *
 * Evento cuando un usuario escribe mal su contraseña o posible intento de acceso indebido
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Http\Request;

use App\Models\User;

class FailedLoginAttemptEvent
{
    use SerializesModels;

    /**
     * El usuario
     *
     * @var User
     */
    public User $user;

    /**
     * Los datos de la solicitud
     *
     * @var Request
     */
    public Request $request;

    /**
     * El constructor
     *
     * @param User    $user    El usuario
     * @param Request $request La solicitud
     */
    public function __construct(User $user, Request $request)
    {
        $this->user = $user;
        $this->request  = $request;
    }
}
