<?php

/**
 * Correo de confirmación de renovación de la subscripción
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

/**
 * Modelos Requeridos
 */
use App\Models\Order;

class EmailSubscriptionRenewSuccess extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * El pedido
     *
     * @var Order
     */
    public Order $order;
    
    /**
     * El constructor
     *
     * @param Order   $pedido                   El pedido
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Construye el mensaje
     *
     * @return self                             El propio objeto
     */
    public function build():self
    {
        $this->subject(
            Lang::get(
                ':app. Gracias por renovar su subscripción.',
                [
                    'app'   => config('app.name'),
                ]
            )
        );

        return $this->view(
            'dashboard.mail.subscription-renew-success',
            [
                'order'    => $this->order,
            ]
        );
    }
}
