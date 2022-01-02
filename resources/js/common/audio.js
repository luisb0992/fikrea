/**
 * Grabación de audio para la validación de un documento
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

const { default: Axios } = require("axios");

new Vue({
	el: "#app",
	data: {
		messages: messages.dataset,             // Los mensajes

		recording: false,                       // true si está grabando audio, false en caso contrario
		pause: false,                           // true si pausado el audio, false en caso contrario
		mediaRecorder: null,                    // El mediarecorder

		timer: null,                            // El cronómetro
		time: 0,                                // El tiempo en segundos de la grabación
		maxTime: audio.dataset.maxRecordTime,   // El máximo tiempo que se puede grabar en segundos

		audios: [],                             // Una lista de audios grabados
		                                        // La posición del usuario firmante datum WGS84
		position: {
			latitude: null,
			longitude: null,
		},

		visit: null,                            // La visita del usuario firmante

		// El audio que ha grabado el solicitante para que se tome como ejemplo en la grabacion
		audio: {
			file: {},                           // Objeto audio con la info del audio grabado por el usuario
		},

		// Timer para la reproduccion del audio
		playingTimer: null,

        // Tiempo de reproduccion del audio
		currentPlayingTime: 0,
	},

	/**
	 * Cuando tenemos la instancia montada
	 *
	 */
	mounted: function() {
		const $this = this;

		// Intentamos obtener la geolocalización del firmante
		this.getCurrentPosition()
			.then(() => {
				console.info(
					`[INFO] Se ha obtenido la posición del firmante con éxito`
				);
			})
			.catch(() => {
				console.info(
					`[INFO] No se ha podido obtener la posición del firmante`
				);
			});

		// Obtenemos el registro de la visita del usuario a la página
		this.visit = JSON.parse(document.getElementById("visit").dataset.visit);
	},
	methods: {

		/**
		 * Inicia o detiene la grabación de audio
		 *
		 */
		recordAudio: function() {
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
		startRecording: function() {
			const $this = this;

			const options = { audio: true, video: true };
			const recordedChunks = [];

			navigator.mediaDevices.getUserMedia(options).then((stream) => {

				// Se pone a cero el tiempo de grabación
				$this.time = 0;

				// Se inicia el cronómetro que se actualiza cada segundo
				$this.timer = setInterval(this.updateTimer, 1000);

				$this.mediaRecorder = new MediaRecorder(stream, options);

				$this.mediaRecorder.addEventListener("dataavailable", function(event) {
					if (event.data.size > 0) {
						recordedChunks.push(event.data);
					}
				});

				$this.mediaRecorder.addEventListener("stop", async function() {
					// Obtiene un id único para el archivo de audio creado
					let id = Math.random().toString(36).substr(2, 9);
					let filename = `${id}.wav`;
					let filetype = "audio/wav";

					// Convierte los datos de audio en base 64 en un objeto blob
					let blob = new Blob(recordedChunks);
					let filesize = Math.round(blob.size / 1024);
					let duration = moment.utc($this.time * 1000).format("mm:ss");

					// Obtiene el audio en base 64
					const file = await $this.convertBlobToBase64(blob);

					// El elemento audio a reproducir
					const sound = new Audio(URL.createObjectURL(blob));

					const audio = {
						id: id,
						filename: filename,
						type: filetype,
						size: filesize,
						file: file,
						sound: sound,
						playing: false,
						duration: duration,
					};

					// Añade el audio a la tabla
					$this.audios.push(audio);

					toastr.success($this.messages.audioRecordSuccess);
					$this.time = 0;
				});

				$this.mediaRecorder.start();
			}).catch((error) => {
                console.error(error);
            });
		},

		/**
		 * Convierte un objeto Blob en base 64
		 *
		 * @param {Blob} blob       Un objeto Blob
		 *
		 * @return {Promise}        Una promesa con el objeto blob convertido a base 64
		 */
		convertBlobToBase64: function(blob) {
			const reader = new FileReader();
			reader.readAsDataURL(blob);

			return new Promise((resolve, reject) => {
				reader.onloadend = () => {
					resolve(reader.result);
				};

				reader.onerror = (error) => {
					reject(error);
				};
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
					(pos) => {
						$this.position.latitude = pos.coords.latitude;
						$this.position.longitude = pos.coords.longitude;
						resolve($this.position);
					},
					(error) => {
						reject(error);
					},
					{
						enableHighAccuracy: true,
						timeout: 5000,
						maximumAge: 0,
					}
				);
			});
		},

        /**
		 * Actualiza el cronómetro
		 *
		 */
		updateTimer: function() {
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
		stopTimer: function() {
			// Detiene el cronómetro
			clearInterval(this.timer);

			// Detiene la grabación
			this.mediaRecorder.stop();

            // parar la grabacion
            this.recording = false;

            // reestablecer la pausa
            this.pause = false;
		},

        /**
		 * Pausa la grabacion
		 */
        pauseAudio() {

            // pausa o reanuda la grabacion
			this.pause = !this.pause;

			// si fue pausada
			if (this.pause) {

                // limpiar el cronometro
                clearInterval(this.timer);

                // pausa la grabación
                this.mediaRecorder.pause();
			} else {

                // reanudar el cronometro
                this.timer = setInterval(this.updateTimer, 1000);

                // reanudar la grabacion
                this.mediaRecorder.resume();
			}
		},

        /**
		 * Suprime la grabación de audio
		 *
		 * @param {Audio} audio     La grabación de audio
		 */
		removeAudio: function(audio) {
			this.audios = this.audios.filter((_audio) => audio.id != _audio.id);
		},

        /**
		 * Reproduce la grabación de audio
		 *
		 * @param {Audio} audio     La grabación de audio
		 */
		playAudio: function(audio) {
			const $this = this;

			// Reproduce la grabación de audio
			audio.sound.play();
			audio.playing = true;

			$this.currentPlayingTime = 0;
			$this.playingTimer = setInterval(() => {
				++$this.currentPlayingTime;
			}, 1000);

			// Al terminar la reproducción de la grabación de audio
			audio.sound.addEventListener("ended", function() {
				audio.playing = false;

				$this.currentPlayingTime = 0;
				clearInterval($this.playingTimer);
			});
		},
		/**
		 * Detiene la reproducción de la grabación de audio
		 *
		 * @param {Audio} audio     La grabación de audio
		 */
		stopAudio: function(audio) {
			const $this = this;

			// Detiene la reproducción de la grabación de audio y la reinicia
			audio.sound.pause();
			audio.sound.currentTime = 0;
			audio.playing = false;

			$this.currentPlayingTime = 0;
			clearInterval($this.playingTimer);
		},
		/**
		 * Guarda la validación por audio
		 *
		 * @param {Event} event
		 */
		save: function(event) {
			const $this = this;

			// Obtiene la url para guardar las grabaciones de audio y la url de la redirección
			// tras el proceso de guardado
			const request = {
				save: event.currentTarget.dataset.saveRequest,
				redirect: event.currentTarget.dataset.redirectRequest,
			};

			// Envía las grabaciones de audio
			HoldOn.open({ theme: "sk-circle" });
			Axios.post(request.save, {
				audios: $this.audios, // Las grabaciones de Audio
				position: $this.position, // La posición datum WGS84
				visit: $this.visit, // La información de la visita realizada
			})
				.then(() => {
					HoldOn.close();
					location.href = request.redirect;
				})
				.catch((error) => {
					HoldOn.close();
					console.error(error);
				});
		},
	},
	computed: {

		/**
		 * Muestra la representación del cronómetro en el formato mm:ss
		 *
		 */
		showTimer: function(event) {
			return this.time && this.recording ? moment.utc(this.time * 1000).format("mm:ss") : "";
		},

        /**
         * Muestra el tiempo del cronometro pausado
         * @returns number
         */
        pauseTimer(){
            return moment.utc(this.time * 1000).format("mm:ss");
        },
	},
});
