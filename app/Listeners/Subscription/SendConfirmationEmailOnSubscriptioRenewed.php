<?php

/**
 * Listener cuando una subsripicón ha sido renovada
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Listeners\Subscription;

use App\Events\SubscriptionRenewed;

use App\Http\Controllers\EmailController;

class SendConfirmationEmailOnSubscriptioRenewed
{
    /**
     * Envía el correo de confirmación de la renovación de la subscripción
     *
     * @param SubscriptionRenewed $event        El evento de renovación de la subscripción
     */
    public function handle(SubscriptionRenewed $event):void
    {
        EmailController::subscriptionRenewSuccessEmail($event->user, $event->order);
    }
}
