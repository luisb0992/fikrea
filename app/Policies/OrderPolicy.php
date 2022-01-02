<?php

/**
 * OrderPolicy
 *
 * Política de acceso a los pedidos u órdenes de pago
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Models\User;
use App\Models\Order;

class OrderPolicy
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
     * Determina la política para poder acceder a la factura de un pedido
     *
     * @param User $user                        El usuario
     * @param Order $Order                      El pedido u orden de pago
     *
     * @return bool
     */
    public function bill(User $user, Order $order): bool
    {
        return $user && ($user->id === $order->user->id || $user->isAdmin());
    }
}
