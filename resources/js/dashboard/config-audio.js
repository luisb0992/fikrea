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
        
        recording       : false,                                // true si está grabando audio, false en caso contrario
        mediaRecorder   : null,                                 // El mediarecorder
                                                    
        timer   : null,                                         // El cronómetro
        time    : 0,                                            // El tiempo en segundos de la grabación
        maxTime : document.getElementById('audio')
                    .dataset.maxRecordTime,                     // El máximo tiempo que se puede grabar en segundos

        audios  : [],                                           // Una lista de audios grabados
    },
    /**
     * Cuando tenemos la instancia montada
     *
     */
    mounted: function () {
    },
    methods: {
        /**
         * Inicia o detiene la grabación de audio
         *
         */
        recordAudio: function () {
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
         * Inicia la grabación de audio
         *
         */
        startRecording: function () {

            const $this = this;

            const options = {};
            const recordedChunks = [];
    
            navigator.mediaDevices
                .getUserMedia({ audio: true, video: false })
                .then(stream =>
                    {
                        // Se pone a cero el tiempo de grabación
                        $this.time  = 0;

                        // Se inicia el cronómetro que se actualiza cada segundo
                        $this.timer = setInterval(this.updateTimer, 1000);

                        $this.mediaRecorder = new MediaRecorder(stream, options);  
    
                        $this.mediaRecorder.addEventListener('dataavailable', function (event) {
                            if (event.data.size > 0) {
                                recordedChunks.push(event.data);
                            }
                        });
                    
                        $this.mediaRecorder.addEventListener('stop', async function () {
                            
                            // Obtiene un id único para el archivo de audio creado
                            let id        = Math.random().toString(36).substr(2, 9);
                            let filename  = `${id}.wav`;
                            let filetype  = 'audio/wav';
                        
                            // Convierte los datos de audio en base 64 en un objeto blob
                            let blob      = new Blob(recordedChunks)
                            let filesize  = Math.round(blob.size / 1024);
                
                            // Obtiene el audio en base 64
                            const file = await $this.convertBlobToBase64(blob);

                            // El elemento audio a reproducir
                            const sound = new Audio(URL.createObjectURL(blob));
                            
                            // Obtiene la duración de la grabación de audio
                            const duration = await $this.getDuration(sound);

                            const audio =
                                {
                                    id       : id,
                                    filename : filename,
                                    type     : filetype,
                                    size     : filesize,
                                    file     : file,
                                    sound    : sound,
                                    playing  : false,
                                    duration : duration,
                                };

                            // Añade el audio a la tabla
                            // Solo se mantiene un solo archivo de audio
                            $this.audios = [];
                            $this.audios.push(audio);

                            $this.time = 0;

                            toastr.success($this.messages.audioRecordSuccess);

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
         * Obtiene la duración de la grabación
         * 
         * @param {Audio} audio     Un objeto audio
         * 
         * @return {Promise}        Una promesa con la duración de la grabación de audio
         */
        getDuration: function (sound) {

            return new Promise(resolve => {
                sound.onloadedmetadata = () => {  
                    resolve(moment.utc(sound.duration * 1000).format('mm:ss'));
                };
            });
        },
        /**
         * Actualiza el cronómetro
         *
         */
        updateTimer: function () {
            ++this.time;

            // Si se supera el tiempo máximo de grabación de audio se detiene el cronómetro
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
         * Suprime la grabación de audio
         * 
         * @param {Audio} audio     La grabación de audio
         */
        removeAudio: function(audio) {
             this.audios = this.audios.filter(_audio => audio.id != _audio.id);
        },
        /**
         * Reproduce la grabación de audio
         * 
         * @param {Audio} audio     La grabación de audio
         */
        playAudio: function(audio) {
            // Reproduce la grabación de audio
            audio.sound.play();
            audio.playing = true;
            // Al terminar la reproducción de la grabación de audio
            audio.sound.addEventListener('ended', function() {
                audio.playing = false;       
            });
        },
        /**
         * Detiene la reproducción de la grabación de audio
         * 
         * @param {Audio} audio     La grabación de audio
         */
        stopAudio: function(audio) {
            // Detiene la reproducción de la grabación de audio y la reinicia
            audio.sound.pause();
            audio.sound.currentTime = 0;
            audio.playing = false;
        },
        /**
         * Guarda el audio 
         *
         * @param {Event} event
         */
        save: function (event) {

            const $this = this;
            
            // Obtiene la url para guardar las grabaciones de audio y la url de la redirección
            // tras el proceso de guardado 
            const request = 
                {
                    save     : event.currentTarget.dataset.saveRequest,
                    redirect : event.currentTarget.dataset.redirectRequest,
                };
            
            HoldOn.open({theme: 'sk-circle'});

            // Envía la grabacion de audio
            axios.post(request.save,
                {
                    audio      : $this.audios[0],     // Las grabacion de Audio
                }
            )
            .then(response => {
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