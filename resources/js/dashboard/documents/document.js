/**
 * Maneja la subida de documentos del usuario
 * 
 * @author javieru <javi@gestoy.co>
 * @copyright 2021 Retail Servicios Externos
 */

new Vue({
    el: '#app',
    data: {
        id                   : id.value || null,
        comment              : null,
        file                 : null
    },
    methods: {
        /**
         * Sube el documento
         * 
         * @param {Event} event 
         */
        uploadDocument: function (event) {

            // Obtiene el archivo
            const file = event.target.files[0];

            this.file = file.name;

            // Comprueba si es un archivo válido
            if (!this.isValidFile(file)) {
                toastr.error(`
                    ${browser.dataset.messageFailed} 
                    ${this.file}
                `);
                return false;
            }
        },

        /**
         * Comprueba si un archivo válido
         *
         * @param {String} file     Un archivo
         *
         * @return {Boolean}        true si es una imagen admitida 
         */
        isValidFile: function (file) {
        
            // Listado de tipos aceptados por la aplicación
            const acceptedImageTypes = 
                [
                    'application/pdf',                                                          // Documento PDF
                    'application/msword',                                                       // Microsoft Word
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',  // Microsoft Word 2007
                    'application/vnd.ms-excel',                                                 // Microsoft Excel
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',        // Microsoft Excel 2007 
                    'image/bmp',                                                                // Imagen Mapa de bits
                    'image/gif',                                                                // Imagen GIF
                    'image/jpeg',                                                               // Imagen JPG
                    'image/png',                                                                // Imagen PNG
                    'image/tiff'                                                                // Imagen TIFF
                ];
            return file && acceptedImageTypes.includes(file['type'])
        }
    },
    computed: {
        /**
         * Habilita/Deshabilita el botón para guardar el documento
         * 
         * @param {Event} event 
         */
        noFile: function () {
            return !this.id && !this.file; 
        }
    }
 });