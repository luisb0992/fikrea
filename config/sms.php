<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración del servicio de envío de SMS
    |--------------------------------------------------------------------------
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Configuración para el proveedor Altiria
    |--------------------------------------------------------------------------
    |
    | @link https://www.altiria.com/
    |
    */

    'altiria' =>
        [
            /*
            |--------------------------------------------------------------------------
            | La dirección URL de la API del proveedor
            |--------------------------------------------------------------------------
            |
            | @link https://www.altiria.com/
            |
            */

            'url'       => 'http://www.altiria.net/api/http',

            /*
            |--------------------------------------------------------------------------
            | El dominio
            |--------------------------------------------------------------------------
            |
            | @link https://www.altiria.com/
            |
            */

            'domain'    => 'gestoy',

            /*
            |--------------------------------------------------------------------------
            | La dirección de correo del usuario registrado en Altiria
            |--------------------------------------------------------------------------
            |
            | @link https://www.altiria.com/
            |
            */

            //'login'    => 'david@gestoy.com',
            'login'    => 'mikel@retailexternal.com',

            /*
            |--------------------------------------------------------------------------
            | La contraseña del usuario registrado en Altiria
            |--------------------------------------------------------------------------
            |
            | @link https://www.altiria.com/
            |
            */

            //'password'    => 'gestoy201810pde',
            'password'    => 'Fikrea.2021',

            /*
            |--------------------------------------------------------------------------
            | El identificador del remitente
            |--------------------------------------------------------------------------
            |
            | @link https://www.altiria.com/
            |
            */

            'sender'    => 'GESTOY',

            /*
            |--------------------------------------------------------------------------
            | La dirección URL de la API del proveedor
            |--------------------------------------------------------------------------
            |
            | Si se habilita el debug, se activa un log en un archivo de registro altiria.log
            |
            */

            'debug'    => false,
        ],
];
