<?php

/**
 * Modelo de Pedido
 *
 * Representa un pedido vinculado a la renovación de una subscripción
 *
 *
 * Hay tres tipos de situaciones posibles que generan tres tipos de pedidos:
 *
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * | Plan Inicial  | Plan Nuevo  | Tipo | Observaciones                                              |
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * | 0   (Free)    | 1 (Premium) | 0    | Paso de subscripción gratuita a Subscripción de pago       |
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * | 0   (Free)    | 2 (Pro)     | 0    | Paso de subscripción gratuita a Subscripción de pago       |
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * | 1   (Premium) | 1 (Premium) | 0    | Mantenimiento de la subscripción actual                    |
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * | 2   (Pro)     | 2 (Pro)     | 0    | Mantenimiento de la subscripción actual                    |
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * | 1   (Premium) | 2 (Pro)     | +1   | Ampliación a la subscripción Pro                           |
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 * | 2   (Pro)     | 1 (Premium) | -1   | Degradar a la subscripción Premium                         |
 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Order extends Model
{
    /**
     * La subscripción mantiene su plan
     */
    public const TYPE_PLAN_MAINTAIN   = 0;

    /**
     * La subscripción amplia su plan
     */
    public const TYPE_PLAN_UPGRADE    = +1;

    /**
     * La subscripción se devalua a un plan inferior
     */
    public const TYPE_PLAN_DOWNGRADE  = -1;

    /**
     * Atributos completables
     *
     * @var array
     */
    protected $fillable =
    [
        'approved',                         // Si el pago ha sido aprobado por la paltaforma de pago o no
        'payed',                            // Si el pedido ha sido pagado o no
        'type',                             // El tipo de pedido aplicable
        'order',                            // El número de pedido
        'token',                            // El token de la transacción
        'subscription_id',                  // El id de la subscripción
        'plan_id',                          // El id del plan de subscripción
        // Se compone de una fecha-hora más el id del usuario
        'months',                           // El número de meses contratados
        'change_months',                    // El número de meses a lo que se aplica un cambio de subscripción
        'price',                            // El precio unitario
        'units',                            // El número de unidades adquiridas
        // meses para susbcripción mensual, años para la anual
        'amountTaxExcluded',                // El importe sin impuestos
        'tax',                              // Los impuestoa aplicables
        'aditional_amount',                 // El importe adicional originado por un cambio de subscripción
        'amount',                           // El importe total con impuestos
        'payed_at',                         // Fecha en la que se ha realizado el pago
    ];

    /**
     * Conversiones de tipo
     *
     * @var array
     */
    public $casts =
    [
        'approved'  => 'boolean',
        'payed'     => 'boolean',
        'payed_at'  => 'datetime',
    ];

    /**
     * No hay marcas de tiempo
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Obtiene el usuario
     *
     * @return HasOne                           Cada pedido se asocia a un usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene la subscripción
     *
     * @return HasOne                           Cada pedido se asocia a una subscripción
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
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
     * Aprueba un pedido, una orden de pago
     *
     * @return bool                             true si la orden de pago ha sido aprobada con éxito
     *                                          false en caso contrario
     */
    public function approve(): bool
    {
        $this->approved = true;

        return $this->save();
    }

    /**
     * Si el pedido ha sido aprobado por la plataforma de pago
     *
     * @return bool                             true si el pedido ha sido aprobado por la plataforma de pago
     */
    public function isApproved(): bool
    {
        return $this->approved;
    }

    /**
     * Si el pedido ha sido aprobado por la plataforma de pago
     *
     * @return bool                             true si el pedido no ha sido aprobado por la plataforma de pago
     */
    public function isNotApproved(): bool
    {
        return !$this->approved;
    }

    /**
     * Realiza el pago del pedido
     *
     * @return bool                             true si la orden de pago ha sido pagada con éxito
     *                                          false en caso contrario
     */
    public function pay(): bool
    {
        $this->payed    = true;
        $this->payed_at = new \DateTime;

        return $this->save();
    }

    /**
     * Si el pedido está pagado
     *
     * @return bool                             true si el pedido está pagado
     */
    public function isPayed(): bool
    {
        return $this->payed;
    }

    /**
     * Si el pedido no está pagado
     *
     * @return bool                             true si el pedido no está pagado
     */
    public function isNotPayed(): bool
    {
        return !$this->payed;
    }

    /**
     * Obtiene los pedidos que han sido pagados
     *
     * @return Builder                          Una Lista pedidos o facturas pagadas
     */
    public static function payed(): Builder
    {
        return Order::where('payed', true);
    }
}
