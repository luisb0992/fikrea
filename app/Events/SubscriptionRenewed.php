<?php

/**
 * Evento SubscriptionRenewed
 *
 * Evento cuando una subscripción ha sido renovada
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Events;

use Illuminate\Queue\SerializesModels;

use App\Models\Order;

class SubscriptionRenewed
{
    use SerializesModels;

    /**
     * El usuario autenticado
     *
     * @var Authenticatable
     */
    public $user;

    /**
     * El pedido de renovación de la subscipción
     *
     * @var Order
     */
    public Order $order;

    /**
     * El constructor
     *
     * @param Authentiable $user                El usuario autenticado
     * @param Order $order                      El pedido de renovación de la subscripción
     */
    public function __construct($user, Order $order)
    {
        $this->user  = $user;
        $this->order = $order;
    }
}
