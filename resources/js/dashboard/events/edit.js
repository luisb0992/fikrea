/**
 * Configuracion/ creacion de eventos del usuario
 *
 * Acciones que realiza el usuario para seleccionar, configurar o crear un evento de encuesta,
 * recogida de firmas, o votaciones
 *
 * @author LuisBarDev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

// variables
const { default: Axios } = require('axios');

// componente para embed de video
import Embed from 'v-video-embed';

Vue.use(Embed);

new Vue({
	el: "#app",
	data: {

		// Mensajes para la app
        messages: {
            invalidFile: null,
            eventSuccess: null,
			anonymousEmptyAnswers: null,
			serverError: null,
        },

		// los tipos de eventos disponibles
		eventTypes: {
			vote: 				null,
			survey: 			null,
			signature: 			null,
			surveyAndSignature: null,
		},

		// datos delevento
		event: {
			title: 		 null,			// titulo*
			descrition:  null,			// descripcion
			imagen:	 	 null,			// imagen
			video: 		 null,			// video
			purpose: 	 null,			// finalidad o proposito*
			startDate: 	 null,			// fecha inicio*
			startTime: 	 null,			// hora inicio
			endDate: 	 null,			// fecha final
			endTime: 	 null,			// hora final
			minGoal: 	 null,			// meta minima
			maxGoal: 	 null,			// meta maxima
			type: 		 null,			// tipo de evento*
			isPublic: 	 null,			// evento publico?
			isAnonymous: null,			// evento anonimo
			kioskMode: 	 null,			// modo kiosko
		},

		// estados de distintos binding
		states: {
			stateDatePicker: 				true,		// estado de la fecha
			stateDateTime: 	 				true,		// estado de la hora
			goalAsNumber: 					true,		// estado de la meta como numero
			maximumGoalIsHigher: 			true,		// estado como numero mayor meta maxima
			validImage: 					true,		// estado de la imagen valida o invalida
			validVideo: 					true,		// estado del video valida o invalida
			isUrlImage: 					false,		// si es una imagen por url
			isUrlVideo: 					false,		// si es un video por url
		},

		// archivos cargados para previzualiacion
		preview: {
			image: null,			// imagen
			video: null,			// video
		},
	},
	mounted() {

		// inicializar la fecha inicial del evento
		const now 	= new Date();
		const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
		this.event.startDate = new Date(today);

		// cargar los tipos de eventos
		const types = document.getElementById('eventTypes');
		this.eventTypes.vote 				= types.dataset.vote;
		this.eventTypes.survey 				= types.dataset.survey;
		this.eventTypes.signature 			= types.dataset.signature;
		this.eventTypes.surveyAndSignature 	= types.dataset.surveyAndSignature;

		// cargar mensajes de la app
		const messages = document.getElementById('messages');
		this.messages.invalidFile = messages.dataset.invalidFile;
		this.messages.eventSuccess = messages.dataset.eventSuccess;
		this.messages.anonymousEmptyAnswers = messages.dataset.anonymousEmptyAnswers;
		this.messages.serverError = messages.dataset.serverError;
	},
	methods: {

		/**
		 -------------------------------------------------------------------------
		 ------------- Metodos de validacion en distintos procesos ---------------
		 -------------------------------------------------------------------------
		*/

		/**
		 * Verificar si una imagen cumple o no con los tipos aceptados
		 *
		 * @param {*} file 		La imagen
		 * @returns bool
		 */
		isFileImage(file) {
            const imageTypes = ['image/gif', 'image/jpeg', 'image/png', 'image/tiff'];
            return file && imageTypes.includes(file['type']);
        },

		/**
		 * Verificar si un video cumple o no con los tipos aceptados
		 *
		 * @param {*} file 		El video
		 * @returns bool
		 */
		 isFileVideo(file) {
            const videoTypes = ['video/mp4', 'video/x-msvideo', 'video/x-matroska'];
            return file && videoTypes.includes(file['type']);
        },

		/**
		 -------------------------------------------------------------------------
		 ---------------- Metodos para los distitnos procesos --------------------
		 -------------------------------------------------------------------------
		*/

		/**
		 * Limpiar fecha y hora final
		 */
		clearEndDateAndTime(){
			this.event.endDate = null;
			this.event.endTime = null;
			this.endDateHigher();
		},

		/**
		 * Determina si la fecha final del evento es mayor o menor
		 *
		 * @returns bool
		 */
		endDateHigher() {

			if (!this.event.endDate) {
				this.states.stateDatePicker = true;
				return;
			}

			const startDate = moment(this.event.startDate, "YYYY/MM/DD");
			const endDate 	= moment(this.event.endDate, "YYYY/MM/DD");
			this.states.stateDatePicker = endDate.isAfter(startDate);
		},

		/**
		 * Verificar si la imagen seleccioanda es valida
		 *
		 * @returns bool/createObjectURL
		 */
		checkFileImage() {

			// prevenir cualquier error
			if (!this.event.imagen) {
				return;
			}

            // Comprueba si es un archivo de imagen
			// sino se muestra el mensaje
            if (!this.isFileImage(this.event.imagen)) {
				this.event.imagen = null;
				this.preview.image = null;
				this.states.validImage = false;
				return;
            }

			// imagen valida
			this.states.validImage = true;

			// precargar la imagen para previsualizar
			this.preview.image = URL.createObjectURL(this.event.imagen);
        },

		/**
		 * Verificar si el video seleccionado es valido
		 *
		 * @returns bool/createObjectURL
		 */
		checkVideoFile(){

			// prevenir cualquier error
			if (!this.event.video) {
				return;
			}

			// Comprueba si es un archivo de video aceptado
			// sino se muestra el mensaje
            if (!this.isFileVideo(this.event.video)) {
				this.event.video = null;
				this.preview.video = null;
				this.states.validVideo = false;
				return;
            }

			// formato de video valido
			this.states.validVideo = true;

			// precargar el video para previsualizar
			this.preview.video = URL.createObjectURL(this.event.video);
		},

		/**
		 * Verificar si es o no una inserccion de video por URL
		 */
		insertUrlVideo() {
			this.states.isUrlVideo 	= !this.states.isUrlVideo;		// la negacion si es un video por url
			this.event.video 		= null;							// reseteamos el video
			this.preview.video 		= null;							// limpiar la precarga del video
			this.states.validVideo 	= true;							// la validacion pasa a true
		},

		/**
		 * Verificar si es o no una inserccion de imagen por URL
		 */
		 insertUrlImage() {
			this.states.isUrlImage 	= !this.states.isUrlImage;		// la negacion si es una imagen por url
			this.event.imagen 		= null;							// reseteamos la imagen
			this.preview.image 		= null;							// limpiar la precarga de la imagen
			this.states.validImage 	= true;							// la validacion pasa a true
		},

		/**
		 * Eliminar previamente cargada una imagen
		 */
		deleteImage(){
			this.event.imagen 		= null;		// eliminar imagen
			this.preview.image 		= null;		// eliminar la precarga de la imagen
			this.states.validImage 	= true;		// resetear la valdiacion
		},

		/**
		 * Determinar si la meta maxima es mayor a la minima
		 *
		 * @returns bool
		 */
		highestMaximumGoal(){
			const regex = /^[+]?([1-9][0-9]*(?:[\.][0-9]*)?|0*\.0*[1-9][0-9]*)(?:[eE][+-][0-9]+)?$/;
			// const regex = /^[+]?([0-9]+(?:[\.][0-9]*)?|\.[0-9]+)$/;
			const min = parseInt(this.event.minGoal);
			const max = parseInt(this.event.maxGoal);

			this.states.goalAsNumber = true;
			this.states.maximumGoalIsHigher = true;

			// Si el minimo existe y no es un numero porsitivo
			if (this.event.minGoal && isNaN(this.event.minGoal)) {
				this.states.goalAsNumber = false;
				return;
			}

			// Si el maximo existe y no es un numero porsitivo
			if (this.event.maxGoal && isNaN(this.event.maxGoal)) {
				this.states.goalAsNumber = false;
				return;
			}

			// Si el minimo existe y no cumple el regex
			if (this.event.minGoal) {
				if (!regex.test(min)) {
					this.states.goalAsNumber = false;
					return;
				}
			}

			// si el maximo existe y no cumple el regex
			if (this.event.maxGoal) {
				if (!regex.test(max)) {
					this.states.goalAsNumber = false;
					return;
				}
			}

			// si el minimo no existe y el maximo si
			if (!this.event.minGoal && this.event.minGoal) {
				this.states.goalAsNumber = true;
			}

			// si el maximo es mayor o igual a la meta minima
			this.states.maximumGoalIsHigher = min ? max >= min : true;
		},

		/**
		 * Formatear una fecha dada a un formato aceptado
		 *
		 * @param {*} date 			La fecha a evaluar
		 * @param {*} time 			La hora a evaluar
		 * @returns  Datetime
		 */
		formatDateTime(date, time = null) {

			const formatDate = 'YYYY-MM-DD';
			const formatDateTime = 'YYYY-MM-DD HH:mm:ss';

			const convertedDate = moment(date).format(formatDate);
			const newStartDate = new Date(convertedDate +' '+ time);

			return time ? moment(newStartDate).format(formatDateTime) : moment(date).format(formatDate);
		},

		/**
		 * Guardar el evento
		 *
		 * @param event 			// Evento del boton
		 */
		saveEvent(event){

			// si no cumple con las validaciones pendientes
			if (!this.allStatesCorrect || !this.requiredFieldsCompleted) {
				return;
			}

			// Rutas a ser usadas
			const dataset = {
				toSaveEvent: 	event.currentTarget.dataset.saveEvent,   		// Guardar evento
			};

			// la data a enviar
			let formData = new FormData();

			// si se guarda como borrador
            formData.append('isDraft', event.currentTarget.dataset.draft);

			// campos del formulario
            this.event.title		? formData.append('title', this.event.title) : null;
            this.event.description	? formData.append('description', this.event.description) : null;
            this.event.imagen		? formData.append('image', this.event.imagen) : null;
            this.event.video		? formData.append('video', this.event.video) : null;
            this.event.purpose		? formData.append('purpose_event_id', this.event.purpose) : null;
            this.event.type			? formData.append('type', this.event.type) : null;
            this.event.isPublic		? formData.append('is_public', this.event.isPublic) : null;
            this.event.kioskMode	? formData.append('kiosk_mode', this.event.kioskMode) : null;
            this.event.minGoal		? formData.append('min_goal', this.event.minGoal) : null;
            this.event.maxGoal		? formData.append('max_goal', this.event.maxGoal) : null;

			// si la fecha inicial existe (obligatoria)
			if (this.event.startDate) {
				formData.append('start_date', this.formatDateTime(this.event.startDate, this.event.startTime));
			}

			// si la fecha final existe (opcional)
			if (this.event.endDate) {
				formData.append('end_date', this.formatDateTime(this.event.endDate, this.event.endTime));
			}

			// si no es un evento tipo votacion debe seleccionar como
			// se deben manipular las respuestas
			if (this.event.type != this.eventTypes.vote) {
				if (!this.event.isAnonymous) {
					toastr.error(this.messages.anonymousEmptyAnswers);
					return false;
				}

				this.event.isAnonymous	? formData.append('is_anonymous', this.event.isAnonymous) : null;
			}

			// Inicio de animacion
			HoldOn.open({theme: 'sk-circle'});

			Axios.post(
				dataset.toSaveEvent,
				formData,
				{
					headers: {
						"Content-Type": "multipart/form-data",
					},
				}
			)
			.then((response) => {

				// Detener animacion
				HoldOn.close();

				// si todo fue correcto desde el backend
				// dependiendo del tipo de evento se redirecciona auna url u a otra
				if (response.data.url) {
					location.href = response.data.url;
				}else{
					toastr.error(this.messages.serverError);
					return;
				}

				// Mensaje de evento guardado
				toastr.success(this.messages.eventSuccess);
			})
			.catch((error) => {
				HoldOn.close(); // Detener animacion

				// errores de validacion
				if (error.response.data.errors) {

					const errors 	= error.response.data.errors;
					const title 	= errors.title ? errors.title.filter(msj => !!msj) : [];
					const startDate = errors.start_date ? errors.start_date.filter(msj => !!msj) : [];
					const purpose 	= errors.purpose_event_id ? errors.purpose_event_id.filter(msj => !!msj) : [];
					const type 		= errors.type ? errors.type.filter(msj => !!msj) : [];

					let msjs = [
						...title,
						...startDate,
						...purpose,
						...type,
					];

					// separacion de la validacion
					msjs = msjs.join('<br/>');

					// mostrar los errores
					toastr.error(msjs);
				}
			});
		},
	},

	computed: {

		/**
		 * Comprobar si todas los estados de valicacion estan correctas
		 *
		 * @returns bool
		 */
		allStatesCorrect() {
			return 	this.states.stateDatePicker &&
					this.states.stateDateTime &&
					this.states.goalAsNumber &&
					this.states.validImage &&
					this.states.validVideo &&
					this.states.maximumGoalIsHigher;
		},

		/**
		 * Verificar si los campos requeridos han sido llenado
		 *
		 * @returns bool
		 */
		requiredFieldsCompleted() {
			return this.event.title && this.event.purpose && this.event.startDate && this.event.type;
		}
	}
});
