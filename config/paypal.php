<?php

return [
    /*
    |--------------------------------------------------------------------------
    | El modo de Paypal
    |--------------------------------------------------------------------------
    |
    | Sólo puede valer 'sandbox' para el entorno de pruebas
    |                o 'live' para el entorno de producción
    |
    */
    'mode'    => env('PAYPAL_MODE', 'sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Configuración para el entorno de pruebas o sandbox
    |--------------------------------------------------------------------------
    |
    |
    */
    'sandbox' =>
        [
            'client_id'         => env('PAYPAL_SANDBOX_CLIENT_ID', ''),
            'client_secret'     => env('PAYPAL_SANDBOX_CLIENT_SECRET', ''),
        ],

    /*
    |--------------------------------------------------------------------------
    | Configuración para el entorno en producción o live
    |--------------------------------------------------------------------------
    |
    |
    */
    'live' => [
        'client_id'         => env('PAYPAL_LIVE_CLIENT_ID', ''),
        'client_secret'     => env('PAYPAL_LIVE_CLIENT_SECRET', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | La moneda a uilizar : EUR, USD
    |--------------------------------------------------------------------------
    |
    */
    'currency'       => env('PAYPAL_CURRENCY', 'EUR'),
    
    /*
    |--------------------------------------------------------------------------
    | Idioma a utilizar en la transacción
    |--------------------------------------------------------------------------
    | Cógigo ISO-639-1 del idioma, por ejemplo: es_ES
    */
    'locale'         => env('PAYPAL_LOCALE', 'es_ES'),
];
