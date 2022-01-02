/**
 * Respuesta de una solicitud de documentos
 * 
 * El usuario responde a la solicitud de documentos porporcionando
 * los archivos necesarios
 * 
 * @author javieru <javi@gestoy.com>
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

const { default: Axios } = require("axios");

// register the component
Vue.component('Treeselect', VueTreeselect.Treeselect)

new Vue({
    el: '#app',

    // register the components
    components: {
        vuejsDatepicker
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
        aportedDocuments: [],                       // Documentos que se han aportados
        nonAportedDocuments: [],                    // Documentos que faltan por aportar

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
                fileTooBig      : document.getElementById('messages').dataset.fileTooBig,     // Archivo muy grande
                fileNotValid    : document.getElementById('messages').dataset.fileNotValid,   // Tipo de archivo no válido
            },

        messages : {},                              // Mensajes en la vista

        // Las fecha en que el documento a aportar puede expirar
        // de Hoy en adelante 7 dias
        // @see https://github.com/charliekassel/vuejs-datepicker
        disabled_dates : { to: moment().add(1, 'days').toDate() },

        fileFromFikrea: false,          // Si se selecciona el archivo desde fikrea para el creador
        fromFikreaCloud: null,
        filesInFikreaCloud: null,       // Archivos en la nube
        filesSelected : [],             // Para meter los archivos que he seleccionado
        optionsFikreaCloud: [
            {                           // Opciones para el componente árbol
                id: 'a',
                label: 'a',
                children: [ 
                    {
                        id: 'aa',
                        label: 'aa',
                    },{
                        id: 'ab',
                        label: 'ab',
                    }
                ],
            },
            {
                id: 'b',
                label: 'b',
            },
            {
                id: 'c',
                label: 'c',
            }
        ],
        requestContent: '',             // La ruta para obtener el contenido base 64 de un archivo
    },
    /**
     * Cuando tenemos la instancia montada
     */
    mounted: function () {

        // Carga los tipos de documentos
        this.documents = JSON.parse(document.getElementById('data').dataset.documents)
        // this.documents.forEach(document => document.moreFiles = true );

        this.nonAportedDocuments = [...this.documents];     // Documentos que faltan por aportar

        // Obtenemos el registro de la visita del usuario a la página
        this.visit = JSON.parse(document.getElementById('visit').dataset.visit);

        // Obtenemos los mensajes que se mostrarán en la vista
        this.messages.fileAported = document.getElementById('messages').dataset.fileAported;

        // Deja seleccionado el primer documento de la lista de documentos
        this.selected = 
            {
                id      : this.nonAportedDocuments[0].id,
                text    : this.nonAportedDocuments[0].name
            };

        // Intentamos obtener la geolocalización del firmante
        this.getCurrentPosition()
            .then(() => {
                console.info(`[INFO] Se ha obtenido la posición del firmante con éxito`);
            })
            .catch(() => {
                console.info(`[INFO] No se ha podido obtener la posición del firmante`);
            });

        // data-file-system="@json($fileSystem)"
        if (document.getElementById('creator-data')) {
            this.filesInFikreaCloud = JSON.parse(document.getElementById('creator-data').dataset.fileSystem);
            this.optionsFikreaCloud = JSON.parse(document.getElementById('creator-data').dataset.fileSystemTree);
            this.requestContent = document.getElementById('creator-data').dataset.requestFileContent;
            this.requestContent = this.requestContent.substr(0, this.requestContent.length - 1);
        }
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
            
            if (file.type == "") {
                toastr.error(document.getElementById('messages').dataset.fileTypeError);
                return;
            }

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

                toastr.info($this.messages.fileAported);

                // Abre la modal para preguntar si va a adjuntar más archivos a este documento solicitado
                $this.$bvModal.show('more-files');
            }; 
        },

        /*
         * Cuando se selecciona continuar adicionando archivos para el documento que estoy
         * aportando se deja en el array de nonAportedDocuments
         */
        moreFilesSelected: function () {
            const $this = this;

            // Oculto la modal
            $this.$bvModal.hide('more-files');
        },

        /*
         * Cuando se selecciona finalizar, que ya no se va a adicionando archivos para
         * el documento que estoy aportando se mueve de nonAportedDocuments para aportedDocuments
         */
        noMoreFilesSelected: function () {
            const $this = this;

            // Muevo el documento de los documentos no aportados a los aportados
            const indexDocument = $this.nonAportedDocuments.findIndex(document => document.id == $this.selected.id);
            $this.aportedDocuments.push($this.nonAportedDocuments.splice(indexDocument, 1)[0]);

            // Deja seleccionado el primer documento de la lista de documentos
            if (this.nonAportedDocuments.length) {
                this.selected = 
                    {
                        id      : this.nonAportedDocuments[0].id,
                        text    : this.nonAportedDocuments[0].name
                    };
            }

            // Oculto la modal
            this.$bvModal.hide('more-files');

        },

        /**
         * Suprime un archivo
         * 
         * @param {File} file       El archivo a eliminar
         */
        removeFile: function(file) {
            const $this= this;

            // Elimino el archivo del listado de archivos seleccionados para aportar
            $this.files = $this.files.filter(_file => file.id != _file.id);

            // Si hay otros archivos para el mismo documento al que pertenece el archivo que se elimina
            // no se hace nada, 
            // sino si no se encuentra el documento en no aportados se mueve hacia estos
            if (!$this.files.filter(_file => file.document.id == _file.document.id).length) {
                // Si el documento al que pertenece el archivo no se encuentra en el listado de archivos no aportados
                // se adiciona a estos
                const indexDocInNonAported = $this.nonAportedDocuments.findIndex(_doc => _doc.id == file.document.id);
                if (indexDocInNonAported == -1) {
                    // Muevo el documento de los documentos aportados a los no aportados

                    const indexDocument = $this.aportedDocuments.findIndex(document => document.id == file.document.id);
                    $this.nonAportedDocuments.push($this.aportedDocuments.splice(indexDocument, 1)[0]);

                    toastr.warning(document.getElementById('messages').dataset.fileToNonAported);
                }

            }

            // Deja seleccionado el primer documento de la lista de documentos
            if ($this.nonAportedDocuments.length) {
                $this.selected = 
                    {
                        id      : $this.nonAportedDocuments[0].id,
                        text    : $this.nonAportedDocuments[0].name
                    };
            }
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
                    creator     : document.getElementById('creator-data')? true:false,  // Si quien aporta es el creador
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

        /**
         * Chequea si un documento ha sido aportado o no
         */
        documentIsAlreadyAported: function (name) {
            return this.files.filter(file => {
                    return file.document.name == name
                }).length;
        },

        /**
         * Adiciona los archivos seleccionados como documentos aportados
         */
        selectForTree: function () {
            const $this = this;

            $this.filesSelected = [];

            $this.fromFikreaCloud.forEach( key => {
                const file = $this.filesInFikreaCloud.filter( file => file.id == key)[0];

                // Si es un archivo
                if (file.is_folder === 0) {
                    $this.filesSelected.push(file);
                // Si es una carpeta, lo hago recursivamente
                } else {
                    $this.filesSelected.push($this.getSonsFiles(file));
                }
                
            });

            $this.filesSelected = $this.filesSelected.filter(file => file !== undefined)

            // El tipo de documento del archivo que se ha adjuntado
            // Una solicitud puede solicitar varios documentos y se asocia cada archivo adjuntado con uno
            // de esos documentos

            const document = $this.documents.find(document => document.id == $this.selected.id);
            
            $this.filesSelected.forEach(file => {
                // Añade el archivo a la lista de archivos

                const hasPreview = file.type.includes('image');
                const expiration_date = $this.expiration_date ? moment($this.expiration_date).format('YYYY-MM-DD') : null;
                const issued_to = $this.issued_to ? moment($this.issued_to).format('YYYY-MM-DD') : null;

                if (hasPreview) {
                    // Envía los documentos solicitados 
                    HoldOn.open({theme: 'sk-circle'});

                    axios.get(`${this.requestContent}${file.id}`)
                        .then(resp => {
                            HoldOn.close();

                            $this.files.push(
                                {
                                    id          : Symbol(),
                                    document    : document,
                                    name        : file.name,
                                    size        : file.size,
                                    type        : file.type,
                                    issued_to   : issued_to,
                                    expiration_date: expiration_date,
                                    file        : `data:image/png;base64, ${resp.data}`,
                                    hasPreview  : hasPreview,
                                }
                            );

                            $this.adjustAfterSaveFile();

                        }).catch(error=>{
                            HoldOn.close();
                        });

                } else {
                    $this.files.push(
                        {
                            id          : Symbol(),
                            document    : document,
                            name        : file.name,
                            size        : file.size,
                            type        : file.type,
                            issued_to   : issued_to,
                            expiration_date: expiration_date,
                            file        : file.path,
                            hasPreview  : hasPreview,
                        }
                    );

                    $this.adjustAfterSaveFile();
                }
                
            });
            
        },

        /**
         * Ajustes a realizar cuando se agrega un archivo para ser aportado
         */
        adjustAfterSaveFile: function () {
            const $this = this;

            // Desmarco el selector del almacenamiento en la nube
            $this.fileFromFikrea = false;

            // Pone en blanco el selector de la fecha de expedición
            $this.clearIssuedDate();
            
            // Limpia el selector de fecha de vencimiento
            $this.expiration_date = null;

            toastr.info($this.messages.fileAported);

            // Deja seleccionado el primer documento de la lista de documentos
            if ($this.nonAportedDocuments.length) {
                $this.selected = 
                    {
                        id      : $this.nonAportedDocuments[0].id,
                        text    : $this.nonAportedDocuments[0].name
                    };
            }

            // Abre la modal para preguntar si va a adjuntar más archivos a este documento solicitado
            $this.$bvModal.show('more-files');
        },

        /**
         * Devuelve los archivos dentro de la carpeta con id=$id
         */
        getSonsFiles: function (file) {
            const $this = this;

            // Si es un archivo
            if (file.is_folder === 0) {
                $this.filesSelected.push(file);

            //Si es una carpeta, lo hago recursivamente
            } else {
                if (file.id) {
                    let sons = $this.filesInFikreaCloud.filter(son => son.parent_id == file.id);
                    sons.forEach(file => {
                        $this.filesSelected.push($this.getSonsFiles(file));
                    })
                }
            }
        },

    },
    computed: {

        /*
        // Devuelve los documentos que no se han aportado aún
        nonAportedDocuments: function () {
            console.log('Devolviendo documentos que se deben aportar...')
            const $this = this;

            let documents = [];

            $this.documents.forEach( document_ => {
                
                console.log(document_)

                console.log('already aporter')
                console.log($this.documentIsAlreadyAported(document_.name))

                if (document_.moreFiles == true) {
                    documents.push(document_);
                    console.log('lo tomo por atributo moreFiles')
                } else if (!$this.documentIsAlreadyAported(document_.name)) {
                    documents.push(document_);
                    console.log('lo tomo por que no ha subido documento aun')

                }
                    
            });

            return documents;
        },
        */

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
            // Si en la lista de cuenta hay algúnbelemento nulo
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
