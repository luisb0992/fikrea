/**
 * Respuesta de una solicitud de renovación de un archivo aportado
 * 
 * El usuario responde a la solicitud de renovar un archivo que ha  porporcionando
 * y está cerca de expirar
 * 
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

 const { default: Axios } = require("axios");


 new Vue({
    el: '#app',
    components: {
        vuejsDatepicker,
    },
    data: {

        // El idioma que aplica al selector de fechas (datepicker)
        // Se obtiene del atributo lang del elemento html de la página
        //
        // La propiedad datepickerLanguage para el idioma español es un JSON:
        //
        // vdp_translation_es.js
        //
        // @link https://www.npmjs.com/package/vuejs-datepicker
        datepickerLanguage : eval(
            `vdp_translation_${document.querySelector('html').getAttribute('lang')}.js`
        ),

        documents   : [],                           // Los tipos de documentos
        selected    : null,                         // El tipo de documento seleccionado
                                                    // entre la lista de tipos de documentos

        issued_to   : null,                         // La fecha de expedición exigible al documento
        expiration_date   : null,                   // La fecha de vencimiento del documento

        files       : [],                           // Los archivos subidos
        position    :                               // La posición del usuario datum WGS84
        {
            latitude : null,
            longitude: null
        },

        visit : null,                               // La visita del usuario firmante

                                                    // Los mensajes de error de la aplicación
        error       : 
            {
                fileTooBig      : document.getElementById('errors').dataset.fileTooBig,     // Archivo muy grande
                fileNotValid    : document.getElementById('errors').dataset.fileNotValid,   // Tipo de archivo no válido
            },

        // Las fecha en que el documento a aportar puede expirar
        // de Hoy en adelante 8 días porque estoy renovando un doc, sino 1
        // Si se pone en 7, la primera fecha permitida, es una fecha en el rango de las fechas 
        // que se tienen en cuenta para decidir si el documento está al expirar 
        // @see https://github.com/charliekassel/vuejs-datepicker
        disabled_dates : { to: moment().add(8, 'days').toDate() },

    },
    /**
     * Cuando tenemos la instancia montada
     *
     */
    mounted: function () {

        // Carga los tipos de documentos
        let documents = JSON.parse(document.getElementById('data').dataset.documents);

        // Por algún motivo está llegando un objeto, debería ser un array
        if (typeof documents === 'object') {
            Object.entries(documents).forEach(([key, value]) => {
                this.documents.push(value);
            });
        } else {
            this.documents = documents;
        }

        // Obtenemos el registro de la visita del usuario a la página
        this.visit = JSON.parse(document.getElementById('visit').dataset.visit);
        
        // Deja seleccionado el primer documento de la lista de documentos
        this.selected = 
            {
                id      : this.documents[0].id,
                text    : this.documents[0].name
            };

        // Intentamos obtener la geolocalización del firmante
        this.getCurrentPosition()
            .then(() => {
                console.info(`[INFO] Se ha obtenido la posición del firmante con éxito`);
            })
            .catch(() => {
                console.info(`[INFO] No se ha podido obtener la posición del firmante`);
            });
        
    },
    methods: {
        /**
         * Carga el archivo
         * 
         * @param {Event} event 
         */
        loadFile: function (event) {
            
            const $this = this;

            // Obtiene el archivo
            const file = event.target.files[0];

            // Carga la vista previa de la imagen
            var reader = new FileReader();

            reader.readAsDataURL(file);
            reader.onload = function (event) {

                // El tipo de documento del archivo que se ha adjuntado
                // Una solicitud puede solicitar varios documentos y se asocia cada archivo adjuntado con uno
                // de esos documentos
                const document = $this.documents.find(document => document.id == $this.selected.id);

                // Si se ha fijado un tamaño maximo para el documento y el archivo lo supera
                if (document.maxsize && file.size > document.maxsize) {
                    toastr.error($this.error.fileTooBig);
                    return;
                }

                // Si se ha fijado un tipo de archivo determinado para el documento y el archivo suministrado
                // no se corresponde con el tipo del archivo requerido en lam solicitud
                if (document.type && file.type != document.type) {
                    toastr.error($this.error.fileNotValid);
                    return;
                }

                // Añade el archivo a la lista de archivos
                $this.files.push(
                    {
                        id          : Symbol(),
                        document    : document,
                        name        : file.name,
                        size        : file.size,
                        type        : file.type,
                        issued_to   : $this.issued_to ? moment($this.issued_to).format('YYYY-MM-DD') : null,
                        expiration_date   : $this.expiration_date ? moment($this.expiration_date).format('YYYY-MM-DD') : null,
                        file        : event.target.result,
                        hasPreview  : file.type.includes('image'),
                    }
                );

                // Pone en blanco el selector de la fecha de expedición
                $this.clearIssuedDate();
                // Limpia el selector de fecha de vencimiento
                $this.expiration_date = null;

            }; 
        },
        /**
         * Suprime un archivo
         * 
         * @param {File} file       El archivo a eliminar
         */
        removeFile: function(file) {
            this.files = this.files.filter(_file => file.id != _file.id);
        },
        /**
         * Elimina la fecha de expedición
         *
         */
        clearIssuedDate: function () {
            this.issued_to = null;     
        },
        /**
         * Guarda la solicitud de documentos
         * 
         * @param {Event} event
         * 
         */
        save: function (event) {

            const $this = this;
             
            
            // Obtiene la url para guardar la solicitud de documentos y la url de la redirección
            // tras el proceso de guardado 
            const request = 
                {
                    save     : event.currentTarget.dataset.saveRequest,
                    redirect : event.currentTarget.dataset.redirectRequest,
                };
            
            // Envía los documentos solicitados 
            HoldOn.open({theme: 'sk-circle'});

            // Envía la solicitud de documentos
            axios.post(request.save,
                {
                    documents   : $this.files,      // Los documentos adjuntados
                    position    : $this.position,   // La posición datum WGS84
                    visit       : $this.visit,      // La información de la visita realizada
                }
            )
            .then((response) => {
                HoldOn.close();

                if (response.data.code == 1) {
                    location.href = request.redirect;
                }
            })
            .catch(error => {
                HoldOn.close();
                console.error(error);
            });
 
        },
        /**
         * Obtiene la posición actual
         * 
         * @returns {Promise}       Una promesa con la posición calculada o un error
         */
        getCurrentPosition: function() {
            
            const $this = this;

            return new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(
                    pos => {
                        $this.position.latitude  = pos.coords.latitude;
                        $this.position.longitude = pos.coords.longitude;
                        resolve($this.position);
                    },
                    error => {
                        reject(error);
                    }, 
                    {
                        enableHighAccuracy: true,
                        timeout: 5000,
                        maximumAge: 0
                    }
                );
            });
        },

        // chekea si un documento ya ha sido aportado
        documentIsAlreadyAported: function (name) {
            return this.files.filter(file => {
                    return file.document.name == name
                }).length;
        }
    },
    computed: {

        // Devuelve los documentos que no se han aportado aun
        nonAportedDocuments: function () {
            const $this = this;
            let documents = [];
            $this.documents.forEach( document => {
                if (!$this.documentIsAlreadyAported(document.name))
                    documents.push(document)
            });
            return documents;
        },

        /**
         * Comprueba que la solicitud de documentos es válida o no
         * 
         * La solicitud es válida si contiene, al menos, un archivo por cada tipo de documento requerido
         * 
         * @return {Boolean}
         */
        requestIsNotValid: function () {
            
            // Cuenta el número de documentos de cada tipo adjuntados
            // y hace una lista con esa cuenta
            let documents = this.documents.map(document => {
                return this.files.filter(file => {
                    return file.document.id == document.id
                }).length;
            });

            // Comprueba si no se ha adjuntado alguno de los tipos de documentos requeridos
            // Si en la lista de cuenta hay algúnelemento nulo
            return documents.some(document => document == 0);
        },

        /**
         * Devuelve el documento que se está aportando según la selección del tipo
         * 
         * @return RequiredDocument
         */
         requiredDocument: function () {
            return this.documents.filter(document => document.id == this.selected.id)[0] || null;
         },

         /**
         * Devuelve true si el documento que se esta aportando requiere fecha de vencimiento
         * y la misma ya ha sido seleccionada
         * 
         * @return boolean
         */
         cannotUploadDocument: function () {

            // false significa que si puede subir el documento requerido
            if (this.requiredDocument) {
                if (!this.requiredDocument.has_expiration_date) {
                    return false;
                } else {
                    if (this.requiredDocument.has_expiration_date == 1 && this.expiration_date) {
                        return false;
                    }
                }

                return true;
            }
            return true;
         },
    },
    filters: {
        /**
         * Redondea un valor al entero más próximo
         * 
         * @param {Number} value  
         * 
         * @return {Number}
         * 
         */
        int: function (value) {
            return Math.round(value);
        },
        /**
         * Expresa la fecha en formato de cadena estándar
         * 
         * @param {Date} date
         * 
         * @return {String} 
         */
        date: function (date) {
            if (!date) {
                return null;
            } else {
                return moment(date).format('DD-MM-YYYY');
            }
        }
    }
});