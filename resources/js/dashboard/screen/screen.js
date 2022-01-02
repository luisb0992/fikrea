/**
 * Creación de archivo de video mediante grabación de la pantalla
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
        recording       : false,                                // true si está grabando video, false en caso contrario
        mediaRecorder   : null,                                 // El mediarecorder
        timer   : null,                                         // El cronómetro
        time    : 0,                                            // El tiempo en segundos de la grabación
        position    :               // La posición del usuario firmante datum WGS84
            {
                latitude : null,
                longitude: null
            },
        capture: null,                              // La captura actual
        editing: {
                    id       : -1,
                    filename : '',
                    type     : '',
                    size     : -1,
                    file     : null,
                    video    : null,
                    playing  : false,
                    path : null,
                    duration : 0,
                },                                  // La captura que se está editando
        captures: [],                               // Listado de capturas realizadas
        messages: [],                               // Los mensajes de la app
        requests: [],                               // Las urls de las peticiones
        optionsFikreaCloud: [],                     // Opciones para el componente árbol
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

        // Intentamos obtener la geolocalización del firmante
        $this.getCurrentPosition()
            .then(() => {
                console.info(`[INFO] Se ha obtenido la posición del firmante con éxito`);
            })
            .catch(() => {
                console.info(`[INFO] No se ha podido obtener la posición del firmante`);
            });

        $this.messages = document.getElementById('messages').dataset;
        $this.requests = document.getElementById('requests').dataset;

        $this.optionsFikreaCloud = JSON.parse(document.getElementById('data').dataset.fileSystemTree);
    
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

        /**
         * Inicia o detiene la grabación de video
         */
        recordCapture: function () {
            // Inicia la grabación o la detiene
            this.recording = !this.recording;

            // Al iniciar la grabación
            if (this.recording) {
                this.startRecording();
            } else {
                this.stopTimer();
            }
        },

        /**
         * Inicia la grabación de captura de pantalla
         */
        startRecording: function () {

            const $this = this;

            const options = { audio: true, video: true };
            $this.recordedChunks = [];

            navigator.mediaDevices.
                getDisplayMedia(options)
                .catch(error => {
                    // Si el firmante ha cancelado compartir su pantalla
                    if (error.name === 'NotAllowedError') {
                        // Inicia la grabación o la detiene
                        this.recording = false;
                    }
                }).then(stream => {
                    // Si el usuario no ha cancelado la compartición de pantalla en el navegador
                    if (stream !== undefined)  {
                        // Se pone a cero el tiempo de grabación
                        $this.time  = 0;

                        // Se inicia el cronómetro que se actualiza cada segundo
                        $this.timer = setInterval(this.updateTimer, 1000);

                        $this.mediaRecorder = new MediaRecorder(stream, options); 

                        // Muestra la captura de video en tiempo real
                        const videoElement = document.getElementById('video-screen');
                        videoElement.srcObject = stream;

                        $this.mediaRecorder.addEventListener('dataavailable', function (event) {
                            if (event.data.size > 0) {
                                $this.recordedChunks.push(event.data);        
                            }
                        });

                        $this.mediaRecorder.addEventListener('stop', async function () {
                            
                            // Obtiene un id único para el archivo de video creado
                            let id        = Math.random().toString(36).substr(2, 9);
                            let filename  = `${id}.mp4`;
                            let filetype  = 'video/mp4';
                        
                            // Convierte los datos de video en base 64 en un objeto blob
                            let blob       = new Blob($this.recordedChunks)
                            let filesize   = Math.round(blob.size / 1024);
                            const duration = moment.utc($this.time * 1000).format('mm:ss')

                            const file = await $this.convertBlobToBase64(blob);

                            $this.capture =
                                {
                                    id       : id,
                                    filename : filename,
                                    type     : filetype,
                                    size     : filesize,
                                    file     : file,
                                    token    : `${id}${Math.random().toString(36)}`,
                                    video    : URL.createObjectURL(blob),
                                    playing  : false,
                                    saved    : false,         // Si se ha guardado en el servidor
                                    path     : null,          // Ubicación en filesystem de nube fikrea
                                    duration : duration,
                                };

                            // Guardo temporalmente esta captura en el servidor
                            axios.post($this.requests.saveScreen, {
                                'capture' : $this.capture
                            }).then(response => {
                                // marcar la captura como guardada
                                try {
                                    $this.captures.filter(
                                            _capture => _capture.token == response.data.token
                                        )[0].saved = true;
                                } catch (error) {
                                    console.error(error);
                                }
                                
                            }).catch(error => {
                                console.error(error);
                            });


                            $this.captures.push($this.capture);
                            toastr.success($this.messages.captureRecordSuccess);
                            $this.capture = null;
                        });
                    
                        // Elimino todos los mensajes toast que puedan estar mostrándose
                        toastr.clear();

                        // Comienzo a grabar
                        $this.mediaRecorder.start();
                        toastr.success(this.messages.recording);
                    }
                    
                });
        },

        /*
         * devuelve el tamaño de los archivos mediante el plugin filesize
         */
        getFileSize(size) {
            return  size == 0? '' : filesize(size, { bits: false });
        },

        /*
         * Elimina una captura realizada del listado de capturas
         */
        removeCapture: function (capture) {
            // Filtro las caturas y desecho la recibida por parámetro
            this.captures = this.captures.filter((_capture) => capture.id != _capture.id);
        },

        /*
         * Edita una captura realizada del listado de capturas
         */
        editCapture: function (capture) {
            this.capture = this.captures.filter(_capture => _capture.id == capture.id)[0];
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
            console.log("Refreshing capture");

            this.capture.filename = `${this.editing.filename}.mp4`; // adiciono la extensión del archivo
            this.capture.path = this.editing.path;

            /// limpio el objeto que controla el objeto que estoy editando
            this.resetEditing();

            // cierro la modal
            this.$bvModal.hide('edit-capture-screen');

            toastr.info(this.messages.editingOk);
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
         * Actualiza el cronómetro
         *
         */
        updateTimer: function () {
            ++this.time;
        },

        /**
         * Detiene el cronómetro
         */
        stopTimer: async function () {
            // Detiene el cronómetro
            clearInterval(this.timer);

            // Detiene la grabación
            await this.mediaRecorder.stop();
            
            // Detiene el proceso de captura de pantalla
            const videoElement = document.getElementById('video-screen');
            const track        = videoElement.srcObject.getVideoTracks().pop();

            track.stop();
        },

        /*
         * Guarda todas las capturas de video realizadas
         * 
         * Se envían solo los datos de video de las capturas
         * que no se han marcado como enviadas
         */
        save: function (event) {
            // Preparo los datos a enviar
            let data = [];
            this.captures.forEach(_capture => {
                // Clono la captura actual
                let captureTmp = {};
                Object.assign(captureTmp, _capture);

                // Desecho la info en file si ya ha sido guardado en el servidor
                if (captureTmp.saved) {
                    captureTmp.file = '';
                    captureTmp.video = '';
                }

                data.push(captureTmp);
            });

            HoldOn.open({theme: 'sk-circle'});
            // Guardo temporalmente esta captura en el servidor
            axios.post(event.target.dataset.saveRequest, {
                'captures' : data
            }).then(response => {
                HoldOn.close();
                if (response.data.code == 1) {
                    location.href = event.target.dataset.redirectRequest;
                }
            }).catch(error => {
                console.error(error);
                HoldOn.close();
            });
        },
        
    },

    computed: {
        /**
         * Muestra la representación del cronómetro en el formato mm:ss
         */
        showTimer: function(event) {
            return this.time && this.recording ? moment.utc(this.time * 1000).format("mm:ss") : "";
        },
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