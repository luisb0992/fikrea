/**
 * Crea un documento
 * 
 * @author javieru <javi@gestoy.co>
 * @copyright 2021 Retail Servicios Externos
 */

const { default: Axios } = require('axios');

new Vue({
    el: '#app',
    data: {
        // Las solicitudes
        request :
            {
                textFromFile   : request.dataset.textFromFile,
            },
        // La configuración OCR de la aplicación
        ocr:
            {
                mimes   : JSON.parse(ocr.dataset.mimes),
                maxSize : parseInt(ocr.dataset.maxSize) * Math.pow(2, 20),
            },
        // Los mensajes de la aplicación
        message :
            {
                fileIsNotValid : message.dataset.fileIsNotValid,
                fileTooBig     : message.dataset.fileTooBig,
                ocrFailed      : message.dataset.ocrFailed,
            },
    },
    /**
     * Cuando la instancia está montada
     * 
     * 
     */
    mounted: function () {
        this.arrayFromList();

        // Obtiene el idioma de la página
        const lang = document.querySelector('html').getAttribute('lang');

        // Configura tinymce
        tinymce.init({
            selector    : '#content',
            language    : lang,
            statusbar   : false,
        });
    },
    methods: {
        /**
         * Selecciona un archivo para subir
         * 
         */
        selectFile: function () {
            file.click();
        },
        /**
         * Sube un archivo para obtener mediante reconocimiento óptico el texto
         *
         * @param {Event} event
         *
         */
        getTextFromFile: function (event) {

            const $this = this;

            // Obtiene el archivo
            const file = event.target.files[0];

            // Comprueba si es un archivo válido
            if (!this.isValidFile(file)) {
                toastr.error(this.message.fileIsNotValid);
                return false;
            }

            // Si el archivo es demasiado grande para ser procesado
            if (file.size > $this.ocr.maxSize) {
                toastr.error($this.message.fileTooBig);
                return false;   
            }
 
            var formData = new FormData();
            formData.append('file', file);   

            // Inicia la animación
            HoldOn.open({theme: 'sk-circle'});

            // Envíamos y guardamos el sello en el servidor
            Axios.post(
                $this.request.textFromFile, 
                formData,
                {
                    headers: 
                        {
                            'Content-Type': 'multipart/form-data',
                        }
                }
            )
            .then(response => {
                // Oculta la animación
                HoldOn.close();

                // Obtiene el contenido existente
                let text =  tinymce.get('content').getContent();

                // Añade el contenido con el texto que ha sido reconocido por OCR
                tinymce.get('content').setContent(text + response.data.text);
            })
            .catch(error => {
                // Oculta la animación
                HoldOn.close();

                // Muestra el error
                toastr.error(`${this.message.ocrFailed}<br />${error.response.data.message}`);
            });

        },
        /**
         * Comprueba si un archivo es válido
         *       
         * Para los sellos estampados se recomienda utilizar PNG o GIF que
         * adminiten transparencia al disponer de canal alpha
         *
         * @param {String} file     Un archivo
         *
         * @return {Boolean}        true si es un archivo admitido
         */
        isValidFile: function (file) {
            return file && this.ocr.mimes.includes(file['type'])
        },
        /**
         * Comprueba se esta cambiando de ruta
         *       
         * Verifica si se esta cambiando de ruta al oprimir cualquiera opcion del sidebar 
         * Envia el modal con la pregunta, si esta seguro de cambiar de ruta
         */
        arrayFromList: function () {
            var self = this;
            //Array con cada value:
            var dataValue = [...document.querySelectorAll(`ul.vertical-nav-menu li`)]
                .forEach((element) => {
                    if (element.className != "app-sidebar__heading") {
                        let href = element.childNodes[1].href;
                        element.addEventListener("click", async (e) => {
                            e.preventDefault();
                            await self.$bvModal.show('link-sidebar-modal');
                            let link = document.getElementById('linkSidebarModal');
                            link.href = href;
                        });
                    }
                });
        }
    }
 });
