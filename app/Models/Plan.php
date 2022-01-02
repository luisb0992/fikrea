<?php

/**
 * Modelo de Plan de precios
 *
 * Representa un plan
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{

    /**
     * Subscripción Gratuita (prueba)
     *
     * Se crea para cualquier usuaro registrado o invitado no cliente
     */
    public const TRIAL      = 0;

    /**
     * Subscripción Premium
     *
     * Orientada al cliente medio
     */
    public const PREMIUM    = 1;

    /**
     * Subscripción Enterprise
     *
     * Orientada al cliente superior
     */
    public const ENTERPRISE = 2;

    /**
     * Subscripción FIKREA
     *
     * Orientada a los admins de Fikrea
     */
    public const FIKREA = 3;

    /**
     * Atributos completables
     *
     * @var array
     */
    protected $fillable =
    [
        'id',                           // El id del plan
        'name',                         // El nombre del plan
        'disk_space',                   // El espacio en disco en MB
        'signers',                      // El número de firmantes por documento
        'monthly_price',                // El precio mensual
        'yearly_price',                 // El precio anual
        'change_price',                 // El precio por cambio al plan inmediatemente superior
        'trial_period',                 // El periodo de prueba en días
    ];

    /**
     * No hay marcas de tiempo
     *
     * @var bool
     */
    public $timestamps = false;


    /**
     * Obtiene el descuento que se aplica en el pago anual de un plan frente
     * al correspondiente pago mensual
     *
     * @return float                            El descuento
     */
    public function getAnnualPaymentDiscountAttribute(): float
    {
        return $this->monthly_price * 12 - $this->yearly_price;
    }

    /**
     * Obtiene si el plan es una prueba gratuita o no
     *
     * @return bool                             true si el plan es una prueba gratuita
     *                                          false si el plan es de pago
     */
    public function isTrial(): bool
    {
        return $this->id == self::TRIAL;
    }
}
