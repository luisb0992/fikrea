/**
 * Crea una solicitud de documento
 * 
 * @author javieru <javi@gestoy.co>
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
        id   : document.getElementById('id').value || null,
        // El nombre de la solicitud 
        name : document.getElementById('name').value || null,
        // El comentario
        comment : null,

        // La lista de documentos solicitados
        documents : 
            [
                {
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
                }
            ],
        
        // El documento que se va a adicionar
        document : {

        }                       
    },
    /**
     * Cuando la instancia está montada
     */
    mounted: function () {
        
        const $this = this;

        // Obtiene el idioma de la página
        const lang = document.querySelector('html').getAttribute('lang');

        // Configura tinymce
        tinymce.init({
            selector    : '#comment',
            language    : lang,
            statusbar   : false,
        });

        // Carga los documentos requeridos de ejemplo
        const documentExamples = JSON.parse(document.getElementById('document-examples').dataset.documents);

        documentExamples.forEach(documentExample => $this.documentExamples.push(documentExample.name));

        // Carga la modal de ayuda
        this.showHelpModal();
       
    },
    methods: {
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
        },
        /**
         * Añade un nuevo documento
         * 
         *
         */
        addNewDocument: function () {
            this.documents.push(
                {
                    id              : Symbol(),
                    name            : '',
                    comment         : null,
                    type            : '',
                    issued_to       : null,
                    validity        : 0,
                    validity_unit   : this.TimeUnit.Days,
                    maxsize         : '',
                }
            );
        },
        /**
         * Elimina un documento
         * 
         * @param {Document} document
         * 
         */
        removeDocument: function (document) {
            // Se debe seleccionar siempre un documento como mínimo
           if (this.documents.length == 1) return;

           // Elimina el documento seleccionado
           this.documents = this.documents.filter(_document => _document.id != document.id);
        },
        /**
         * Guarda la solicitud de documentos
         *
         * @param {Event} event
         * 
         */
        save: function (event) {

            const $this = this;

            $this.comment = tinymce.get('comment').getContent();

            const saveDocumentRequest = event.target.dataset.saveDocumentRequest;
            const afterSaveRedirectTo = event.target.dataset.afterSaveRedirectTo;

            Axios.post(saveDocumentRequest, 
                {
                    id          : $this.id,
                    name        : $this.name,
                    comment     : $this.comment,
                    documents   : $this.documents,
                }
            )
            .then(response => {
                // Obtiene el id de la solicitud de documentos
                let id = response.data.id;

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
         *
         */
        saveButtonDisabled: function () {
            // El nombre de la solicitud es obligatorio
            if (!this.name) {
                return true;
            }

            // Al menos debe existir un documento con un nombre
            return !this.documents.length
                        ||
                   this.documents.filter(document => document.name == '').length == this.documents.length;
        },
    },
});

  