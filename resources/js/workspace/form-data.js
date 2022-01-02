/**
 * Formulario de datos
 *
 * El usuario o firmante responde a la solicitud de un formulario de datos
 * con validaciones establecidas
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

const { default: Axios } = require("axios");

new Vue({
	el: "#app",
	data: {
		// La posición del usuario datum WGS84
		position: {
			latitude: null,
			longitude: null,
		},

		// La visita del usuario firmante
		visit: null,

		// data a ser guardada
		inputs: [],

		// Errores de la app
		errors: {
			inputEmpty: document.getElementById("errors").dataset.inputEmpty, // Mensaje de todos los input de texto vacios
			inputOneEmpty: document.getElementById("errors").dataset
				.inputOneEmpty, // Mensaje de input de texto si esta vacio
			inputMin: document.getElementById("errors").dataset.inputMin, // Mensaje de input minimo de caracteres
			inputMax: document.getElementById("errors").dataset.inputMax, // Mensaje de input maximo de caracteres
			inputCharacterType: document.getElementById("errors").dataset
				.inputCharacterType, // Mensaje de input tipo de caracter
		},

		// Mensaje de proceso completado
		success: document.getElementById("success").dataset.success,

		// las validaciones de cada input
		validations: {
			min: false,
			max: false,
			characterType: false,
		},

		// Validacion del tipo de caracter
		characterType: {
			string: "string", // solo cadenas de texto (letras)
			numeric: "numeric", // solo numeros
			special: "special", // solo texto y caracteres especiales
		},
	},

	/**
	 * Cuando tenemos la instancia montada
	 *
	 */
	mounted: function() {
		// Obtenemos el registro de la visita del usuario a la página
		this.visit = JSON.parse(document.getElementById("visit").dataset.visit);

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

		// verificar si se puede realizar o no la verificación de datos
		if (document.getElementById("urlIsDone")) {

			// Obetner la ruta
			const url = document.getElementById("urlIsDone").dataset.verificationIsDone;

			// repetir con el intervalo de 2 segundos
			let timerId = setInterval(() => this.getVerificationFormIsDone(url), 1000);

			// después de 5 segundos parar
			setTimeout(() => {
				clearInterval(timerId);
			}, 2000);
		}
	},

	methods: {

		/**
		 * Verificar si algun input de texto esta vacio
		 *
		 * @param inputs		array de inputs tipo texto
		 * @returns bool
		 */
		isFieldNameEmpty(inputs) {
			return inputs.some(
				(input) =>
					input.value.length === 0 ||
					input.value === null ||
					input.value === ""
			);
		},

		/**
		 * Verificar si alguna de las validaciones es incorrecta (no valida)
		 *
		 * @returns bool
		 */
		isNotValidate() {
			return Object.values(this.validations).some(
				(validation) => validation
			);
		},

		/**
		 * Verificar si todas las validaciones son correctas
		 *
		 * @returns bool
		 */
		isValidate() {
			return Object.values(this.validations).every(
				(validation) => !validation
			);
		},

		/**
		 * Verificar si existen solo letras y caracteres en la cadena
		 *
		 * @param text 			texto o string a validar
		 * @returns bool
		 */
		isOnlyLetters(text) {
			const regex = /^[A-Z]+$/i;

			if (!regex.test(text)) {
				return (this.validations.characterType = true);
			}
		},

		/**
		 * Verificar si existen solo numeros en la cadena
		 *
		 * @param text 			texto o string a validar
		 * @returns bool
		 */
		isOnlyNumbers(text) {
			const regex = /^[0-9\s]+$/;

			if (!regex.test(text)) {
				return (this.validations.characterType = true);
			}
		},

		/**
		 * Verificar si existen solo letras y caracteres
		 *
		 * @param text 			texto o string a validar
		 * @returns bool
		 */
		isOnlySpecial(text) {
			const regex = /^[A-Za-z\s\D]+$/g;

			if (!regex.test(text)) {
				return (this.validations.characterType = true);
			}
		},

		/**
		 * Resetea las validaciones del formulario
		 */
		resetValidations() {
			this.validations.min = false;
			this.validations.max = false;
			this.validations.characterType = false;
		},

		/**
		 * Marcar un input como valido
		 *
		 * @param row 			fila que contiene el input y el div del mensaje validado
		 */
		inputValid(row) {
			row.input.classList.remove("is-invalid");
			row.input.classList.add("is-valid");
			row.divMsj.classList.remove("invalid-feedback");
			row.divMsj.classList.add("valid-feedback");
			row.divMsj.textContent = null;
		},

		/**
		 * Marcar el input como invalido
		 *
		 * @param row 		fila que contiene el input y el div del mensaje validado
		 * @param msj 		Mensaje de validacion a mostrar
		 */
		inputInvalid(row, msj) {
			row.input.classList.remove("is-valid");
			row.input.classList.add("is-invalid");
			row.divMsj.classList.remove("valid-feedback");
			row.divMsj.classList.add("invalid-feedback");
			row.divMsj.textContent = msj;
		},

		/**
		 * Remover todas las clases si los campos estan vacios
		 *
		 * @param row 		fila que contiene el input y el div del mensaje validado
		 */
		inputWithoutValidation(row) {
			row.input.classList.remove("is-invalid", "is-valid");
			row.divMsj.classList.remove("invalid-feedback", "valid-feedback");
			row.divMsj.textContent = null;
		},

		/**
		 * Modificar la clase del boton de validacion a "invalido"
		 *
		 * @param index 	Indice a buscar dentro del id
		 * @param type 		tipo de boton a modificar
		 */
		buttonValidationInvalid(index, type) {
			const btn = document.getElementById(`btn-${type}-${index}`);
			btn.classList.remove("btn-success", "btn-outline-secondary");
			btn.classList.add("btn-danger");
		},

		/**
		 * Modificar la clase del boton de validacion a "valida"
		 *
		 * @param index 	Indice a buscar dentro del id
		 * @param type 		tipo de boton a modificar
		 */
		buttonValidationValid(index, type) {
			const btn = document.getElementById(`btn-${type}-${index}`);

			if (btn) {
				btn.classList.remove("btn-danger", "btn-success");
				btn.classList.add("btn-outline-secondary");
			}
		},

		/**
		 * Modificar la clase del boton ya correcto y validado
		 *
		 * @param index 	Indice a buscar dentro del id
		 * @param type 		tipo de boton a modificar
		 */
		btnValid(index, type) {
			const btn = document.getElementById(`btn-${type}-${index}`);

			if (btn) {
				btn.classList.remove("btn-danger", "btn-outline-secondary");
				btn.classList.add("btn-success");
			}
		},

		/**
		 * Verificar si el input cumple con las regla de validacion para cada caso
		 *
		 * @param type 		el tipo de caracter a evaluar
		 * @param value 	el valor del input
		 */
		validateCharacterType(type, value) {
			switch (type) {
				case this.characterType.string:
					this.isOnlyLetters(value);
					break;
				case this.characterType.numeric:
					this.isOnlyNumbers(value);
					break;
				case this.characterType.special:
					this.isOnlySpecial(value);
					break;
				default:
					break;
			}
		},

		/**
		 * Validar un input de texto mientras el usuario escribe sobre el
		 * obteniendo sus valdiaciones previas
		 *
		 * @param index 		indice del input a validar
		 */
		validateFieldText(index) {
			const validationsToInput = {
				min: document.getElementById(`span-min-${index}`),
				max: document.getElementById(`span-max-${index}`),
				characterType: document.getElementById(
					`span-character_type-${index}`
				),
			};

			const row = {
				input: document.getElementById(`input-field_text-${index}`),
				divMsj: document.getElementById(`msj-field_text-${index}`),
			};

			// Resetear las validaciones
			this.resetValidations();

			// remover clases y textos
			this.inputWithoutValidation(row);

			// resetear botones de validacion
			this.buttonValidationValid(index, "min");
			this.buttonValidationValid(index, "max");
			this.buttonValidationValid(index, "character_type");

			// campo vacio
			if (
				row.input.value === null ||
				!row.input.value.length ||
				row.input.value === undefined
			) {
				this.inputInvalid(row, this.errors.inputOneEmpty);
				return false;
			}

			// Valor minimo aceptado
			if (validationsToInput.min) {
				if (
					row.input.value.length <
					parseInt(validationsToInput.min.textContent)
				) {
					this.inputInvalid(row, this.errors.inputMin);
					this.buttonValidationInvalid(index, "min");
					return false;
				}
			}

			// Valor maximo permitido
			if (validationsToInput.max) {
				if (
					row.input.value.length >
					parseInt(validationsToInput.max.textContent)
				) {
					this.inputInvalid(row, this.errors.inputMax);
					this.buttonValidationInvalid(index, "max");
					return false;
				}
			}

			// Caracter aceptado
			if (validationsToInput.characterType) {
				// switch de validacion
				this.validateCharacterType(
					validationsToInput.characterType.dataset.type,
					row.input.value
				);

				if (this.validations.characterType) {
					this.inputInvalid(row, this.errors.inputCharacterType);
					this.buttonValidationInvalid(index, "character_type");
					return false;
				}
			}

			// si pasa todas las validaciones el input es valido
			this.inputValid(row);

			// modificar class correctas de los botones
			this.btnValid(index, "min");
			this.btnValid(index, "max");
			this.btnValid(index, "character_type");
		},

		/**
		 * Validar que el input cumpla con la validacion asignada
		 *
		 * @param inputs 			Array de inputs con la informacion
		 */
		validateInputs(inputs) {
			this.inputs = []; // Vaciar antes de llenar

			inputs.forEach((input) => {
				// id del dataset
				const id = input.dataset.id;

				// Obtener las validaciones establecidas para el input
				const validationsToInput = {
					min: document.getElementById(`span-min-${id}`),
					max: document.getElementById(`span-max-${id}`),
					characterType: document.getElementById(
						`span-character_type-${id}`
					),
				};

				// Valor minimo aceptado
				if (validationsToInput.min) {
					if (
						input.value.length <
						parseInt(validationsToInput.min.textContent)
					) {
						this.validations.min = true;
					}
				}

				// valor maximo aceptado
				if (validationsToInput.max) {
					if (
						input.value.length >
						parseInt(validationsToInput.max.textContent)
					) {
						this.validations.max = true;
					}
				}

				// Caracter aceptado
				if (validationsToInput.characterType) {
					switch (validationsToInput.characterType.dataset.type) {
						case this.characterType.string:
							this.isOnlyLetters(input.value);
							break;
						case this.characterType.numeric:
							this.isOnlyNumbers(input.value);
							break;
						case this.characterType.special:
							this.isOnlySpecial(input.value);
							break;
						default:
							break;
					}
				}

				if (this.isValidate()) {
					this.inputs.push({
						id: id,
						field_text: input.value,
					});
				}
			});
		},

		/**
		 * Guarda el formulario de datos solicitado
		 *
		 * @param {Event} event
		 *
		 */
		saveFormdata: function(event) {
			this.resetValidations(); // resetear validaciones

			const inputs = [
				...document.querySelectorAll('input[name="field_text[]"]'),
			]; // array de inputs

			// Primero verificar que ningun input este vacio
			// esta validacion permitira que todos sean requeridos
			if (this.isFieldNameEmpty(inputs)) {
				toastr.error(this.errors.inputEmpty);
				return false;
			}

			// Validar si cumple o no las reglas establecidas por el usuario
			// Verificar si se cumple todas las validaciones asignadas
			this.validateInputs(inputs);

			// en caso de que alguno sea un "false" detiene el proceso
			// y muestra el mensaje de error de validacion
			if (this.isNotValidate()) {
				if (this.validations.min) {
					toastr.error(this.errors.inputMin);
				} else if (this.validations.max) {
					toastr.error(this.errors.inputMax);
				} else if (this.validations.characterType) {
					toastr.error(this.errors.inputCharacterType);
				}

				return false;
			}

			// Si todo sale OK continua con el proceso y guarda los datos
			const $this = this;

			// Obtiene la url para guardar los inputs
			// y la url de redireccion
			const request = {
				save: event.currentTarget.dataset.saveRequest,
				redirect: event.currentTarget.dataset.redirectRequest,
			};

			// Activar el plugin de bloqueo de pantalla
			HoldOn.open({ theme: "sk-circle" });

			// Envía la solicitud de documentos
			axios.post(request.save, {
					data: $this.inputs, // Los inputs
					position: $this.position, // La posición datum WGS84
					visit: $this.visit, // La información de la visita realizada
				})
				.then(() => {
					HoldOn.close(); // desactivar el plugin de bloqueo de pantalla
					toastr.success($this.success); // Mensaje de proceso completado

					location.href = request.redirect;
				})
				.catch((error) => {
					HoldOn.close();
					console.error(error);
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
		 * Comprueba si la verificación de datos fue realizada anteriormente
		 * o no
		 *
		 * @param {*} url 			Url de la ruta a consultar
		 */
		getVerificationFormIsDone(url) {
			axios.get(url)
				.then((response) => {
					if (response.data.isDone) {
						location.reload();
					}
				})
				.catch((error) => {
					console.error(error);
				});
		},
	}
});
