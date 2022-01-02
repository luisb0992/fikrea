<?php

/**
 * Configuración de Documentos
 *
 * Opciones de configuración para la subida y procesamiento de los documentos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

return [
    
    'folder'      =>
        [

            /*
            |--------------------------------------------------------------------------
            | La carpeta que almacena los documentos originales
            |--------------------------------------------------------------------------
            |
            | Los documentos sin convertir
            |
            */
            'original' => 'documents/original',

            /*
            |--------------------------------------------------------------------------
            | La carpeta que almacena los documentos convertidos
            |--------------------------------------------------------------------------
            |
            | Los documentos convetidos a PDF para su procamiento
            |
            */
            'converted' => 'documents/converted',

            /*
            |--------------------------------------------------------------------------
            | La carpeta que almacena los documentos firmados
            |--------------------------------------------------------------------------
            |
            | Los documentos convetidos a PDF para su procamiento
            |
            */
            'signed'    => 'documents/signed',

            /*
            |--------------------------------------------------------------------------
            | La carpeta que almacena las imágenes
            |--------------------------------------------------------------------------
            |
            | Cada imagen es una página del documento
            |
            */
            'images' => 'documents/images',
        ],
    
    /*
    |--------------------------------------------------------------------------
    | El número de archivos por página que se muestran en los listados
    |--------------------------------------------------------------------------
    |
    | Un número de archivos por página
    | Ejemplo: 25
    */
    'pagination'  => 25,
        
    'max'        =>
        [
            /*
            |--------------------------------------------------------------------------
            | El tamaño máximo admitido para los archivo subidos
            |--------------------------------------------------------------------------
            |
            | El valor elegido dependerá de la capacidad de almacenamiento
            | y de las directivas post_max_size y upload_max_filesize configurados
            | en su sistema
            |
            | @see https://www.php.net/manual/es/ini.core.php
            |
            */
            'size' => 40 * 1024, // 40 MB

            /*
            |--------------------------------------------------------------------------
            | El número de páginas que se pueden llegar a procesar
            |--------------------------------------------------------------------------
            |
            | El valor elegido dependerá de la cantidad de memoria instalada en el sistema
            |
            */
            'pages' => 60,
        ],
    /*
    |--------------------------------------------------------------------------
    | Conversión del documento en imágenes
    | Cada página es una imagen
    |--------------------------------------------------------------------------
    |
    | Necesaria para la conversión de los archivos a PDF
    */
    'images'    =>
        [
            /*
            |--------------------------------------------------------------------------
            | El formato de los archivos de imagen generados
            |--------------------------------------------------------------------------
            |
            | Normalmente formato JPG
            |
            */
            'format'    => 'jpg',

            /*
            |--------------------------------------------------------------------------
            | La resolución de las imágenes generadas en píxeles por pulgada
            |--------------------------------------------------------------------------
            |
            | La resolución horizontal y vertical, que en general, deben ser iguales
            |
            */
            'resolution' =>
                [
                    'width'     => 300,
                    'height'    => 300,
                ],

            'composite'  =>
                [
                    /*
                    |--------------------------------------------------------------------------
                    | El método utilizado para la composición de imágenes
                    |--------------------------------------------------------------------------
                    |
                    | El método utilzado para fusionas dos imágenes
                    */
                    'method'  => \Imagick::COMPOSITE_OVER,
                ],

            /*
            |--------------------------------------------------------------------------
            | Filtro utilizado para el redimensionamiento de las imágenes
            |--------------------------------------------------------------------------
            |
            | El filtro que debe usar Imagick para redimensionar las imágenes
            |
            | @see https://www.php.net/manual/es/imagick.constants.php#imagick.constants.filters
            */
            'filter'     =>  \Imagick::FILTER_CUBIC,

            /*
            |--------------------------------------------------------------------------
            | El factor de borrosidad cuando se redimensionan las imágenes
            |--------------------------------------------------------------------------
            |
            | El factor de borrosidad es mayor que 1 para borroso,
            | y menor que 1 para nítido
            |
            | @see https://www.php.net/manual/es/imagick.resizeimage.php
            */
            'blur'     =>  1,
        ],


        /*
        |--------------------------------------------------------------------------
        | Configuración de la firma
        |--------------------------------------------------------------------------
        |
        | La configuración para la firma del documento
        |
        */
        'sign'     =>
            [
                /*
                |--------------------------------------------------------------------------
                | El desplazamiento de la firma
                |--------------------------------------------------------------------------
                |
                | Las coordenadas se refieren a le esquina superior izquierda del
                | cuadro de firma, pero la firma se realiza a cierta distancia de esa
                | esquina de la ventana que es preciso corregir
                |
                | Se define aquí el desplazamiento de la firma con respecto a esa esquina
                | de la ventana de firma
                |
                | El cuadro de firma se establece en P y la firma empieza en S
                | Se debe definir ese desplazamiento relativo entre los puntos P y S
                |
                | P(xo,yo)
                | x-------------------------+
                | | S(x1,y1)                |
                | | x---------------------+ |
                | | |       Firma         | |
                | | +---------------------+ |
                | +-------------------------+
                |
                | offsetX = x1 - xo
                | offsetY = y1 - yo
                |
                */
                'offset'    =>
                    [
                        'x'     => 10,
                        'y'     => 36,
                    ],
                /*
                |--------------------------------------------------------------------------
                | El código identificador único de cada firma
                |--------------------------------------------------------------------------
                |
                | A cada firma se asigna un identificador único que aparece junto a la misma
                | Se indica aquí el formato de ese identificador cuando se representa sobre el documento
                |
                */
                'code'      =>
                    [
                        'size'          => '45',            /** Tamaño de la fuente en píxeles */
                        'color'         => 'white',         /** Color de texto                 */
                        'background'    => 'blue',          /** Color de fondo                 */
                    ],
            ],

    /*
    |--------------------------------------------------------------------------
    | La ruta de la librería DomPDF
    |--------------------------------------------------------------------------
    |
    | Necesaria para la conversión de los archivos a PDF
    */
    'dompdf'    => '/vendor/dompdf/dompdf',

    /*
    |--------------------------------------------------------------------------
    | La ruta de la librería mPDF
    |--------------------------------------------------------------------------
    |
    | Necesaria para la conversión de los archivos a PDF
    */
    'mpdf'      => '/vendor/mpdf/mpdf',

];
