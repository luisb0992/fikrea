<?php

return
    [
       /*
        |--------------------------------------------------------------------------
        | Configuración de Google Analytics
        |--------------------------------------------------------------------------
        |
        | El id del código de seguimiento de google analytics
        |
        | @link https://analytics.google.com/analytics/web/
        |
        */
        'analytics' =>
            [
                'site'      => 'https://www.fikrea.com',
                'user'      => 'admin@fikrea.com',
                'tracking'  =>
                    [
                        'code'  => 'UA-186435123-1',
                    ],
            ],

        /*
        |--------------------------------------------------------------------------
        | Configuración de las APIs de Google
        |--------------------------------------------------------------------------
        |
        | Configurar cada una de las APIs de Google utilizadas
        |
        */

        'api' =>
            [
                /*
                |--------------------------------------------------------------------------
                | Configuración de la API de Google Maps
                |--------------------------------------------------------------------------
                |
                | Configuración de la API de Google Maps
                |
                */
                'maps' =>
                    [
                        'url'   => 'https://maps.googleapis.com/maps/api/js',
                        'key'   => 'AIzaSyB29gSufZVYIvENBA0nZ5Jd2exqfX1Sh0U',
                    ]
            ],
    ];
