/**
 * Registro de usuarios
 * 
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

/**
 * Muestra la fortaleza de la contraseña
 * 
 * @link https://github.com/miladd3/vue-simple-password-meter
 */
import passwordMeter from 'vue-simple-password-meter';

new Vue({
    el: '#app',
    components: { passwordMeter },
    data: {
        name                    : document.getElementById('name').value,                    // El nombre
        lastname                : document.getElementById('lastname').value,                // Los apellidos
        email                   : document.getElementById('email').value,                   // La dirección de Correo
        password                : document.getElementById('password').value,                // La contraseña
        password_confirmation   : document.getElementById('password_confirmation').value,   // La confirmación de contraseña
        acceptUseConditions     : false,                                                    // Aceptación de las condiciones de uso
        passwordHidden          : true,                                                     // Ocultar/Mostrar la contraseña
        type                    : 'password',
    },
    /**
     * Cuando la instancia está montada
     *
     */
    mounted: function() {
        // Si se ha modificado el perfil por defecto del usuario 
        // (lo que se detecta leyendo la cookie "user-has-changed-profile")
        // se completa el nombre, apellidos y la dirección de correo con la información del perfil 
        if (Cookies.get('user-has-changed-profile') == 'true') {
            // Coloca el nombre y la dirección del correo
            this.name      = document.getElementById('name').dataset.name;     // El nombre guardado en el perfil
            this.lastname  = document.getElementById('lastname').dataset.name; // Los apellidos guardados en el perfil
            this.email     = document.getElementById('email').dataset.email;   // La dirección de correo guardada en el perfil
            
            // Fija la entrada en el campo contraseña
            setTimeout(() => { document.getElementById('password').focus(); }, 500);    
        } else  {
            // En caso contrario se invita al usuario a empezar introduciendo su nombre
            // Fija la entrada en el campo nombre
            setTimeout(() => { document.getElementById('name').focus(); }, 500);
        }
    },
    methods: {
        /**
         * Botón mostrar/ocultar la contraseña
         *
         */
        tooglePassword : function () {
            this.passwordHidden = !this.passwordHidden;
            this.type = this.passwordHidden ? 'password' : 'text';
        },
        /**
         * Cuando se pulsa el botón registrar
         * 
         * @param {Event} event
         *  
         */
        register: function(event) {
            // La cookie que indica que el perfil del usuario ha sido mofificada se fija a false
            // En el proceso de registro se va a terminar la sesión
            Cookies.set('user-has-changed-profile', 'false');

            // Envías el formulario
            document.getElementById('form').submit();
        }
    },
    computed: {
        /**
         * Habilita/Deshabilita el botón para continuar el registro
         * en función de si se han aceptado o no las condiciones de uso y si las contraseña coinciden
         * 
         * @return {Boolean}
         *
         */
        isDisabled: function() {
            return !this.acceptUseConditions || this.password != this.password_confirmation;
        },
        /**
         * Si las contraseña no coinciden
         * 
         * @return {Boolean}
         * 
         */
        passwordNoMismatch: function () {
            return this.password != this.password_confirmation;
        }
    }
});
