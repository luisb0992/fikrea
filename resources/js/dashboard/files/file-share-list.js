/**
 * Lista de comparticiones o conjuntos de archivos compartidos con los usuarios
 * 
 * @author javieu <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

new Vue({
    el: '#app',
    methods: {
        /**
         * Comparte la URL de la descarga
         * 
         * 
         * @param {String} url  La url de descarga del archivo
         *  
         */
        share: function (url) {

            // Obtiene los mensajes de la aplicación
            const message = document.getElementById('message').dataset;

            // Si estamos en un sistema Android, la app se encarga de manejar la compartición de archivos
            if (window.AndroidShareHandler) {
                window.AndroidShareHandler.share(url);
            } else if (navigator.share) {
                navigator.share({
                    title: message.shareTitle,
                    text : message.shareText,
                    url  : url,
                })
                .then(() => {
                    console.info(`[OK] La dirección ${url} se ha compartido con éxito`);
                })
                .catch(error => {
                    console.error(`[ERROR] No se ha podido compartir. ${error}`)
                    console.error(error);
                });
            } else if (navigator.clipboard) {
                navigator.clipboard.writeText(url)
                .then(() => {
                    toastr.success(message.shareText);
                    console.info(`[OK] La dirección ${url} se ha compartido con éxito`);
                })
                .catch(error => {
                    console.error(`[ERROR] No se ha podido copiar la URL en el portapapeles. ${error}`);
                });
            }      
        },
    }
});
