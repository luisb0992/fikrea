<?php

/**
 * Trait Statistical
 *
 * Añade las estadísticas básicas del sitio web a una vista
 * Estos incluye el múmero de usuarios, el número de subscripciones, las facturas,
 * datos de Google Analytics
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers\Traits;

use App\Models\User;
use App\Models\Order;
use App\Models\Sms;

use Fikrea\ModelAndView;

use Analytics;
use Spatie\Analytics\Period;

trait Statistical
{
    /**
     * Añade las estadísticas básicas
     *
     * @param ModelAndView $mav                 Una vista
     *
     * @return array                            Una lista con valores estadísticos de la aplicación
     *                                          como el número de usuarios registrados
     */
    protected function appendStats(ModelAndView $mav):void
    {
        $stats =
            [
                'users'         => User::all()->count(),        // El número de cuentas de usuario totales
                'registered'    => User::registered()->count(), // El número de usuarios registrados
                'clients'       => User::clients()->count(),    // El número de usuarios clientes
                'orders'        => Order::payed()->count(),     // El número de pedidos pagados
                                                                // Usuarios en la última semana
                                                                // @link https://github.com/spatie/laravel-analytics
                'visitors'      => Analytics::fetchVisitorsAndPageViews(Period::days(7)),
                'smses'         => Sms::all()->count(),         // Cantidad de sms enviados
            ];

        $mav->append(
            [
                'stats'     => (object) $stats,                 // La estadísticas se añaden como un objeto stdClass
            ]
        );
    }
}
