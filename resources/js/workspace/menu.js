/**
 * Menú del workspace del usuario
 * 
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

const { default: Axios } = require("axios");

new Vue({
    el: '#menu',
    data:
        {
            // Las rutas de las solicitudes
            request: 
                {
                    cancelProcess   : request.dataset.cancelProcess,
                    processCanceled : request.dataset.processCanceled,
                },
            
            subject: null,                  // El motivo de la cancelación del proceso
        },
    methods: {
        /**
         * Muestra la modal de cancelación del proceso
         * 
         * @param {Event} event
         *  
         */
        showCancelProcessModal: function (event) {
            this.$bvModal.show('cancel-process');
        },

        /**
         * Muestra el modal para enviar un comentario
         */
        showSendCommentModal() {
            this.$bvModal.show('send-comment-modal');
        },

        /**
         * Cerrar ventana y salir del workspace
         */
        exitWorkspace() {

            const msj = document.getElementById("msjExitWorkspace").dataset.success;
            const exitUrl = document.getElementById("exitWorkspace").dataset.exitUrl;

            // ocultar el modal
            this.$bvModal.hide('exit-workspace-modal');

            // Mensaje de proceso terminado
            toastr.info(msj);

            // redirigir a la una ruta
            location.href = exitUrl;
        },

        /**
         * Cancelar el proceso
         * 
         */
        cancelProcess: function () {
      
            const $this = this;

            Axios.post(this.request.cancelProcess,
                {
                    subject      : $this.subject,     // El motivo de la cancelación
                }
            ).then(() => {
                location.href = $this.request.processCanceled;
            })
            .catch(error => {
                console.error(error);
            });
        },
    }
});
