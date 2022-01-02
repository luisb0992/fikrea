/**
 * La lista de archivos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

const {default: Axios} = require("axios");

new Vue({
    el: '#app',
    data: {
        // El archivo seleccionado para su eliminación
        file: {
            id: null,
        },

        // La lista de archivos a compartir
        files: [],
        // Se almacena en almacenamiento local del navegador
        // lo que permite recordar esa lista al cambiar entre páginas del listado

        // El espacio en disco del usuario
        diskSpace: {
            free: 0,                // El espacio de almacenamiento libre disponible para subir archivos
            used: 0,                // El espacio de almacenamiento usado por los archivos
            available: 0,                // El espacio de almacenamiento disponible por la subscripción
        },

        // Los mensajes de la aplicación
        message: {
            shareFile: {
                title: message.dataset.shareTitle,
                text: message.dataset.shareText,
            },
        },

        request: {
            removeFile: request.dataset.removeFile,
        },

        // La cantidad de archivos a mostrar en la pagina - 10 por defecto
        filesToShow: 10,

        // Opciones para la cantidad de archivos a mostrar en la pagina
        optionsFilesToShow: [{
            value: 10,
            text: '10'
        }, {
            value: 25,
            text: '25'
        }, {
            value: 50,
            text: '50'
        }, {
            value: 100,
            text: '100'
        }, {
            value: 500,
            text: '500'
        }, {
            value: 1000,
            text: '1000'
        }],

        allSelected: false,            // Controla cuando se seleccionan todos los archivos
    },
    /**
     * Cuando la instancia está montada
     *
     */
    mounted: function () {
        // Carga la lista de archivos seleccionados del almacenamiento local
        // desde la clave "selected-files"
        // Con ello se obtienen los archivos seleccionados en otras páginas diferentes a la actual
        this.files = JSON.parse(localStorage.getItem('selected-files')) || [];

        // Carga el almacenamiento
        this.diskSpace =
            {
                free: parseInt(document.getElementById('free-disk-space').value),
                used: parseInt(document.getElementById('used-disk-space').value),
                available: parseInt(document.getElementById('available-disk-space').value),
            };

        // Elementos mostrados en la vista
        this.filesToShow = document.getElementById("filesToShow").value;
    },
    methods: {

        /**
         * Cargando X elementos en la vista
         */
        changingSelection: function (count) {
            let search = location.search;

            // Redirigir a la vista cargando los X elementos
            location.href = route('dashboard.file.list', {count: count}) + search;
        },

        /**
         * Selecciona un archivo
         *
         */
        select: function () {
            // Guarda la lista de archivos seleccionados en el almacenamiento local
            localStorage.setItem('selected-files', JSON.stringify(this.files));

            // Determinar el total de archivos seleccionados
            const count = this.files.length;

            // Actualizar el contador de archivos seleccionados
            document.getElementById('selected-files-badge').innerHTML = count.toString();

            // Mostrar la opción en el menú únicamente si existen archivos seleccionados
            document.getElementById('selected-files-menu').style.display = count ? 'inherit' : 'none';
        },
        /**
         * Selecciona todos los archivos
         *
         * @param {Event} event
         */
        selectAll: function (event) {
            if (event.target.checked) {
                // Marca todos los documentos
                this.files = [...document.querySelectorAll('.file')].map(file => file.value);
                this.allSelected = true;
            } else {
                // Desmarca todos los documentos
                this.files = [];
                this.allSelected = false;
            }

            // Guarda la lista de archivos seleccionados en el almacenamiento local
            this.select();
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
                    title: $this.message.shareFile.title,
                    text: $this.message.shareFile.text,
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
         * Compartir los archivos seleccionados
         *
         */
        shareFiles: function (event) {
            // Elimina los elementos seleccionados del almacenamiento local
            localStorage.removeItem('selected-files');

            // Envío el formulario
            const form = document.getElementById('form');

            let id = event.target.dataset.id;

            // Si se indica un ID, se comparte un fichero simple, no una selección múltiple
            form.childNodes[form.childNodes.length - 1].value = (typeof id === 'undefined') ? this.files : [id];

            form.submit();
        },

        /**
         * Mover los archivos seleccionados
         *
         */
        moveFiles: function () {
            // Elimina los elementos seleccionados del almacenamiento local
            localStorage.removeItem('selected-files');

            // Envío el formulario
            const form = document.getElementById('formMultipleMove');

            // Si se indica un ID, se comparte un fichero simple, no una selección múltiple
            form.childNodes[form.childNodes.length - 1].value = this.files;

            // Resetea la lista de archivos seleccionados
            this.files = [];

            form.submit();
        },

        /**
         * Firmar archivos seleccionados
         */
        selectedSign: function () {
            // Elimina los elementos seleccionados del almacenamiento local
            localStorage.removeItem('selected-files');

            // Envío el formulario
            const form = document.getElementById('formMultiple');
            form.childNodes[form.childNodes.length - 1].value = this.files
            form.submit();
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
         * Registrar que los archivos fueron compartidos
         *
         * @param token
         * @param title
         * @param description
         */
        saveSharingMultiple(token, title, description) {
            // Elimina los elementos seleccionados del almacenamiento local
            localStorage.removeItem('selected-files');

            // Registrar la compartición
            axios.post(route('dashboard.save.file.sharing'), {
                token: token,
                no_contacts: true,
                title: title,
                description: description,
                files: this.files.map(function (item) {
                    let obj = {};
                    obj['id'] = item;
                    return obj;
                }),
            }).catch(error => {
                console.error(error);
            });
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
         * Disparar el diálogo de compartir nativo del dispositivo (acción 'Copiar URL')
         */
        shareFilesNoContacts() {
            // Generar un token aleatorio
            const token = this.generateToken(64);

            const that = this;
            const url = route('workspace.set.share', {token: token});

            // El título y la descripción indicado en la ventana modal
            const title = document.getElementById('extra-data-title').value;
            const description = document.getElementById('extra-data-description').value;

            // Si estamos en un sistema Android, la app se encarga de manejar la compartición de archivos
            if (window.AndroidShareHandler) {
                window.AndroidShareHandler.share(url);

                // Registrar los ficheros compartidos
                this.saveSharingMultiple(token, title, description);
            } else if (navigator.share) {
                navigator.share({
                    title: that.message.shareFile.title,
                    text: that.message.shareFile.text,
                    url: url,
                }).then(() => {
                    // Registrar los ficheros compartidos
                    this.saveSharingMultiple(token, title, description);

                    console.info(`[OK] La dirección ${url} se ha compartido con éxito`);
                }).catch(error => {
                    console.error(`[ERROR] No se ha podido compartir. ${error}`);
                });
            } else if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(() => {
                    // Registrar los ficheros compartidos
                    this.saveSharingMultiple(token, title, description);

                    toastr.success(that.message.shareFile.text);
                    console.info(`[OK] Se ha copiado la url ${url} en el portapapeles`);
                }).catch(error => {
                    console.error(`[ERROR] No se ha podido copiar la URL en el portapapeles. ${error}`);
                });
            }

            this.$bvModal.hide('sharing-title-and-description-multiple');
        },

        /**
         * Limpia la lista de archivos seleccionados
         *
         */
        clearFiles: function () {
            // Resetea la lista de archivos seleccionados
            this.files = [];
            // Elimina los elementos seleccionados del almacenamiento local
            localStorage.removeItem('selected-files');
            // Desmarca los checkboxs del header de la tabla
            document.querySelectorAll('[type="checkbox"]').forEach(ch => {
                ch.checked = false;
            })
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
         *
         *
         */
        removeFile: function (id) {
            location.href = this.request.removeFile.replace(':id', this.file.id);
        },
        /**
         * Confirma la eliminación de una slección de archivos
         *
         *
         */
        confirmRemoveFiles: function () {
            this.$bvModal.show('remove-files-confirmation');
        },
        /**
         * Elimina los archivos seleccionados
         *
         */
        removeFiles: function () {
            // Elimina los elementos seleccionados del almacenamiento local
            localStorage.removeItem('selected-files');

            const $this = this;

            // Obtiene la ruta para eliminar los archivos
            const requestRemoveFiles = document.querySelector('form').dataset.requestRemoveFiles;

            // Obtiene la redirección tras la eliminación de los archivos
            const afterRemoveRedirectTo = document.querySelector('form').dataset.afterRemoveRedirectTo;

            Axios.post(requestRemoveFiles,
                {
                    files: $this.files,
                }
            )
                .then(() => {
                    location.href = afterRemoveRedirectTo;
                })
                .catch(error => {
                    console.error(error);
                });
        },

        /*
         * Descarga los archivos seleccionados
         */
        downloadFiles: function () {
            const $this = this;

            // Obtiene la ruta para eliminar los archivos
            const requestDownloadFiles = document.querySelector('form').dataset.requestDownloadFiles;

            Axios.post(requestDownloadFiles, {files: $this.files}, {responseType: 'arraybuffer'}).then((response) => {
                let fileURL = window.URL.createObjectURL(new Blob([response.data], {type: 'application/zip'}));
                let fileLink = document.createElement('a');

                fileLink.href = fileURL;
                fileLink.setAttribute('download', 'download-multiple.zip');
                document.body.appendChild(fileLink);

                fileLink.click();

                document.body.removeChild(fileLink);
            }).catch(error => {
                console.error(error);
            });
        },

        /**
         * Título y descripción para la compartición
         */
        sharingExtraData: function (id) {
            document.getElementById('extra-data-id').value = id;

            this.$bvModal.show('sharing-title-and-description');
        },

        /**
         * Título y descripción para la compartición
         */
        sharingExtraDataMultiple: function () {
            this.$bvModal.show('sharing-title-and-description-multiple');
        }
    }
});