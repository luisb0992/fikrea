 
/**
 * Controla la firma multiple de archivos
 * 
 * @author    rosellpp <rosellpp@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

const { default: Axios } = require('axios');

Vue.config.productionTip = false;

// Para usar Vuelidate desde assets
const validationMixin = window.vuelidate.validationMixin;
const { required, minLength } = window.validators;

Vue.use(window.vuelidate.default);

new Vue({
    el: '#app',

    mixins: [
        validationMixin
    ],

    data: {
        files   : [],                   // Listado de archivos firmables
        show    : false,                // Para mostrar formulario donde se completa el proceso
        document: {},                   // Info del documento que se genera
        
        file    : {
            id  : null,                 // El id del archivo que se genera
            name:'',                    // Para nombre y ubicacion del archivo generado
            location:'',                // Location del archivo en el sistema de carpetas
        },                   
    },

    // Validaciones para Vuelidate
    validations: {

        // validaciones para el archivo
        file: {
            id: {
                required,
            },
            name: {
                required,
            },
        },
        
    },

    /**
     * Carga la tabla de firmantes
     */
    mounted: function () {
        this.files = JSON.parse(document.getElementById('data').dataset.files);
        
        // Chequeo si la data esta en el localStorage
        this.show = localStorage.getItem('show')? localStorage.getItem('show') : false;
        this.document = localStorage.getItem('document-multiple')? JSON.parse(localStorage.getItem('document-multiple')) : {};

        localStorage.removeItem('show');
        localStorage.removeItem('document-multiple');
    },
    methods: {

        // Valida el campo name con vuelidate según reglas de validación establecidas en validations
        validateState: function(name) {
          const { $dirty, $error } = this.$v.file[name];
          return $dirty ? !$error : null;
        },

        /*
         * Envía el documento a firmar, al proceso de seleccionar los firmantes
         */
        gotoSigners: function () {
            const $this = this;

            // chekeo por el nombre del file en el formulario
            $this.$v.file.$touch();
            if ($this.$v.file.$anyError) {
                toastr.error(document.getElementById('data').dataset.messageInvalidForm);
                return;
            }

            if (!$this.document) {
                console.error("Algo ha pasado, por favor revise");
                return;
            }

            let request = document.getElementById('data').dataset.requestSelectSigners;
            request = request.substr(0, request.length-1)
            request = `${request}${$this.document.id}`;

            let saveRequest = document.getElementById('data').dataset.requestSaveFileInfo;
            saveRequest = saveRequest.substr(0, saveRequest.length-1)
            saveRequest = `${saveRequest}${$this.file.id}`;

            // Inicia la animación
            HoldOn.open({theme: 'sk-circle'});

            // Envío el nombre del archivo y el location dentro del sistem de carpetas
            axios.post(saveRequest, {
                name:     $this.file.name,              // El nombre ue tendrá el archivo
                location: $this.file.location,          // El id de la carpeta que será su padre en el arbol jerárquico
            }).then(response => {
                // Oculta la animación
                HoldOn.close();
                if (response.data.code == 1) {
                    localStorage.removeItem('show');
                    localStorage.removeItem('document-multiple');

                    location.href = request;
                }
            }).catch(error => {
                console.error(error);
                // Oculta la animación
                HoldOn.close();
            });
        },

        /*
         * Elimina un archivo con nombre 'name' de la lista de archivos seleccinados
         * para la firma múltiple
         */
        removeFile: function (name) {
            var index = this.files.findIndex( file => file.name === name);
            if (index != -1) {
                // Si he encontrado el archivo en la lista elimino la fila donde se muestra en la tabla
                // y el archivo de la lista de archivos
                document.querySelector(`table tr[data-file-id="${this.files[index].id}"]`).remove();
                this.files.splice(index, 1);
            }
            this.hideTooltips();
        },

        /*
         * Oculta todos los tooltips
         */
        hideTooltips: function () {
            document.querySelectorAll('.tooltip.fade.show.bs-tooltip-bottom').forEach(tooltip=>{tooltip.remove()});
        },

        /*
         * Envía los archivos firmables a ser procesados en un documento que podrá ser proceso
         * como el resto de los archivos, el mismo proceso de config
         */
        gotoSignMultiple: function() {
            const $this = this;

            const files = $this.files.filter(file => file.signable === true).map(file => file.id);
            const request = document.getElementById('data').dataset.requestSignMultiple;

            // Inicia la animación
            HoldOn.open({theme: 'sk-circle'});

            // Envíamos y guardamos el sello en el servidor
            axios.post(
                request,
                {
                    dataFiles : files
                }
            )
            .then(response => {
                // Oculta la animación
                HoldOn.close();
                /*
                    Durante la conversión le pedimos que nos de 
                    - el nombre del archivo que nace de esa fusión y 
                    - la ubicación que quiere, para almacenarlo en mis archivos...... 
                */
                if (response.data.code == 1) {
                    // Guardo la data en el localStorage
                    localStorage.setItem('show', this.show);
                    localStorage.setItem('document-multiple', JSON.stringify(response.data.document));

                    $this.show = true;
                    $this.document = response.data.document;
                    $this.file.id = response.data.file.id ?? null;
                    toastr.info(document.getElementById('data').dataset.messageDocumentCreated);
                } else {
                    if (response.data.error) {
                        console.error(response.data.error)
                        toastr.error(response.data.error);    
                    } else {
                        toastr.error("Se ha producido un error !");
                    }
                }
                 
            })
            .catch(error => {
                // Oculta la animación
                HoldOn.close();
                console.error(error)
                toastr.error(error);
                
            });
        },

    },

    computed: {
        canSign: function () {
            return this.files.filter(file => file.signable === true).length > 0;
        }    
    },
    
 });