<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * Las rutas que deben ser excluidas de verificación por token CSRF
     *
     * Esto debe incluir cualquier conexión que hagan Webhooks de plataformas de pago
     * como Paypal, Stripe, Bots de Telegram, etc, pues no suminisitrarán el token CSRF
     * en sus peticiones (Error HTTP/419 Page Expired)
     *
     * @var array
     */
    protected $except =
        [
            /**
             * Excluir el webhook de conexión de la plataforma de pago Paypal
             */
            '*/subscription/payment/approved',
        ];
}
