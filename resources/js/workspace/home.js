/**
 * Paǵina de inicio del workspace del usuario
 * 
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

new Vue({
    el: '#app',
    data:
        {
            privacityPolicy: false,             // Si se marcado como leída la política de privacidad o no
        },
    /**
     * Cuando la instancia está montada
     * 
     * 
     */
    mounted: function () {
        // Muestra la modal de advertencia sobre la política de privacidad
        // si el valor de cookie hide-privacity-policy-modal no se ha establecido a "true"
        if (Cookies.get('hide-privacity-policy-modal') !== 'true') {
            this.$bvModal.show('privacity-policy');
        }

    },
    methods: {
        /**
         * Cuando se marca/desmarca que la política de privacidad ha sido leída
         * 
         * @param {Event} event
         *
         */
        checkPrivacityPolicy: function (event) {
            // Fija una cookie para que no se vuelva a mostrar la modal de la política de privacidad
            // Duración una hora
            Cookies.set('hide-privacity-policy-modal', 'true', {
                sameSite : 'strict',    // Atributo SameSite
                expires  : 1/24,        // 1 hora
            });
        },

        /**
         * Muestra la modal de cancelación del proceso
         * 
         * @param {Event} event
         *  
         */
        showCancelRequestModal: function (id) {
            this.$bvModal.show('cancel-request-'+id);
        },

        /**
         * Muestra la modal de cancelación para verificación de datos
         */
        showCancelVerificationFormModal: function () {
            this.$bvModal.show('cancel-verificationform');
        },
    },
    computed: {
        /**
         * Si se ha marcado como leída o no la política de privacidad
         * 
         * @return {Boolean}
         */
        privacityPolicyRead: function () {
            return this.privacityPolicy;
        }
    }
});