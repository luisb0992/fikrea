/**
 * Estado de validación del documento
 * 
 * @author javieru <javi@gestoy.co>
 * @copyright 2021 Retail Servicios Externos
 */

const { default: Axios } = require("axios");

new Vue({
    el: '#app',
    data: {
                                            // Si la lista de documentos esta vacía o no
        noDocuments        : document.querySelectorAll('#documents tbody tr').length == 1,
        documentWasSharing : false,         // Si el documento se ha vuelto a compartir o no
                                            // Es posible reenviar la solicitud de firma a los usuarios firmantes que aún no
                                            // han realizado las validaciones propuestas sobre el documento
    },
    methods: {
        /**
         * Al descargar el archivo firmado
         * Puede demorar tiempo
         * 
         */
        downloadSigned: function (event) {

            // Muestra una notificación indicando que el archivo firmado está siendo generado
            const message = event.currentTarget.dataset.message;

            toastr.info(message);
        },
        /**
         * Envía el documento a los firmantes que aún no ha efectuado la validación
         * No se admite más de un envío diario
         *
         * @param {Event} event
         */
        sendDocument: function (event) {

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
    }
});