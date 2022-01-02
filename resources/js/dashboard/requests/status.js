/**
 * Muestra estado de una Solicitud de Documentos
 * 
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

const { default: Axios } = require("axios");

new Vue({
    el: '#app',
    data: {
        requestWasSharing : false,         // Si la solicitud se ha vuelto a compartir o no
                                           // Es posible reenviar la solicitud de documentos a los usuarios firmantes que aún no
                                           // han rcompletado la referida solicitud
    },
    /**
     * Cuando la instancia está montada
     */
    mounted: function () {
  
        // Obtiene el idioma de la página
        const lang = document.querySelector('html').getAttribute('lang');
    },
    methods: {
        /**
         * Envía la solicitud de documentos a los firmantes que aún no ha efectuado la validación
         * No se admite más de un envío diario
         *
         * @param {Event} event
         */
        sendRequest: function (event) {

            const $this = this;

            const sendDocumentRequest = event.target.dataset.sendDocumentRequest; 

            Axios.post(sendDocumentRequest)
                .then(() => {
                    // El documento ha sido compartido con los usuarios firmantes que aún no ha realizado los
                    // procesos de validación propuestos por el autor/creador del documento
                    $this.documentWasSharing = true;
                    $this.$bvModal.show('document-sended-success');
                })
                .catch(error => {
                    console.error(error);
                });
        },   

    },
});
