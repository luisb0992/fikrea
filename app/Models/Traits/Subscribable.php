<?php

/**
 * Trait para un modelo subscribible
 *
 * Se aplica al usuario que posee una susbcripción
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Models\Subscription;

trait Subscribable
{
    /**
     * Obtiene la subscripción de un usuario
     *
     * @return HasOne                           Un usuario puede tener una única subscripción
     */
    public function subscription():HasOne
    {
        return $this->hasOne(Subscription::class);
    }
}
