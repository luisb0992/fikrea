<?php

/**
 * Configuración de Sellos estampados
 *
 * Opciones de configuración para la estampación de sellos sobre docucmentos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

return [

    /*
    |--------------------------------------------------------------------------
    | La carpeta que almacena la librería de sellos predeterminada
    |--------------------------------------------------------------------------
    | Contiene los archivos de sello en formato PNG
    | Debemos usar PNG porque incluye un canal alpha para la transparencia
    */
    'folder'      => '/assets/images/dashboard/stamps',

    'size'          =>
        [
            /*
            |--------------------------------------------------------------------------
            | El ancho del sello cuando se estampa en un documento
            |--------------------------------------------------------------------------
            | El ancho en píxeles, el alto se determina de forma automática en función
            | de aquel
            */
            'width'     => 180,
        ]
];
