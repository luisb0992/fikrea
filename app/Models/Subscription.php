<?php

/**
 * Modelo de Subscripción
 *
 * Representa una subscripción vinculada a un plan de precios
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Carbon\Carbon;

class Subscription extends Model
{
    /**
     * Atributos completables
     *
     * @var array
     */
    protected $fillable =
    [
        'plan_id',                          // El id del plan de susbcripción
        'months',                           // El número de meses de la subscripción
                                            // En periodo de prueba es 0, en los demás casos, los meses
                                            // contratados
        'starts_at',                        // La fecha de inicio de la subscripción
        'ends_at',                          // La fecha de fin de la subscripción
        'canceled_at',                      // La fecha en la que la subscripción ha sido cancelada
        'payment',                          // El importe de la subcripción
        'payed_at',                         // La fecha del pago
    ];

    /**
     * Conversiones de tipos
     *
     * @var array
     */
    protected $casts =
    [
        'starts_at'     => 'datetime',
        'ends_at'       => 'datetime',
        'canceled_at'   => 'datetime',
        'payed_at'      => 'datetime',
    ];

    /**
     * No hay marcas de tiempo
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Obtiene los días que faltan para que la subscripción finalice
     *
     * @return int                              El número de días restantes
     *                                          cero, si la subscripción a finalizado
     */
    public function getRemainingDaysAttribute(): int
    {
        // El número de días de diferencia entre la fecha de finalización de la subscripción y
        // la fecha actual.
        // El segundo parámetro (false) permite obtener un valor positivo o negativo para esa diferencia
        // @see https://carbon.nesbot.com/docs/#api-difference
        //
        $remainingDays = Carbon::parse(Carbon::now())->diffInDays($this->ends_at, false) + 1;

        return $remainingDays < 0 ? 0 : $remainingDays;
    }

    /**
     * Obtiene el estado de la subscripción en porcentaje
     * Vale 0% si se acaba de iniciar la subscripción, 100% si ha finalizado
     *
     * @return int                              El estado de la subscripción
     */
    public function getPercentageStatusAttribute(): int
    {
        // Obtiene el número de días entre el inicio y el final de la subscripción
        $subscriptionDays = Carbon::parse(Carbon::parse($this->starts_at))->diffInDays($this->ends_at, false);

        $subscriptionStatus = intval(($subscriptionDays - $this->remainingDays) * 100 / $subscriptionDays);

        return $subscriptionStatus > 100 ? 100 : $subscriptionStatus;
    }

    /**
     * Obtiene el usuario de la subscripción
     * El usuario que tiene subscripción se llama cliente
     *
     * @return HasOne                           Cada subscripción viene asociada a un usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene el plan de la subscripción
     *
     * @return HasOne                           Cada subscripción viene asociada a un plan
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Comprueba si la subscripción actual es la subscripción de prueba
     *
     * @return bool                             true si la subscripción es de prueba (trial)
     *                                          false en caso contrario
     */
    public function onTrial(): bool
    {
        return $this->plan == Plan::TRIAL;
    }

    /**
     * Comprueba si la subscripción actual está activa o no
     * Es decir, no ha terminado y no ha sido cancelada
     *
     * @return boolean
     */
    public function active(): bool
    {
        return $this->remainingDays > 0 && !$this->canceled_at;
    }

    /**
     * Comprueba si la subscripción actual ha terminado o no
     *
     * @return bool                             true si la subscripción ha terminado
     *                                          false en caso contrario
     */
    public function ended(): bool
    {
        return $this->remainingDays <= 0 && !$this->canceled_at;
    }

    /**
     * Comprueba si la subscripción actual se ha cancelado o no
     *
     * @return bool                             true si la subscripción se ha cancelado
     *                                          false en caso contrario
     */
    public function canceled(): bool
    {
        return $this->canceled_at == null;
    }

    /**
     * Renueva la subscripción
     *
     * @param Order $order                      El pedido de renovación de la subscripción
     *
     * @return bool                             true si la renovación se realiza con éxito
     *                                          false en caso contrario
     */
    public function renew(Order $order): bool
    {
        $this->plan_id   = $order->plan->id;
        $this->months    = $order->months;

        // Si la fecha de fin de la susbcripción anterior es superior a la fecha actual
        if ($this->ends_at > new \DateTime) {
            // Se incrementa la fecha fin en el número de meses indicados
            $this->ends_at = $this->ends_at->modify("+ {$order->months} months");
        } else {
            // En caso contrario se cuenta desde la fecha actual
            $this->ends_at = (new \DateTime)->modify("+ {$order->months} months");
        }

        $this->payment         = $order->amount;
        $this->payed_at        = new \DateTime;

        return $this->save();
    }
}
