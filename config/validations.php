<?php

/**
 * Configuración de validaciones adicionales de un documento
 *
 * Opciones de configuración para la validación alternativa de un documento,
 * mediante un archivo de audio, video, etc.
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

use App\Enums\ValidationType;

return [

    /*
    |--------------------------------------------------------------------------
    | Configuración de la validación mediante un archivo de audio
    |--------------------------------------------------------------------------
    |
    | Configuración de la validación mediante una grabación de audio
    |
    */
    'audio'     =>
    [
        /*
            |--------------------------------------------------------------------------
            | La carpeta que almacena los archivos de audio
            |--------------------------------------------------------------------------
            |
            | La carpeta que contiene los archivos de audio
            |
            */
        'folder'      => 'validations/audio',

        /*
            |--------------------------------------------------------------------------
            | El texto de referencia para la grabación de audio
            |--------------------------------------------------------------------------
            |
            | Una guión de ayuda para la locución
            |
            */
        'text'       =>
        'Hola, mi nombre completo es ____________________________ (Diga su nombre y apellidos) y mi número de identificación es el ____________________ (Diga el número de su DNI-NIE-….) teniendo fecha de expedición el ____ (Diga la fecha de expedición que tenga su documento). En la actualidad resido en ___________ (Diga el domicilio completo donde reside actualmente, CALLE + CODIGO POSTAL + CIUDAD - PROVINCIA). Realizo esta grabación a petición de  ________ (nombre de la persona o empresa que le ha solicitado este proceso) y en la que me reconozco mayor de edad y con pleno poder para aceptar este proceso que realizo en la aplicación FIKREA.COM, donde será almacenada y tratada por el usuario que lanza dicho proceso, por el que he accedido mediante token. Dicha grabación quedara como prueba irrefutable de las condiciones pactadas entre ambas partes.',

        /*
            |--------------------------------------------------------------------------
            | El máximo tiempo de grabación de audio
            |--------------------------------------------------------------------------
            |
            | El tiempo máximo en segundos de la grabación de audio
            |
            */
        'recordtime'   => 60,

        'file' =>
        [
            /*
                    |--------------------------------------------------------------------------
                    | La extensión de los archivos de audio
                    |--------------------------------------------------------------------------
                    |
                    | El extensión de los archivos con las grabaciones de audio guardadados
                    |
                    */
            'extension' => 'wav',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de la validación mediante un archivo de video
    |--------------------------------------------------------------------------
    |
    | Validación mediante una grabación de video
    |
    */
    'video'     =>
    [
        /*
            |--------------------------------------------------------------------------
            | La carpeta que almacena los archivos de video
            |--------------------------------------------------------------------------
            |
            | La carpeta que contiene los archivos de video
            |
            */
        'folder'      => 'validations/video',

        /*
            |--------------------------------------------------------------------------
            | El texto de referencia para la grabación de video
            |--------------------------------------------------------------------------
            |
            | Un guión de ayuda para la grabación del video
            |
            */
        'text'       =>
        'Hola, mi nombre completo es ____________________________ (Diga su nombre y apellidos) y mi número de identificación es el ____________________ (Diga el número de su DNI-NIE-….) teniendo fecha de expedición el ____ (Diga la fecha de expedición que tenga su documento). En la actualidad resido en ___________ (Diga el domicilio completo donde reside actualmente, CALLE + CODIGO POSTAL + CIUDAD - PROVINCIA). Realizo esta grabación a petición de  ________ (nombre de la persona o empresa que le ha solicitado este proceso) y en la que me reconozco mayor de edad y con pleno poder para aceptar este proceso que realizo en la aplicación FIKREA.COM, donde será almacenada y tratada por el usuario que lanza dicho proceso, por el que he accedido mediante token. Dicha grabación quedara como prueba irrefutable de las condiciones pactadas entre ambas partes.',

        /*
            |--------------------------------------------------------------------------
            | El máximo tiempo de grabación de video
            |--------------------------------------------------------------------------
            |
            | El tiempo máximo en segundos de la grabación de video
            |
            */
        'recordtime'   => 60,

        'file' =>
        [
            /*
                    |--------------------------------------------------------------------------
                    | La extensión de los archivos de video
                    |--------------------------------------------------------------------------
                    |
                    | El extensión de los archivos con las grabaciones de video guardadados
                    |
                    */
            'extension' => 'mp4',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Configuración de la validación mediante una captura de pantalla
    |--------------------------------------------------------------------------
    |
    | Validación mediante una grabación de video
    |
    */
    'capture'     =>
    [
        /*
            |--------------------------------------------------------------------------
            | La carpeta que almacenan las capturas de pantalla
            |--------------------------------------------------------------------------
            |
            | La carpeta que contiene los archivos de captura de pantalla
            |
            */
        'folder'      => 'validations/capture',

        /*
            |--------------------------------------------------------------------------
            | El máximo tiempo de grabación de video
            |--------------------------------------------------------------------------
            |
            | El tiempo máximo en segundos de la grabación de video
            |
            */
        'recordtime'   => 120,

        'file' =>
        [
            /*
                    |--------------------------------------------------------------------------
                    | La extensión de los archivos de captura de pantalla
                    |--------------------------------------------------------------------------
                    |
                    | El extensión de los archivos con las captura de pantalla guardadadas
                    |
                    */
            'extension' => 'mp4',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de la validación mediante documentos identificativos
    |--------------------------------------------------------------------------
    |
    | Son documentos que identifican a una persona, como el documento nacional de
    | identidad o DNI (ES) o el pasaporte
    |
    */
    'identification-document' =>
    [
        /*
            |--------------------------------------------------------------------------
            | La carpeta donde se guardan los documentos identificativos
            | y la imagen frontal del usuario para realizar el análisis de coincidencia
            | facial
            |--------------------------------------------------------------------------
            |
            | La carpeta que contiene los documentos identificativos como el pasaporte
            |
            */
        'folder'    => 'validations/identification-document',

        /*
            |--------------------------------------------------------------------------
            | Si se usan técnicas de reconocimiento facil mediante redes neuronales
            | en el proceso de validación de documentos identificativos
            |--------------------------------------------------------------------------
            |
            | Si se usa reconocimiento facial o no en el proceso
            |
            */
        'useFacialRecognition'    => true,

    ],
    /*
    |--------------------------------------------------------------------------
    | Configuración de las solicitudes de documentos
    |--------------------------------------------------------------------------
    |
    | Son documentos que han sido solicitados a los usuarios
    |
    */
    'request-document' =>
    [
        /*
            |--------------------------------------------------------------------------
            | La carpeta donde se guardan los documentos solicitados
            |--------------------------------------------------------------------------
            |
            | La carpeta que contiene los documentos solicitados a los usuarios
            |
            */
        'folder'    => 'requests',

        /*
            |--------------------------------------------------------------------------
            | Los tamaños máximos de los archivos requeridos en una solicitud
            |--------------------------------------------------------------------------
            | Los tamaños están dados en bytes
            */
        'sizes'     =>
        [
            1024 * 1024,                //  1 MB
            1024 * 1024 * 10,           // 10 MB
            1024 * 1024 * 25,           // 25 MB
        ],
    ],

    /* 
    |------------------------------------------------------
    | Configuracion de formulario de validacion de datos
    |------------------------------------------------------
    |
    | Validacion de formulario de datos para certificar datos personales
    | o datos especificos de un receptor o persona
    |
    */
    'form-validations'  =>
    [
        'character-types' =>
        [
            'string',       // solo cadenas de texto (letras)
            'numeric',      // solo numeros
            'special',      // solo texto y caracteres especiales (letras y caracteres)
        ],
    ],

    /*
    |----------------------------------------------------------
    | Enumeracion para los tipos de validacion en un documento
    |----------------------------------------------------------
    |
    | Acceder a la valdiacion mediante el helper config()
    / de una forma mas facil y practica
    |
    */
    'document-validations' =>
    [
        'handWrittenSignature'  => ValidationType::HAND_WRITTEN_SIGNATURE,
        'audio'                 => ValidationType::AUDIO_FILE_VERIFICATION,
        'video'                 => ValidationType::VIDEO_FILE_VERIFICATION,
        'passport'              => ValidationType::PASSPORT_VERIFICATION,
        'screenCapture'         => ValidationType::SCREEN_CAPTURE_VERIFICATION,
        'documentRequest'       => ValidationType::DOCUMENT_REQUEST_VERIFICATION,
        'dataForm'              => ValidationType::FORM_DATA_VERIFICATION,
        'textBox'               => ValidationType::TEXT_BOX_VERIFICATION,
    ],

    /*
    |----------------------------------------------------------
    | Validaciones independients de un documento
    |----------------------------------------------------------
    |
    | Acceder a la valdiacion mediante el helper config()
    | Estas validaciones son las que pueden formar parte de un proceso de firma
    | de documento y a su vez son independiente del mismo
    |
    | => Verificacion de datos
    | => Solicitud de documentos
    |
    */
    'independent-validations' =>
    [
        'dataCertification' => 'dataCertification',         // Certificacion de datos
        'documentRequest'   => 'documentRequest',           // Solicitud de documentos
    ],
];
