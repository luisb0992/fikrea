/**
 * Crea las solicitudes de documentos para los firmantes con validación 
 * de tipo Solicitud de Documento durante este proceso
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

    // Validaciones para Vuelidate
    validations: {

        // validaciones para el documento
        document: {
            name: {
                required,
            },
        },
        // validaciones para el firmante
        signer:{
            id: {
                required,
            }
        }
    }, 

    data: {

        // documento que se va a adicionar
        document: {
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
                                                       // o vacio si puede ser cualquiera
                },
        // Listado de firmantes a relacionar con el documento a requerir
        checkedSigners: [], 

        // Medida del tiempo
        TimeUnit :
            {
                Days    :   1,
                Months  :  30,
                Years   : 365,
            },

        // Los documentos requeridos de ejemplo
        documentExamples: [],

        // La lista de documentos solicitados
        documents : [],

        // Listado de firmantes
        signers : [],
        
        // Mensajes que se pueden mostrar en la vista
        messages: {},

        // Textos de dia, mes, ano para singular y plural
        texts : {},

        // Fecha inicial de emisión del documento
        dateInit: '',

        // Controla cuando estoy editando un documento
        // no se utiliza pero se utiliza en la vista compartida
        editing: false,

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

        // Firmantes
        $this.signers = JSON.parse(document.getElementById('data').dataset.signers);

        // data-message-non-selected-signer
        $this.messages.nonSelectedSigner = document.getElementById('data').dataset.messageNonSelectedSigner;
        // data-message-non-selected-doc
        $this.messages.nonSelectedDoc = document.getElementById('data').dataset.messageNonSelectedDoc;
        // data-message-request-saved
        $this.messages.requestSaved = document.getElementById('data').dataset.messageRequestSaved;
        // data-message-non-request-for-signer
        $this.messages.nonRequestForSigner = document.getElementById('data').dataset.messageNonRequestForSigner;


        // textos para validez de documento
        $this.texts = JSON.parse(document.getElementById('data').dataset.texts);

        // carga los datos en localStorage en caso de que se hayan refrescado la pagina
        if (localStorage.getItem('signers.documents')) {
            $this.documents = JSON.parse(localStorage.getItem('signers.documents'));
        }
       
    },
    /**
     * Métodos
     */
    methods: {

        /** devuelve el tamanno de los archivos plugin filesize */
        getFileSize(size) {
            return  size == 0? '' : filesize(size, { bits: false });
        },

        // Valida el campo name con vuelidate según reglas de validación establecidas en validations
        validateDocumentState: function(name) {
          const { $dirty, $error } = this.$v.document[name];
          return $dirty ? !$error : null;
        },

        // Valida el campo name con vuelidate según reglas de validación establecidas en validations
        validateState: function(name) {
          const { $dirty, $error } = this.$v.document[name];
          return $dirty ? !$error : null;
        },

        /**
         * Restablece los datos del documento 
         */
        resetDocuemnt(){
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
                                                       // o vacio si puede ser cualquiera
                };
        },

        // Restablece el formulario de creacion de un nuevo documento para un firmante
        resetForm() {
            const $this = this;
            $this.resetDocuemnt();

            $this.$nextTick(() => {
                $this.$v.$reset();
            });
        },
        
        /** 
         * Verifica si el listado de documentos a requerir esta vacío
         */
        documentsEmpty() {
            return this.documents.length == 0;
        },

        // Al enviar el formulario para relacionar un documento con un firmante (onSubmit)
        addDocument() {
            // Chekeo que se haya seleccionado algún firmante
            if (!this.checkedSigners.length) {
                toastr.error(this.messages.nonSelectedSigner);
                return;
            }

            // chekeo por el nombre del documento en el formulario
            this.$v.document.$touch();
            if (this.$v.document.$anyError) {
                toastr.error(this.messages.nonSelectedDoc);
                return;
            }

            // guardo el documento para cada firmante seleccionado
            this.checkedSigners.forEach((el)=>{
                const signer = this.signers.filter(s => s.id==el)[0];
                // chekeo que no se haya relacionado ya o
                // eliminamos de la lista de documentos 
                this.eliminaSolicitud(signer.id, this.document.name);

                this.documents.push({
                    signer: signer,
                    document: this.document,
                });
                
            });
            
            toastr.info(this.messages.requestSaved);

            // reseteo los datos
            this.resetDocuemnt();
            this.checkedSigners = [];

            // guardo en el localStorage
            localStorage.setItem('signers.documents', JSON.stringify(this.documents));

        },

        /**
         * Selecciona un período de validez determinado para un documento
         * 
         * @param {Document} document   El documento requerido seleccionado
         * @param {Number}   validity   El período de validez para ese documento
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
         * Guarda la solicitud de documentos
         *
         * @param {Event} event
         * 
         */
        saveRequests: function (event) {
            const $this = this;

            // Validamos que hayan solicitudes creadas para cada firmante
            let cannotFinishProcess = false;
            $this.signers.forEach( signer => {
                if ($this.documents.filter( req => req.signer.id == signer.id ).length == 0) {
                    cannotFinishProcess = true;
                }
            });
            if (cannotFinishProcess) {
                toastr.error($this.messages.nonRequestForSigner);
                return;
            }

            // Si ya tengo todo listo pues guardamos las solicitudes de los firmantes
            const data = {
                'requests' : $this.documents,
                'signers'  : $this.signers,
            };

            const request = event.currentTarget.dataset.redirectToSave;

            // Inicia la animación
            HoldOn.open({theme: 'sk-circle'});

            axios.post(
                request,
                data
            ).then(resp => {

                // Oculta la animación
                HoldOn.close();

                // Elimino los datos del localStorage
                localStorage.removeItem('signers.documents');

                // redirijo a la vista de documentos
                if (resp.data.code == 1) {
                    location.href = event.target.dataset.redirectToList;
                }

            })
            .catch(error => {
                // Oculta la animación
                HoldOn.close();
                console.error(error);
                // Muestra el error
                toastr.error(`${error.response.data.message}`);
            });

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

        /** 
         * Elimina una solicitud de la lista 
        */
       eliminaSolicitud: function (signerId, documentName) {
            // filtramos las solicitudes dejando las que no coinciden con la solicitud recibida por parametro
            let docs= [];
            this.documents.forEach( el => {
                if (el.signer.id === parseInt(signerId) && el.document.name === documentName) {
                } else {
                    docs.push( el );
                }
            });
            this.documents = [];
            this.documents = docs;

            // guardo en el localStorage
            localStorage.setItem('signers.documents', JSON.stringify(this.documents));
       },

    },
    /**
     * Propiedades computadas
     */
    computed: {

        /**
         * Para controlar si se muestra el texto de
         * que se debe seleccionar un firmante
         */
        showTextSignerAlert() {
            return this.checkedSigners.length > 0;
        },

        showTextNoRequests(){
            return this.documents.length == 0;
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
    }
});