/*
 * CookieConsent v1.2
 * https://www.github.com/orestbida/cookieconsent
 * Author Orest Bida
 * Released under the MIT License
*/
(function(){

	// obtain cookieconsent plugin
	var cc = initCookieConsent();

	// run plugin with config object
	cc.run({
		cc_autorun : true, 								// show as soon as possible (without the need to manually call CookieConsent.show() )
		cc_delay : 0,								    // specify initial delay after website has loaded		
		cc_enable_verbose : true,						// if enabled, prints all info/error msgs (not available on dist version)
		cc_current_lang : 'es',		
		cc_policy_url : null,                           // specify your own dedicated cookie policy page url
		cc_auto_language : true,						// if enabled, overrides cc_current_lang
		cc_cookie_expiration : 	365,    				// [NEW FROM version 1.2]
		cc_autoclear_cookies : true,					// [NEW FROM version 1.2]
		cc_autoload_css : true, 						// [NEW FROM version 1.2]
		cc_theme_css : '/assets/css/landing/vendor/cookieconsent.css',	
		cc_accept_callback : function(cookies){
			console.info(`Se han aceptado las cookies siguientes: `, cookies);
			
			//Example: if functionality cookies are enabled do something ...
			if(cc.inArray(cookies.level, 'functionality_cookies')){
				// js code here
			}
		},

		cc_languages : [
			{
				lang : 'es',
				modal : {
					cc_title       : 'Usamos cookies',
					cc_more_text   : 'Configuración', 
					cc_accept_text : 'Entendido',
					cc_description : 'Utilizamos cookies propias y de terceros para personalizar el contenido y analizar el tráfico web',
				},
				policy : {
					ccp_title : 'Configuración de Cookies',
					ccp_blocks : [
						{
							ccb_title : '¿Qué son las Cookies?',
							ccb_description: 'Las cookies son archivos de texto muy pequeños que se almacenan en su computadora cuando visita un sitio web. Utilizamos cookies para asegurar las funcionalidades básicas del sitio web y para mejorar su experiencia en línea. A continuación puede configurar los diferentes tipos de cookies que desee que utilicemos.'
						},{
							ccb_title : 'Cookies estrictamente necesarias',
							ccb_description: 'Estas cookies son esenciales para el correcto funcionamiento de mi sitio web. Sin estas cookies, el sitio web no funcionaría correctamente.',
							ccb_switch : {
								value : 'necessary_cookies',
								enabled : true,
								readonly: true
							}
						},{
							ccb_title : 'Cookies funcionales',
							ccb_description: 'Estas cookies se utilizan para brindarle una experiencia más personalizada en mi sitio web y para recordar las elecciones que realiza cuando navega por el sitio web. Por ejemplo, si habilitó o no el modo oscuro en este sitio web.',
							ccb_switch : {
								value : 'functionality_cookies',
								enabled : true,
								readonly: false
							},
						},{
							ccb_title : 'Más Información',
							ccb_description: 'Puede consultar nuestra política de cookies',
						}
					],
					ccp_save_text : 'Guardar Preferencias',
				}
			}
		]
    });
})();