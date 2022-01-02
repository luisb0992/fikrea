/**
 * Crea una solicitud de documento
 * 
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

const { default: Axios } = require("axios");

Vue.config.productionTip = false;

// Para usar Vuelidate desde assets
const validationMixin = window.vuelidate.validationMixin;
const { required, minLength } = window.validators;

Vue.use(window.vuelidate.default);


// Importamos el componente Select2
// @link https://github.com/godbasin/vue-select2
import Select2 from 'v-select2-component';

new Vue({
    el: '#app',
    components: {
        Select2,
    },
    
    mixins: [
        validationMixin
    ],

    data: {
        // Medida del tiempo
        TimeUnit :
            {
                Days    :   1,
                Months  :  30,
                Years   : 365,
            },

        // Los documentos requeridos de ejemplo
        documentExamples: [],

        // El id del la solicitud
        id   : null,
        // El nombre de la solicitud 
        name : null,
        // El comentario
        comment : null,

        // La lista de documentos solicitados
        documents : [],

        // El documento que se va a adicionar
        document : {
            id              : Symbol().toString(),        // El id del documento requerido
            name            : '',              // El nombre del documento requerido
            comment         : null,            // El comentario del documento requerido
            type            : '',              // El tipo del documento requerido
                                               // o vacio si puede ser cualquiera
            issued_to       : null,            // La fecha de expedición que se exige al documento requerido
                                               // o null si no exige
            validity        : 0,               // El periodo de validez exigible para el documento
                                               // o cero si no se exige
            validity_unit   : 1,               // La unidad de tiempo de medida del periodo de validez
            maxsize         : '',              // El tamaño máximo del archivo requerido
                                               // o vacio si puede ser cuqluiere
            has_expiration_date : false,           // Si el firmante debe introducir fecha de caducidad del documento
            notify          : false,           // Recibir notificaciones antes de que el documento expire
        },

        // La Solicitud
        request : {
            name: '',                           // Nombre de la solicitud
            comment: '',                        // Comentario de la solicitud
        },

        // Textos de dia, mes, ano para singular y plural
        texts : {},

        // Mensajes en el proceso de creación de la solicitud
        messages: {},

        // Fecha inicial de emisión del documento
        dateInit: '',

        // Controla cuando estoy editando un documento
        editing: false,

    },

    // Validaciones para Vuelidate
    validations: {

        // validaciones para el documento
        document: {
            name: {
                required,
            },
        },

        // validaciones de la solicitud
        request: {
            name: {
                required,
            },
            comment: {
                required,
            },
        }
        
    },
    /**
     * Cuando la instancia está montada
     */
    mounted: function () {
        
        const $this = this;

        // Obtiene el idioma de la página
        const lang = document.querySelector('html').getAttribute('lang');

        // Carga los documentos requeridos de ejemplo
        const documentExamples = JSON.parse(document.getElementById('document-examples').dataset.documents);

        documentExamples.forEach(documentExample => $this.documentExamples.push(documentExample.name));

        // textos para validez de documento
        $this.texts = JSON.parse(document.getElementById('data').dataset.texts);

        // Mensajes
        // data-message-request-saved
        $this.messages.requestSaved = document.getElementById('data').dataset.messageRequestSaved;
        // data-message-non-selected-doc
        $this.messages.nonSelectedDoc = document.getElementById('data').dataset.messageNonSelectedDoc;
        // data-message-request-uncomplete
        $this.messages.unCompleteRequest = document.getElementById('data').dataset.messageRequestUncomplete;


        // verifico si hay documents en el localStorage
        if (localStorage.getItem('requests-documents')) {
            $this.documents = JSON.parse(localStorage.getItem('requests-documents'));
        }

        // Carga la modal de ayuda
        this.showHelpModal();
    },
    methods: {
        // Valida el campo name con vuelidate según reglas de validación establecidas en validations
        validateDocumentState: function(name) {
          const { $dirty, $error } = this.$v.document[name];
          return $dirty ? !$error : null;
        },

        // Valida la solicitud de documentos
        validateRequestState: function(name) {
          const { $dirty, $error } = this.$v.request[name];
          return $dirty ? !$error : null;
        },

        /**
         * Muestra una modal de ayuda para el usuario invitado
         * 
         */
        showHelpModal: function () {
            this.$bvModal.show('help-for-document-requests');
        },
        /**
         * Selecciona un periodo de validez determinado para un documento
         * 
         * @param {Document} document   El documento requerido seleccionado
         * @param {Number}   validity   El periodo de validez para ese documento
         * @param {TimeUnit} unit       La unidad de medida del periodo de validez    
         *
         */
        selectValidity: function (document, validity, unit) {
            document.validity      = validity;
            document.validity_unit = unit;

            // Calcula la fecha de inicio
            switch (document.validity_unit) {
                case this.TimeUnit.Days:
                    // Obtengo la fecha de emisión restando x dias a la fecha actual
                    this.dateInit = moment()
                    .subtract( document.validity_unit * document.validity, 'd')
                    .format('DD-MM-YYYY');
                    break;
                case this.TimeUnit.Months:
                    // Obtengo la fecha de emisión restando x meses a la fecha actual
                    this.dateInit = moment()
                        .subtract(document.validity, 'M')
                        .format('DD-MM-YYYY');
                    break;
                case this.TimeUnit.Years:
                    // Obtengo la fecha de emisión restando x años a la fecha actual
                    this.dateInit = moment()
                        .subtract(document.validity, 'Y')
                        .format('DD-MM-YYYY');
                    break;
            }
            
        },

        /**
         * Restablece el formulario
         */
        resetForm: function() {
            const $this = this;
            $this.resetDocument();

            $this.$nextTick(() => {
                $this.$v.$reset();
            });
        },
        /**
         * Restablece los datos del documento
         */
         resetDocument: function(){
            this.document = {
                id              : Symbol(),        // El id del documento requerido
                name            : '',              // El nombre del documento requerido
                comment         : null,            // El comentario del documento requerido
                type            : '',              // El tipo del documento requerido
                                                   // o vacio si puede ser cualquiera
                issued_to       : null,            // La fecha de expedición que se exige al documento requerido
                                                   // o null si no exige
                validity        : 0,               // El periodo de validez exigible para el documento
                                                   // o cero si no se exige
                validity_unit   : 1,               // La unidad de tiempo de medida del periodo de validez
                maxsize         : '',              // El tamaño máximo del archivo requerido
                                                   // o vacio si puede ser cuqluiere
                has_expiration_date : false,           // Si el firmante debe introducir fecha de caducidad del documento
                notify          : false,           // Recibir notificaciones antes de que el documento expire
            };
         },

        /**
         * Adiciona un documento a la lista de documentos
         */
        addDocument: function () {
            const $this = this;

            // chekeo por el nombre del documento en el formulario
            $this.$v.document.$touch();
            if ($this.$v.document.$anyError) {
                toastr.error($this.messages.nonSelectedDoc);
                return;
            }

            // En caso que estoy editando debo eliminar el anterior y guardar la modificación
            $this.removeRequest($this.document.name);

            $this.documents.push($this.document);
            $this.resetDocument();

            toastr.info($this.messages.requestSaved);
            $this.editing = false;

            // guardo los documentos en el localStorage
            localStorage.setItem('requests-documents', JSON.stringify(this.documents));
        },

        /**
         * Muestra el tiempo de validez de un documento según lo seleccionado por el usuario
         */
        showValidity: function (document) {
            const validity = document.validity;
            if (validity == 0) {
                return '';
            }

            let unity    = '';
            
            if ( parseInt(document.validity_unit) == 30) {
                unity = this.texts.month;
                unity = validity > 1? this.texts.months :  unity;
            } else if (parseInt(document.validity_unit) == 365) {
                unity = this.texts.year;
                unity = validity > 1? this.texts.years :  unity;
            } else {
                unity = this.texts.day;
                unity = validity > 1? this.texts.days :  unity;

            }

            return `${validity} ${unity}`;
        },
        
        /** devuelve el tamanno de los archivos plugin filesize */
        getFileSize(size) {
            return  size == 0? '' : filesize(size, { bits: false });
        },

        /**
         * Elimina una solicitud del listado que he adicionado
         */
        removeRequest: function(name) {
            let docs= [];
            this.documents.forEach( document => {
                if (document.name === name) {
                } else {
                    docs.push( document );
                }
            });
            this.documents = [];
            this.documents = docs;

            // guardo los documentos en el localStorage
            localStorage.setItem('requests-documents', JSON.stringify(this.documents));
        },

        /**
         * Carga los datos del Request seleccionado para ser modificado
         */
        editRequest: function(name) {
            // Busco el Request por su nombre
            this.documents.forEach( document => {
                if (document.name === name) {
                    this.document = document;
                }  
            });
            // Lo elimino de la lista de Solicitudes
            this.removeRequest(this.document.name);
            // Establezco que estoy editando
            this.editing = true;
        },

        /** 
         * Verifica si el listado de documentos a requerir esta vacío
         */
        documentsEmpty() {
            return this.documents.length == 0;
        },

        /** 
         * Salva la solicitud de documentos
         */
        saveRequest: function () {
            const $this = this;

            // chekeo por la solicitud
            $this.$v.request.$touch();
            if ($this.$v.request.$anyError) {
                toastr.error($this.messages.unCompleteRequest);
                return;
            }

            // Debe haber minimo un documento a solicitar
            if (!$this.documents.length) {
                return;   
            }

            const saveDocumentRequest = event.target.dataset.saveDocumentRequest;
            const afterSaveRedirectTo = event.target.dataset.afterSaveRedirectTo;

            axios.post(saveDocumentRequest, 
                {
                    id          : $this.id,
                    name        : $this.request.name,
                    comment     : $this.request.comment,
                    documents   : $this.documents,
                }
            )
            .then(response => {
                // Obtiene el id de la solicitud de documentos
                let id = response.data.id;

                // limpio los documenos
                $this.documents = [];
                localStorage.removeItem('requests-documents');

                // Redirige a la vista para seleccionar los usuarios "firmantes",
                // que en este caso serán los usuarios que deben remitir los deocumentos requeridos
                location.href = `${afterSaveRedirectTo}/${id}`;
            })
            .catch(error => {
                console.error(error);
            });

        }

    },
    computed: {
        /**
         * Si el botón de guardar debe estar habilitado o no
         * 
         * @return {Boolean}
         */
        saveButtonDisabled: function () {
            // Al menos debe existir un documento con un nombre
            return !this.documents.length
                        ||
                   this.documents.filter(document => document.name == '').length == this.documents.length;
        },

        /**
         * Devuelve true si está seleccionada la unidad de tiempo dias
         */
        isDaysSelected: function () {
            return this.document.validity_unit === this.TimeUnit.Days;
        },

        /**
         * Devuelve true si está seleccionada la unidad de tiempo meses
         */
        isMonthsSelected: function () {
            return this.document.validity_unit === this.TimeUnit.Months;
        },

        /**
         * Devuelve true si está seleccionada la unidad de tiempo anos
         */
        isYearsSelected: function () {
            return this.document.validity_unit === this.TimeUnit.Years;
        },

    },
});

  