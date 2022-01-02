/**
 * Sesión del usuario
 * 
 * Permite la recuperación de una sesión de usuario invitado anterior
 * 
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

new Vue({
    el: '#app',
    data: {
        email       : null,                     // La dirección de correo del usuario                   
    },
    /**
     * Cuando la instancia está montada
     * 
     *
     */
    mounted: function () {
        setTimeout(() => { document.getElementById('email').focus(); }, 500);
    },
    computed:  {
        /**
         * Comprueba si la dirección de correo proporcionada es válida o no
         * 
         * @return {Boolean}
         */
        emailIsNotValid: function () {

            // Expresión regular para verificar una dirección de correo RFC822/RFC2822
            const email = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    
            return !this.email || !email.test(this.email);
        }
    }
});
