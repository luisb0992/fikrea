<?php

/**
 * Configuración de Solicitudes de documentos
 *
 * Opciones de configuración para las solicitudes de documentos
 *
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

return [

	/*
	    |--------------------------------------------------------------------------
	    | Configuración para el fake signer que se crea cuando generamos URL para 
	    | una solicitud de documentos
	    |--------------------------------------------------------------------------
	    |
	    | Los archivos de video grabados sin guardar
	    |
		*/
    
    'user'      =>
        [

            /*
	            |--------------------------------------------------------------------------
			    | Configuración para el fake signer que se crea cuando generamos URL para 
			    | una solicitud de documentos
			    |--------------------------------------------------------------------------
			    |
			    | El nombre del usuario
			    |
				*/
            'name' => 'Usuario',

           	/*
	            |--------------------------------------------------------------------------
			    | Configuración para el fake signer que se crea cuando generamos URL para 
			    | una solicitud de documentos
			    |--------------------------------------------------------------------------
			    |
			    | El apellido del usuario
			    |
				*/
            'lastname' => 'Invitado',

            /*
	            |--------------------------------------------------------------------------
			    | Configuración para el fake signer que se crea cuando generamos URL para 
			    | una solicitud de documentos
			    |--------------------------------------------------------------------------
			    |
			    | El correo del usuario
			    |
				*/
            'email' => 'guest-request@fikrea.com',

             
        ],
];
