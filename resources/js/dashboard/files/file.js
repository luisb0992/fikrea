/**
 * Maneja la subida de archivos del usuario
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

const {default: Axios} = require("axios");

let countErrors = 0;

/**
 * Filtro para mostrar el tamaño de un archivo en su formato adecuado
 *
 * @param  {Number} size     El tamaño del archivo en bytes
 *
 * @return {String}          El tamaño del archivo formateado
 *
 */
Vue.filter('formatSize', function (size) {

    const exponent = Math.floor(Math.log2(size) / 10);

    const filesize =
        {
            value: Math.ceil(size / ((10 ** 3) ** exponent)),
            unit: ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB'][exponent],
        };

    return `${filesize.value} ${filesize.unit}`;
});

/**
 * Carga el componente vue-upload-component
 *
 * @link https://lian-yue.github.io/vue-upload-component/#/documents
 *
 */
const VueUploadComponent = require('vue-upload-component');

Vue.component('file-upload', VueUploadComponent);

new Vue({
    el: '#app',
    data: {
        // El archivo seleccionado para su eliminación
        file: {
            id: null,
        },

        files: [],                          // La lista de archivos a subir

        diskSpace: {
            free: 0,                        // El espacio de almacenamiento libre disponible para subir archivos
            used: 0,                        // El espacio de almacenamiento usado por los archivos
            available: 0,                   // El espacio de almacenamiento disponible por la subscripción
        },

        maxFileSize: null,                  // El tamaño máximo adminisible para un archivo

        message: null,                      // Los mensajes de la aplicación

        request: {
            removeFile: request.dataset.removeFile,
        },

        route: null,                        // Las rutas para las solicitudes de la aplicación
    },

    components: {
        FileUpload: VueUploadComponent,
    },

    /**
     * Cuando la instancia está montada
     */
    mounted: function () {
        // Define el tamaño máximo de los archivos que se pueden subir
        this.maxFileSize = document.getElementById('config').dataset.maxSize;

        // Carga el almacenamiento
        this.diskSpace =
            {
                free: parseInt(document.getElementById('free-disk-space').value),
                used: parseInt(document.getElementById('used-disk-space').value),
                available: parseInt(document.getElementById('available-disk-space').value),
            };

        // Carga los mensajes de la aplicación
        this.message = {
            fileShared: document.getElementById('message').dataset.fileShared,
            fileNotValid: document.getElementById('message').dataset.fileNotValid,
            fileNotValidWithError: document.getElementById('message').dataset.fileNotValidWithError,
            fileTooBig: document.getElementById('message').dataset.fileTooBig,
            fileUploadSuccess: document.getElementById('message').dataset.fileUploadSuccess,
            storageExceeded: document.getElementById('message').dataset.storageAmountLimitExceeded,
            fileLocked: document.getElementById('message').dataset.fileLocked,
        };

        // Carga las rutas para las solicitudes
        this.route = {
            signFile: document.getElementById('route').dataset.signFileRequest,
            shareFile: document.getElementById('route').dataset.shareFileRequest,
            deleteFile: document.getElementById('route').dataset.deleteFileRequest,
        };
    },
    methods: {

        /**
         * Al seleccionar un archivo
         *
         * @param {File} newFile      El archivo nuevo          [Lectura y Escritura]
         * @param {File} oldFile      El archivo antiguo        [Sólo Lectura]
         */
        inputFile: function (newFile, oldFile) {

            // Subida automática de los archivos seleccionados
            // Cuando el archivo es cargado por vez primera (al inicio de la subida)
            if (Boolean(newFile) !== Boolean(oldFile) || oldFile.error !== newFile.error) {
                // Si la subida no está aún activa
                if (!this.$refs.upload.active) {
                    // Si el tamaño del archivo excede la capacidad de almacenamiento actualmente disposible
                    if (newFile.file.size > this.maxFileSize) {
                        toastr.error(this.message.fileTooBig);
                        newFile.error = 'abort';
                        return false;
                    }

                    // Si el tamaño del archivo excede la capacidad de almacenamiento libre, el archivo subirá en estado bloqueado
                    newFile.data.locked = (newFile.file.size > this.diskSpace.free);

                    // Actualiza el espacio de disco utilizado
                    this.diskSpace.free -= newFile.file.size;
                    this.diskSpace.used += newFile.file.size;

                    // Marca la subida como activa
                    this.$refs.upload.active = true;
                }
            }
        },

        /**
         * Al filtrar los archivos
         *
         * @param {File}     newFile   El archivo nuevo         [Lectura y Escritura]
         * @param {File}     oldFile   El archivo viejo         [Sólo Lectura]
         * @param {Function} prevent   Función que evita que la lista de archivos subidos sea cambiada
         *
         */
        inputFilter: function (newFile, oldFile, prevent) {

            // Si es un archivo nuevo y no uno viejo
            if (newFile && !oldFile) {
                // No es necesaria acción alguna
            }

            // si ha ocurrido algun erro con alguna validacion (mimes o tamaño)
            if (newFile.response == 400) {

                if (!countErrors) {
                    toastr.error(`${this.message.fileNotValidWithError} - ${newFile.name}`);
                    newFile.error = 'abort';
                    countErrors++;
                    return false;
                }


                // si el proceso fue satisfactorio
            } else if (newFile.success) {
                // Incrementa el contador de "Archivos"
                let badgeFiles;
                let files = 0;

                // Dependiendo del estado en que se subió el fichero, es el contador a actualizar
                if (newFile.data.locked) {
                    badgeFiles = document.getElementById('locked-files-badge');
                } else {
                    badgeFiles = document.getElementById('regular-files-badge');
                }

                files = badgeFiles.innerHTML.trim();
                badgeFiles.innerHTML = ++files;

                // Fija el contador de archivos subidos en "flash"
                badgeFiles.classList.add('blink-me');

                // Obtiene el id del archivo subido de la respuesta (JSON) del servidor
                // y lo fija
                newFile.id = newFile.response.id;

                // Fija la url del archivo para compartirlo con otros usuario
                newFile.url = this.route.shareFile.replace(':token', newFile.response.token);

                // Muestra un mensaje de que el archivo sea subido con éxito
                if (newFile.data.locked) {
                    toastr.warning(this.message.fileLocked);
                } else {
                    toastr.success(this.message.fileUploadSuccess);
                }
            }

            // Crea un objeto Blob    
            newFile.blob = URL.createObjectURL(newFile.file);
        },

        /**
         * Envía un archivo al proceso de firma
         *
         * @param {File} file       El archivo a firmar
         *
         */
        signFile: function (file) {

            // Obtiene la ruta para llevar el archivo al proceso de firma
            const signFileRequest = this.route.signFile.replace(':id', file.id);

            location.href = signFileRequest;
        },

        /**
         * Confirma la eliminación de un archivo
         *
         * @param {Number} id       El id del archivo a eliminar
         *
         */
        confirmRemoveFile: function (id) {
            this.file.id = id;
            this.$bvModal.show('remove-file-confirmation');
        },

        /**
         * Elimina un archivo
         */
        removeFile: function (id) {
            let theId = this.file.id;
            let modal = this.$bvModal;

            // Ejecutar la eliminación del fichero indicado
            axios.get(this.request.removeFile.replace(':id', theId))
                .then(() => {
                    // Eliminar la fila de la tabla que corresponde al fichero eliminado
                    document.getElementById('uploaded-file-' + theId).remove();

                    if (!document.querySelector('tr[id^="uploaded-file"]')) {
                        this.files = [];
                    }

                    // Cerrar la ventana modal porque el fichero fue eliminado con éxito
                    modal.hide('remove-file-confirmation');
                })
                .catch(error => {
                    console.error(error);
                });
        },

        /**
         * Compartir un fichero con un contacto
         *
         * @param {File} file  Un archivo
         */
        shareFile: function (file) {
            // Envío el formulario
            const form = document.getElementById('form');

            // Se comparte un fichero simple
            form.childNodes[form.childNodes.length - 1].value = [file.id];

            form.submit();
        },

        /**
         * Comparte la URL de la descarga
         */
        share: function () {
            // El ID del archivo que se comparte
            const id = document.getElementById('extra-data-id').value;

            // Generar un token aleatorio
            const token = this.generateToken(64);

            const url = route('workspace.set.share', {token: token});

            const $this = this;

            // El título y la descripción indicados en la ventana modal
            const title = document.getElementById('extra-data-title').value;
            const description = document.getElementById('extra-data-description').value;

            // Si estamos en un sistema Android, la app se encarga de manejar la compartición de archivos
            if (window.AndroidShareHandler) {
                window.AndroidShareHandler.share(url);

                // Registrar que fue compartido, para mostrar en el historial
                this.saveSharing(token, id, title, description);
            } else if (navigator.share) {
                navigator.share({
                    title: title,
                    text: description,
                    url: url,
                }).then(() => {
                    // Registrar que fue compartido, para mostrar en el historial
                    this.saveSharing(token, id, title, description);

                    console.info(`[OK] La dirección ${url} se ha compartido con éxito`);
                }).catch(error => {
                    console.error(`[ERROR] No se ha podido compartir. ${error}`);
                });
            } else if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(() => {
                    // Registrar que fue compartido, para mostrar en el historial
                    this.saveSharing(token, id, title, description);

                    toastr.success($this.message.shareFile.text);
                    console.info(`[OK] Se ha copiado la url ${url} en el portapapeles`);
                }).catch(error => {
                    console.error(`[ERROR] No se ha podido copiar la URL en el portapapeles. ${error}`);
                });
            }

            this.$bvModal.hide('sharing-title-and-description');
        },

        /**
         * Título y descripción para la compartición
         */
        sharingExtraData: function (id) {
            document.getElementById('extra-data-id').value = id;

            this.$bvModal.show('sharing-title-and-description');
        },

        /**
         * Generar un token aleatorio de la longitud indicada
         *
         * @param length
         * @returns {string}
         */
        generateToken(length) {
            const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';

            let result = '';

            const charactersLength = characters.length;
            for (let i = 0; i < length; i++) {
                result += characters.charAt(Math.floor(Math.random() * charactersLength));
            }

            return result;
        },

        /**
         * Registrar que el archivo fue compartido, para que aparezca en el historial
         *
         * @param token
         * @param id
         * @param title
         * @param description
         */
        saveSharing(token, id, title, description) {
            // Registrar la compartición
            axios.post(route('dashboard.save.file.sharing'), {
                token: token,
                no_contacts: true,
                title: title,
                description: description,
                files: [{'id': id}]
            }).catch(error => {
                console.error(error);
            });
        },

        /**
         * Adicionar a la lista de ficheros a subir una imagen copiada en el portapapeles
         */
        addClipboardData() {
            const file = document.getElementById('clipboard-file').data;

            this.$refs.upload.add(file);
        },

        /**
         * Redirigir hacia la interfaz para mover el fichero recién subido hacia una carpeta
         *
         * @param id
         */
        redirectToURL(id) {
            location.href = route('dashboard.files.move', {'id': id});
        }
    },
});