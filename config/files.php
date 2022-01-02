<?php

/**
 * Configuración de Archivos Subidos
 *
 * Opciones de configuración para la subida de archivos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

return [
    /*
    |--------------------------------------------------------------------------
    | La carpeta donde se almacenan los archivos subidos
    |--------------------------------------------------------------------------
    |
    | La carpeta donde se almacenan todos los archivos subidos
    */
    'folder'      => 'files',

   /*
    |--------------------------------------------------------------------------
    | El número de archivos por página que se muestran en los listados
    |--------------------------------------------------------------------------
    |
    | Un número de archivos por página
    | Ejemplo: 25
    */
    'pagination'  => 10,

    /*
    |--------------------------------------------------------------------------
    | El tamaño máximo de los archivos subidos
    |--------------------------------------------------------------------------
    |
    | El tamaño máximo de los archivos subidos
    |
    | El valor elegido dependerá de la capacidad de almacenamiento
    | y de las directivas post_max_size y upload_max_filesize configurados
    | en su sistema
    |
    | @see https://www.php.net/manual/es/ini.core.php
    */
    'max'        =>
        [
            'size' => 2 * 1024 * 1024 * 1024,  // 2 GB
        ],
];
