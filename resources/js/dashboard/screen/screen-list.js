/**
 * Listado de grabaciones de pantalla
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

// register the component
Vue.component('Treeselect', VueTreeselect.Treeselect)
 
new Vue({
    el: '#app',

    components: {
    },
    
    mixins: [
        validationMixin
    ],

    data: {
    	capture: null, 								// La captura que voy a eliminar
        editing: {									// La captura que se está editando
                    id       : -1,
                    filename : '',
                    type     : '',
                    size     : -1,
                    file     : null,
                    video    : null,
                    playing  : false,
                    path : null,
                    duration : 0,
                },                                  
        captures: [],                               // Listado de capturas realizadas
        optionsFikreaCloud: [],                     // Opciones para el componente árbol

        paginate: ['captures'],						// Paginado de las grabaciones
        messages: [],								// Los mensajes de la app
        requests: [],								// Los rutas necesarias
    },

    // Validaciones para Vuelidate
    validations: {

        // validaciones para la captura que estoy editando
        editing: {
            filename: {
                required,
                minLength: 3,
            },
        },
    },
 
    /**
     * Cuando la instancia está montada
     */
    mounted: function () {
        const $this = this;
        
        // Los mensajes de la app
        $this.messages = document.getElementById('messages').dataset;

        // Las rutas
        $this.requests = document.getElementById('requests').dataset;

        // El sistema de carpetas de fikrea para el usuario
        $this.optionsFikreaCloud = JSON.parse(document.getElementById('data').dataset.fileSystemTree);

        // Las capturas que tiene el usuario guardadas
        $this.captures = JSON.parse(document.getElementById('data').dataset.captures);
        $this.captures.forEach( capture => {
        	capture.video = `data:video/mp4;base64,${capture.base64}`;
        });
    },

    methods: {

        /*
         * Inicializa el objeto que controla la captura de pantalla que se edita
         */
        resetEditing: function() {
            this.editing =
                {
                    id       : -1,
                    filename : '',
                    type     : '',
                    size     : -1,
                    file     : null,
                    video    : null,
                    playing  : false,
                    path : null,
                    duration : 0,
                };
        },


        // Valida la edición de la captura
        validateCaptureState: function(name) {
          const { $dirty, $error } = this.$v.editing[name];
          return $dirty ? !$error : null;
        },

        /*
         * devuelve el tamaño de los archivos mediante el plugin filesize
         */
        getFileSize(size) {
            return  size == 0? '' : filesize(size, { bits: false });
        },

        /*
         * Edita una captura realizada del listado de capturas
         */
        editCapture: function (capture) {
            this.capture = capture;
            Object.assign(this.editing, this.capture);

            if (this.editing.filename) {
                // elimino el substring .mp4 del nombre del archivo
                this.editing.filename = this.editing.filename.replace('.mp4', '');

                // Abre la modal para editar la captura
                this.$bvModal.show('edit-capture-screen');
            } else {
                toastr.error(this.messages.captureNotFound);
            }
        },

        /*
         * Actualiza una captura realizada
         */
        refreshCapture: function () {
            this.capture.filename = `${this.editing.filename}.mp4`; // adiciono la extensión del archivo
            this.capture.path = this.editing.path;

            this.editing.filename += '.mp4';
            this.editing.base64 = null;
            this.editing.video = null;

            // Petición para actualizar la captura en la db
            const request = this.requests.updateRequest.replace('X', this.capture.id);

            HoldOn.open({theme: 'sk-circle'});
            axios.post(request, {
            	'capture' : this.editing
            }).then(resp => {
            	if (resp.data.code == 1) {
            		// limpio el objeto que controla el objeto que estoy editando
		            this.resetEditing();

		            // cierro la modal
		            this.$bvModal.hide('edit-capture-screen');

		            HoldOn.close();

		            toastr.info(this.messages.editingOk);
            	}
            	
            }).catch(error => {
            	console.log(error);
            	HoldOn.close();
            });
        },

        deleteCapture: function (capture) {
        	console.log('Eliminando captura');
        	console.log(capture);

        	this.capture = capture;

        	// Abre la modal para confirmar eliminar la captura
            this.$bvModal.show('delete-capture');
        },

        destroyCapture: function () {

        	// Petición para eliminar la captura de la db
            const request = this.requests.destroyRequest.replace('X', this.capture.id);

        	HoldOn.open({theme: 'sk-circle'});
            axios.post(request)
            .then(resp => {
            	// cierro la modal
		        this.$bvModal.hide('delete-capture');

            	if (resp.data.code == 1) {
		            HoldOn.close();

		            // Filtro las caturas y desecho la recibida por parámetro
            		this.captures = this.captures.filter((_capture) => this.capture.id != _capture.id);

		            toastr.info(this.messages.deletingOk);
            	} else {
					toastr.error(resp.data.message);
            	}
            	
            }).catch(error => {
            	console.log(error);
            	HoldOn.close();
            });

        },

        /**
         * Convierte un objeto Blob en base 64
         * 
         * @param {Blob} blob       Un objeto Blob 
         * 
         * @return {Promise}        Una promesa con el objeto blob convertido a base 64
         */
        convertBlobToBase64: function (blob) {
            const reader = new FileReader();
            reader.readAsDataURL(blob);

            return new Promise((resolve, reject) => {
                
                reader.onloadend = () => {
                    resolve(reader.result);
                };

                reader.onerror = error => {
                    reject(error);
                }
            });
        },
        
    },

    computed: {
        
    },
});
 
// ir al inicio de la página siempre al cargar
if (history.scrollRestoration) {
    history.scrollRestoration = 'manual';
} else {
    window.onbeforeunload = function () {
        window.scrollTo(0, 0);
    }
} 