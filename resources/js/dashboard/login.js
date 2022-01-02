/**
 * Login de usuarios
 * 
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

new Vue({
    el: '#app',
    data: {
        email           : document.getElementById('email').value,
        password        : document.getElementById('password').value,
        passwordHidden  : true,
        type            : 'password',
    },
    /**
     * Al cargar la página
     */
    mounted: () => {
        setTimeout(() => { document.getElementById('email').focus(); }, 500);
    },
    methods: {
        /**
         * Botón mostrar/ocultar la contraseña
         *
         */
        tooglePassword : function () {
            this.passwordHidden = !this.passwordHidden;
            this.type = this.passwordHidden ? 'password' : 'text';
        }
    },
    computed: {
        /**
         * Si el botón de inicio de sesión está deshabilitado o no
         * 
         * @return {Boolean}
         * 
         * 
         */
        isDisabled: function () {
            return !this.email || !this.password;
        }
    }
});