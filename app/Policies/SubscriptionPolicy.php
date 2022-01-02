<?php

/**
 * SubscriptionPolicy
 *
 * Política de acceso a las subscripciones
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\Subscription;

class SubscriptionPolicy
{
    use HandlesAuthorization;

    /**
     * El constructor
     */
    public function __construct()
    {
        //
    }

    /**
     * Determina la política para poder modificar una subscripción
     *
     * @param User $user                        El usuario
     * @param Subscription $subscription        La subscripción
     *
     * @return bool
     */
    public function edit(User $user, Subscription $subscription): bool
    {
        // Solo los administradores pueden cambiar una subscripción
        return $user && $user->isAdmin();
    }

    /**
     * Determina la política para poder guardar una subscripción
     *
     * @param User $user                        El usuario
     * @param Subscription $subscription        La subscripción
     *
     * @return bool
     */
    public function save(User $user, Subscription $subscription): bool
    {
        // Solo los administradores pueden guardar una subscripción
        return $user && $user->isAdmin();
    }
}
