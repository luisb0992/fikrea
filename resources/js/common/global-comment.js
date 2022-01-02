/**
 * Manipula los comentarios dejados por los usuarios en varias zonas de la app
 * aplicando para las validaciones del proceso de firma y validaciones independientes
 *
 * @author LuisBarDev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

/**
 * Plugin global para guardar un comentario
 *
 * @param {Event} event     El evento del boton
 */
const SaveValidationOrProcessComment = {
	install(Vue, options) {
		Vue.prototype.saveValidationComment = (event) => {
			// comentario vacio o no
			let commentIsEmpty = false;

			// cargar axios
			const { default: Axios } = require("axios");

			// los campos del formulario
			const formElements = Array.from(event.target.elements);

			// cargar los mensajes
			const commentMessageComplete 		= document.getElementById("comment-messages").dataset.complete;
			const commentMessageError 			= document.getElementById("comment-messages").dataset.error;
			const commentMessageEmptyComment 	= document.getElementById("comment-messages").dataset.empty;
			const commentMessageAlreadyDone 	= document.getElementById("comment-messages").dataset.alreadydone;

			// botones que abren el modal
			const btnEnableModal = Array.from(document.querySelectorAll(".disabledCommentButton"));

			// texto a reemplazar en el boton una vez ejecutada la peticion
			const sendComment = document.getElementById("comment-messages").dataset.sendtext;

			// un objeto con los datos del formulario
			const formComment = {
				id: 			null,	// el id del signer o del proceso (opcional si es un proceso de documento)
				token: 			null,	// el token de acceso
				validationType: null,	// el tipo de validacion
				process: 		null,	// el nombre del proceso (opcional si es un proceso de documento)
				comment: 		null,	// el comentario
				urlSave: 		null,	// la url donde ooira la peticion
			};

			// cargar y validar los datos necesarios
			formElements.forEach((input) => {

				// si el comentario esta vacio se valida fuera le foreach
				if (input.name == "comment" && input.value == "") {
					commentIsEmpty = true;
				}

				// carga los datos a enviar
				if (input.name == "comment") {
					formComment.comment = input.value;
				} else if (input.name == "token") {
					formComment.token = input.value;
				} else if (input.name == "id") {
					formComment.id = input.value;
				} else if (input.name == "process") {
					formComment.process = input.value;
				} else if (input.name == "validationtype") {
					formComment.validationType = input.value;
				}

				// Tomar la url a enviar la peticion
				if (input.type == "submit") {
					formComment.urlSave = input.dataset.save;
				}
			});

			// si el comentario esta vacio
			if (commentIsEmpty) {
				toastr.error(commentMessageEmptyComment);
				return false;
			}

			// ejecutar la peticion si existe una url
			if (formComment.urlSave) {

				// open animacion
				HoldOn.open({ theme: "sk-circle" });

				Axios.post(formComment.urlSave, {
					id: 			formComment.id, 			// El id del signer o del proceso segun sea el caso
					token: 			formComment.token, 			// El token del signer
					validationType: formComment.validationType, // El tipo de validacion que se le esta asignando el comentario (si existe)
					process: 		formComment.process, 		// El proceso independiente del documento (si existe)
					comment: 		formComment.comment, 		// El comentario a guardar
				}).then((response) => {

					// cierra la animacion
					HoldOn.close();

					// cerrar modal
					document.getElementById("closeCommentModal").click();

					if (response.data.res == 2) {

						// mensaje de comentario ya realizado
						toastr.error(commentMessageAlreadyDone);
					}else{

						// Mensaje de comentario guardado
						toastr.success(commentMessageComplete);

						// desactivar todos los campos y botones
						formElements.map(input => input.disabled = true);

						// desactivar boton que activa el modal
						btnEnableModal.map(btn => btn.disabled = true);

						// cambiar texto
						btnEnableModal.map(btn => btn.textContent = sendComment);
					}
				}).catch((error) => {
					HoldOn.close();
					console.error(error);
					toastr.error(commentMessageError);
				});
			}else{
				toastr.error(commentMessageError);
			}
		};
	},
};

Vue.use(SaveValidationOrProcessComment);
