<?php

/**
 * Configuración OCR
 *
 * Configuración del reconocimiento óptico de carácteres utilizando Tesseract
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

return
    [
        /*
        |--------------------------------------------------------------------------
        | Configuración de idiomas para el OCR
        |--------------------------------------------------------------------------
        |
        | Contiene los idiomas para el reconocimiento óptico de caráctes
        | Los archivos con los datos de entrenamiento para cada idioma deben estar
        | descargados y disponibles en:
        |
        | /usr/share/tesseract-ocr/4.00/tessdata
        |
        | El archivo en español es:
        |
        | spa.traineddata
        |
        | La siguiente lista contiene la relación entre los prefijos de idioma
        | que utilizan estos datos de entrenamiento y el código de idioma ISO-639-1
        | que usa la aplicación para identificar cada uno de los idiomas dispoibles
        |
        | @link https://github.com/tesseract-ocr/langdata
        |
        */
        'lang' =>
            [
                /*
                |--------------------------------------------------------------------------
                | Idiomas utilziado por defecto en el reconocimiento óptico OCR
                |--------------------------------------------------------------------------
                | El idioma por defecto
                |
                | Requiere que los datos de entrenamiento para ese idioma esten disponibles en:
                |
                | /usr/share/tesseract-ocr/4.00/tessdata
                |
                | Por ejemplo, para el idioma español, el archivo:
                |
                | /usr/share/tesseract-ocr/4.00/tessdata/spa.traineddata
                |
                | es requerido
                */
                'default' => 'es',

                /*
                |--------------------------------------------------------------------------
                | Idiomas admitidos para el reconocimiento óptico de carácteres (OCR)
                |--------------------------------------------------------------------------
                |
                | Contiene los idiomas para el reconocimiento óptico de carácteres
                | Los archivos con los datos de entrenamiento para cada idioma deben estar
                | descargados y disponibles en:
                |
                | /usr/share/tesseract-ocr/4.00/tessdata
                |
                | El archivo en español es:
                |
                | spa.traineddata
                |
                | La siguiente lista contiene la relación entre los prefijos de idioma
                | que utilizan estos datos de entrenamiento y el código de idioma ISO-639-1
                | que usa la aplicación para identificar cada uno de los idiomas dispoibles
                |
                | @link https://github.com/tesseract-ocr/langdata
                |
                */
                'list' =>
                    [
                        'ar'    => 'ara',
                        'ca'    => 'cat',
                        'cn'    => 'chi_sim',
                        'de'    => 'deu',
                        'en'    => 'eng',
                        'es'    => 'spa',
                        'eu'    => 'eus',
                        'fr'    => 'fra',
                        'gl'    => 'glg',
                        'it'    => 'ita',
                        'pt'    => 'por',
                        'ru'    => 'rus',
                    ],
             ],
        'max'   =>
            [
                /*
                |--------------------------------------------------------------------------
                | El tamaño máximo para el archivo a procesar
                |--------------------------------------------------------------------------
                | El tamaño máximo del archivo en MB
                */
                'size'  => 10,

                /*
                |--------------------------------------------------------------------------
                | El tamaño máximo en páginas del documento (PDF) a procesar
                |--------------------------------------------------------------------------
                | El número de páginas documento
                */
                'pages' => 5,
            ]
    ];
