<?php

/**
 * Listener para crear una subscripción de prueba gratuita
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Listeners\Subscription;

use Carbon\Carbon;

use App\Events\UserCreated;
use App\Models\Plan;

class CreateTrialSubscription
{
    /**
     * Crea la subscripción de prueba gratuita
     *
     * @param UserCreated $event                El evento de creación del nuevo usuario
     */
    public function handle(UserCreated $event):void
    {
        // Obtiene el plan gratuito
        $trialPlan = Plan::find(Plan::TRIAL);

        // Obtiene el usuario creado
        $user = $event->user;

        // Crea la subscripción al plan gratuito
        // Con duración desde la fecha actual hasta la fecha definida por el periodo de prueba
        $user->subscription()->create(
            [
                'plan'      => $trialPlan,
                'starts_at' => Carbon::now(),
                'ends_at'   => Carbon::now()->addDays($trialPlan->trial_period)
            ]
        );
    }
}
