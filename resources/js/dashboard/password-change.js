/**
 * Cambia la contraseña del usuario
 * 
 * @author javieru <javi@gestoy.co>
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
        password             : null,                        // La contraseña
        password_confirmation: null,                        // La confirmación de contraseña
        passwordHidden       : true,                        // Mostrar/Ocultar la contraseña
        passwordFieldType    : 'password',
    },
    methods: {
        /**
         * Botón mostrar/ocultar la contraseña
         *
         */
        tooglePassword : function () {
            this.passwordHidden = !this.passwordHidden;
            this.passwordFieldType = this.passwordHidden ? 'password' : 'text';
        },
    },
    computed: {
        /**
         * Habilita/Deshabilita el botón de cambiar la contraseña
         * 
         * @param {Event} event 
         */
        isPasswordChangeDisabled: function (event) {
            return !this.password || !this.password_confirmation || this.password != this.password_confirmation; 
        }
    }
 });