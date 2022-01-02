/**
 * Configuración del usuario
 * 
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 * 
 * Para el componente de firma que se desarolla sobre el elemento canvas vease:
 * 
 * @link https://github.com/szimek/signature_pad
 * 
 * 
 */

const { default: Axios } = require("axios");


new Vue({
    el: '#app',
    data: {

        //
        // La configuración del usuario
        //

        // Configuración de firma manuscrita
        sign  :
            {
                sign             : document.getElementById('sign-image').getAttribute('src'),
                useAsDefault     : document.getElementById('use-as-default').checked,
            },

        // Configuración de las grabaciones de audio
        audio : 
            {
                text    : document.getElementById('audio-text').value,
                sample  : document.getElementById('audio-sample').dataset.src,

                recording       : false,                                // true si está grabando audio, false en caso contrario
                mediaRecorder   : null,                                 // El mediarecorder
                                            
                timer   : null,                                         // El cronómetro
                time    : 0,                                            // El tiempo en segundos de la grabación
                maxTime : document.getElementById('audio')
                            .dataset.maxRecordTime,                     // El máximo tiempo que se puede grabar en segundos
        

            },
        // Configuración de las grabaciones de video
        video : 
            {
                text: document.getElementById('video-text').value,

                sample  : document.getElementById('video-sample').dataset.src,

                recording       : false,                                // true si está grabando video, false en caso contrario
                mediaRecorder   : null,                                 // El mediarecorder
                                            
                timer   : null,                                         // El cronómetro
                time    : 0,                                            // El tiempo en segundos de la grabación
                maxTime : document.getElementById('video')
                            .dataset.maxRecordTime,                     // El máximo tiempo que se puede grabar en segundos
            },

        // Configuración de los documentos identificativos
        identificationDocument:
            {
                useFacialRecognition : document.getElementById('use-facial-recognition').checked,
            },
        // Configuración de la perioricidad de las notificaciones
        notification :
            {
                // Envío automático de recuerdo del proceso de firma y validación a firmantes
                days    : document.getElementById('notifications').dataset.selected,
                // Recivir noticiaciones por Email o SMS
                receive : document.getElementById('receive-notifications').checked,
            },
        //
        // El Pad de Firma
        //
        pad   : null,
    },
    /**
     * Cuando la instancia está montada
     * 
     */
    mounted: function () {

        const $this = this;

        // Fija el selector del periodo de las notificaciones
        document.querySelector(`#notifications [value="${$this.notification.days}"]`).selected;

        // El Canvas de firma
        const signCanvas = document.getElementById('sign');

        // Si exite ya una firma la muestra
        if ($this.sign.sign) {
                        
            // Carga la imagen de la firma
            let image = new Image();
            image.src = $this.sign.sign;

            // Cuando la imagen ha sido cargada, se debuja sobre el canvas
            image.onload = () => signCanvas.getContext('2d').drawImage(image, 0, 0);
        }

        $this.pad = new SignaturePad(signCanvas, {
            /**
             * Cuando se termina de trazar la firma
             *
             */
            onEnd: function () {
                // Se guarda la firma en base 64
                $this.sign.sign = this.toDataURL();
            }
        });

        window.addEventListener('resize', $this.resizeCanvas);
        $this.resizeCanvas();
    },
    methods: {
        /**
         * Redimensiona el elemento canvas
         * 
         * Hace que el elemento de firma (canvas) sea adaptable
         * Al cambiar las deimensiones de la pantalla hayq eu redimensionar convenientemente el elemento
         * ya que, en caso, contrario, el trazado no puede dibujarse bien.
         * 
         * Al realizar esta operación el canvas debe ser borrado y posteriomente recreado 
         * Para entender mejor este proceso se puede consultar la documentación de referencia:
         * 
         * @link https://github.com/szimek/signature_pad
         *
         * @return {Void}
         * 
         */
        resizeCanvas: async function() {
           
            const $this = this;

            let signCanvas   = document.getElementById('sign');
            let signaturePad = new SignaturePad(signCanvas);
            
            signCanvas.width  = signCanvas.offsetWidth;
            signCanvas.height = signCanvas.offsetHeight;
            
            // Borrar el canvas
            signaturePad.clear();

            // Si exite ya una firma, hay que volverla a cargar la imagen en el canvas
            if ($this.sign.sign) {

                // Redimensiona la imagen para ajustarla a la dimensión del canvas
                let resized = await this.resizeImage($this.sign.sign, signCanvas.width, signCanvas.height);
                signCanvas.getContext('2d').drawImage(resized, 0, 0);
            }
        },
        /**
         * Redimensiona una imagen
         * 
         * @param {String} data             Los datos de la imagen en formato Base64
         * @param {Number} wantedWidth      El ancho de la imagen requerido en píxeles
         * @param {Number} wantedHeight     El alto de la imagen requerido en píxeles
         *  
         * @return {Promise}                Una promesa con un objeto Image redimensionado
         * 
         */
        resizeImage: function (data, wantedWidth, wantedHeight) {

            return new Promise(resolve => {

                // Creamos una nueva Imagen
                let image = new Image();

                // y cargamos a la imagen a redimensionar
                image.src = data;

                // Cuando la imagen está cargada
                image.onload = () =>
                    {        
                        // Creamos un nuevo elemento canvas para efectuar la operación
                        let canvas = document.createElement('canvas');
                        let ctx    = canvas.getContext('2d');

                        // y lo dimensionamos con las dimensiones requeridas
                        canvas.width  = wantedWidth;
                        canvas.height = wantedHeight;

                        // Dibujamos la imagen
                        ctx.drawImage(image, 0, 0, wantedWidth, wantedHeight);

                        image.src = canvas.toDataURL();

                        // Devolvemos la imagen redimensionada
                        image.onload = () => {
                            resolve(image);
                        };
                    };

            });
        },
        /**
         * Guarda la configuración del usuario
         * 
         * @return {Void}
         *
         */
        saveConfig: function () {
            
            const $this = this;

            // Obtiene la ruta para realizar la solicitud para guardar la configuración del usuario
            const request = document.querySelector('form').action;

            // Obtiene la redirección tras guardar la configuración del usuario
            const afterSaveRedirectTo = document.querySelector('form').dataset.afterSaveRedirectTo;

            HoldOn.open({theme: 'sk-circle'});

            Axios.post(request, 
                {
                    sign                        : $this.sign,
                    audio                       : 
                        {
                            text    : $this.audio.text,
                            sample  : $this.audio.sample,
                        },
                    video                       : 
                        {
                            text    : $this.video.text,
                            sample  : $this.video.sample,
                        },
                    identificationDocument      : $this.identificationDocument,
                    notification                : $this.notification,
                }
            )
            .then(() => {
                HoldOn.close();
                location.href = afterSaveRedirectTo;
            })
            .catch(error => {
                HoldOn.close();
                console.error(error);
            });
        },
        /**
         * Elimina la firma del canvas
         * 
         * @return {Void}
         *
         */
        clearSign: function () {
            this.pad.clear();
            this.sign.sign = null;
        },
        /**
         * Inicia o detiene la grabación de audio
         *
         */
        recordAudio: function () {
            // Inicia la grabación o la detiene
            this.audio.recording = !this.audio.recording;

            // Al iniciar la grabación
            if (this.audio.recording) {
                this.startAudioRecording();
            } else {
                this.stopAudioTimer();
            }
        },
        /**
         * Inicia la grabación de audio
         *
         */
        startAudioRecording: function () {

            const $this = this;

            const options = {};
            const recordedChunks = [];
    
            navigator.mediaDevices
                .getUserMedia({ audio: true, video: true })
                .then(stream =>
                    {
                        // Se pone a cero el tiempo de grabación
                        $this.audio.time  = 0;

                        // Se inicia el cronómetro que se actualiza cada segundo
                        $this.audio.timer = setInterval(this.updateAudioTimer, 1000);

                        $this.audio.mediaRecorder = new MediaRecorder(stream, options);  
    
                        $this.audio.mediaRecorder.addEventListener('dataavailable', function (event) {
                            if (event.data.size > 0) {
                                recordedChunks.push(event.data);
                            }
                        });
                    
                        $this.audio.mediaRecorder.addEventListener('stop', async function () {
                                        
                            // Convierte los datos de audio en base 64 en un objeto blob
                            let blob = new Blob(recordedChunks, {
                                type: 'audio/x-wav'
                            });
                     
                            // Obtiene el audio en base 64
                            $this.audio.sample = await $this.convertBlobToBase64(blob);     
                        });
                    
                        $this.audio.mediaRecorder.start();
                    }
                );
        },
        /**
         * Actualiza el cronómetro de grabación de audio
         *
         */
        updateAudioTimer: function () {
            ++this.audio.time;

            // Si se supera el tiempo máximo de grabación de audio se detiene el cronómetro
            if (this.audio.time >= this.audio.maxTime) {
                this.stopAudioTimer();
            }
        },
        /**
         * Detiene el cronómetro de la grabación de audio
         * 
         */
        stopAudioTimer: function () {
            // Detiene el cronómetro
            clearInterval(this.audio.timer);

            // Detiene la grabación
            this.audio.mediaRecorder.stop();
        },
        /**
         * Elimina la grabación de audio
         * 
         * @return {Void}
         * 
         */
        removeAudio: function () {
            this.audio.sample = null;
        },
        /**
         * Inicia o detiene la grabación de video
         *
         */
        recordVideo: function () {
            // Inicia la grabación o la detiene
            this.video.recording = !this.video.recording;

            // Al iniciar la grabación
            if (this.video.recording) {
                this.startVideoRecording();
            } else {
                this.stopVideoTimer();
            }
        },
        /**
         * Inicia la grabación de video
         *
         */
        startVideoRecording: function () {

            const $this = this;

            // Elimina la grabación de video que pudiera existir previamente
            $this.video.sample = null;

            const options = {};
            const recordedChunks = [];
    
            navigator.mediaDevices
                .getUserMedia({ audio: true, video: true })
                .then(stream =>
                    {
                        // Se pone a cero el tiempo de grabación
                        $this.video.time  = 0;

                        // Se inicia el cronómetro que se actualiza cada segundo
                        $this.video.timer = setInterval(this.updateVideoTimer, 1000);

                        $this.video.mediaRecorder = new MediaRecorder(stream, options);

                        // Muestra la captura de video en tiempo real
                        const videoElement = document.getElementById('video-sample');
                        videoElement.srcObject = stream;
                        videoElement.play();

                        $this.video.mediaRecorder.addEventListener('dataavailable', function (event) {
                            if (event.data.size > 0) {
                                recordedChunks.push(event.data);
                            }
                        });
                    
                        $this.video.mediaRecorder.addEventListener('stop', async function () {
                                        
                            // Convierte los datos de audio en base 64 en un objeto blob
                            let blob = new Blob(recordedChunks, {
                                type: 'video/mp4'
                            });
                     
                            // Obtiene el audio en base 64
                            $this.video.sample = await $this.convertBlobToBase64(blob);     
                        });
                    
                        $this.video.mediaRecorder.start();
                    }
                );
        },
        /**
         * Actualiza el cronómetro de grabación de video
         *
         */
        updateVideoTimer: function () {
            ++this.video.time;

            // Si se supera el tiempo máximo de grabación de audio se detiene el cronómetro
            if (this.video.time >= this.video.maxTime) {
                this.stopVideoTimer();
            }
        },
        /**
         * Detiene el cronómetro de la grabación de video
         * 
         */
        stopVideoTimer: function () {
            // Detiene el cronómetro
            clearInterval(this.video.timer);

            // Detiene la grabación
            this.video.mediaRecorder.stop();
        },
        /**
         * Elimina la grabación de audio
         * 
         * @return {Void}
         * 
         */
        removeVideo: function () {
            this.video.sample = null;
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
         * Muestra la modal para guardar la validacion
         * 
         * @param {Event} event
         *  
         */
        saveConfigModal: function (id) {
            this.$bvModal.show('save-config-modal');
        },
    },
    computed: {
        /**
         * Muestra la representación del cronómetro de la grabación de audio en el formato mm:ss
         *
         */
        showAudioTimer: function (event) {
            return moment.utc(this.audio.time * 1000).format('mm:ss');
        },
        /**
         * Muestra la representación del cronómetro de la grabación de video en el formato mm:ss
         *
         */
        showVideoTimer: function (event) {
            return moment.utc(this.video.time * 1000).format('mm:ss');
        },
    }
});
