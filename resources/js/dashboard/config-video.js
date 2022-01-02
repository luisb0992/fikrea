/**
 * Grabación de video para la validación de un documento
 * 
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

const { default: Axios } = require("axios");

new Vue({
    el: '#app',
    data: {
        messages: document.getElementById('messages').dataset,  // Los mensajes
        
        recording       : false,                                // true si está grabando video, false en caso contrario
        mediaRecorder   : null,                                 // El mediarecorder
                                                    
        timer   : null,                                         // El cronómetro
        time    : 0,                                            // El tiempo en segundos de la grabación
        maxTime : document.getElementById('video')
                    .dataset.maxRecordTime,                     // El máximo tiempo que se puede grabar en segundos

        videos  : [],                                           // Una lista de videos grabados
    },
    /**
     * Cuando tenemos la instancia montada
     *
     */
    mounted: function () {
        
    },
    methods: {
        /**
         * Inicia o detiene la grabación de video
         *
         */
        recordVideo: function () {
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
         * Inicia la grabación de video
         *
         */
        startRecording: function () {

            const $this = this;

            const options = {};
            const recordedChunks = [];
    
            navigator.mediaDevices
                .getUserMedia({ audio: true, video: true })
                .then(stream =>
                    {
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
                                recordedChunks.push(event.data);        
                            }
                        });

                        $this.mediaRecorder.addEventListener('stop', async function () {
                            
                            // Obtiene un id único para el archivo de video creado
                            let id        = Math.random().toString(36).substr(2, 9);
                            let filename  = `${id}.mp4`;
                            let filetype  = 'video/mp4';
                        
                            // Convierte los datos de video en base 64 en un objeto blob
                            let blob       = new Blob(recordedChunks)
                            let filesize   = Math.round(blob.size / 1024);
                            const duration = moment.utc($this.time * 1000).format('mm:ss')
                
                            const file = await $this.convertBlobToBase64(blob);

                            const video =
                                {
                                    id       : id,
                                    filename : filename,
                                    type     : filetype,
                                    size     : filesize,
                                    file     : file,
                                    video    : URL.createObjectURL(blob),
                                    playing  : false,
                                    duration : duration,
                                };

                            // Solo se mantiene un solo archivo de video
                            $this.videos = [];
                            $this.videos.push(video);

                            toastr.success($this.messages.videoRecordSuccess);

                        });
                    
                        $this.mediaRecorder.start();
                    }
                );
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
         * Actualiza el cronómetro
         *
         */
        updateTimer: function () {
            ++this.time;

            // Si se supera el tiempo máximo de grabación de video se detiene el cronómetro
            if (this.time >= this.maxTime) {
                this.stopTimer();
            }
        },
        /**
         * Detiene el cronómetro
         * 
         */
        stopTimer: function () {
            // Detiene el cronómetro
            clearInterval(this.timer);

            // Detiene la grabación
            this.mediaRecorder.stop();
        },
        /**
         * Suprime la grabación de video
         * 
         * @param {video} video     La grabación de video
         */
        removeVideo: function(video) {
             this.videos = this.videos.filter(_video => video.id != _video.id);
        },
        /**
         * Guarda la validación por video
         *
         * @param {Event} event
         */
        save: function (event) {

            const $this = this;
            
            // Obtiene la url para guardar las grabaciones de video y la url de la redirección
            // tras el proceso de guardado 
            const request = 
                {
                    save     : event.currentTarget.dataset.saveRequest,
                    redirect : event.currentTarget.dataset.redirectRequest,
                };

            HoldOn.open({theme: 'sk-circle'});
            
            // Envía la grabacion de video
            axios.post(request.save,
                {
                    video      : $this.videos[0],     // La grabacion de video
                }
            )
            .then((response) => {
                HoldOn.close();
                if( response.data.code === 1){
                    location.href = request.redirect;
                } else {
                    // Mostrar mensaje de error
                }
            })
            .catch(error => {
                HoldOn.close();
                console.error(error);
            });
        }
    },
    computed : {
        /**
         * Muestra la representación del cronómetro en el formato mm:ss
         *
         */
        showTimer: function (event) {
            return moment.utc(this.time * 1000).format('mm:ss');
        }
    }
});