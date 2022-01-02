/**
 * Selecciona los usuarios a los cuales se les va a compartir el o los documentos
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

const mailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

const { default: Axios } = require("axios");

new Vue({
	el: "#app",
	data: {
		// Los campos del formulario
		name: null,
		lastname: null,
		email: null,
		phone: null,
		dni: null,
		company: null,
		position: null,
		title: null,
		description: null,

		// Si se añade o no el usuario a la lista de contactos del usuario
		addUserToContactList: false,

		// El listado de documentos compartidos
		documents: [],

		// Los destinatios de los documentos
		users: [],

		// El número máximo de usuarios que se pueden seleccionar
		// y que dependerá del plan de susbcripción
		maxUsers: null,

		// El mensaje de error caundo se produce
		error: null,
	},
	/**
	 * Cuando la instancia está montada
	 *
	 */
	mounted: function() {
		// Fija la lista de documentos compartidos
		if (document.getElementById("documents")) {
			this.documents = JSON.parse(
				document.getElementById("documents").value
			);
		}

		// Fija el número máximo de firmantes que pueden ser seleccionados
		if (document.getElementById("max-users")) {
			this.maxUsers = document.getElementById("max-users").value;
		}
	},
	methods: {
		/**
		 * Elimina un usuario
		 *
		 * @param {Number} index    El índice del usuario a eliminar en la tabla
		 */
		removeUser: function(index) {
			this.users.splice(index, 1);
		},

		/**
		 * Comparte los archivos con los usuarios indicados
		 *
		 * @param {Event} event
		 *
		 */
		documentShare() {
			// Inicia la animación
			HoldOn.open({ theme: "sk-circle" });

			axios
				.post(route("dashboard.save.document.sharing"), {
					users: this.users, // La lista de usuarios con los que se comparte
					documents: this.documents, // La lista de documentos compartidos
					title: this.title, // el titulo
					description: this.description, // la descripcion
				})
				.then(() => {
					// Detiene la animación
					HoldOn.close();

					// Redirige a la lista de comparticiones del documento del usuario
					location.href = route("dashboard.list.document.sharing");
				})
				.catch((error) => {
					console.error(error);
					HoldOn.close();
				});
		},

		/**
		 * Añade un contacto desde la lista de contactos a la lista de usuarios
		 *
		 * @param {Event} event
		 */
		addContactToSigners: function(event) {
			const userData = event.target.dataset;

			// Obtiene los datos del contacto
			const user = {
				id: userData.id,
				name: userData.name,
				lastname: userData.lastname,
				email: userData.email,
				phone: userData.phone,
				dni: userData.dni,
				company: userData.company,
				position: userData.position,
			};

			// Si la dirección de correo del usuario ya está incluida en la lista
			// no permite que se vuelva a incluir en el listado de usuarios
			var userAlreadySelected = this.users.find(
				(selectedUser) => selectedUser.email == user.email
			);

			if (typeof userAlreadySelected !== "undefined") {
				this.error = email.dataset.messageEmailExists;
				toastr.error(this.error);
				return false;
			}

			// Lo añade a la table de usuarios
			this.users.push(user);
		},

		/**
		 * Limpia el formulario de creación de usuarios
		 *
		 */
		clearNewUser: function() {
			this.name = null;
			this.lastname = null;
			this.email = null;
			this.phone = null;
			this.dni = null;
			this.company = null;
			this.position = null;
			this.addUserToContactList = false;
			this.error = null;
		},

		/**
		 * Guarda un usuario en la lista personal de contactos
		 *
		 * @param {User} user   El usuario
		 */
		saveUserAsContact: function(user) {
			// datos necsarios para la peticion
			const data = {
				urlSave: document.getElementById("saveForm").dataset.urlSave,
				messageSuccess: document.getElementById("saveForm").dataset
					.messageSuccess,
				messageFailed: document.getElementById("saveForm").dataset
					.messageFailed,
			};

			axios
				.post(data.urlSave, {
					id: null,
					name: user.name,
					lastname: user.lastname,
					email: user.email,
					phone: user.phone,
					dni: user.dni,
					company: user.company,
					position: user.position,
				})
				.then((response) => {
					if (response.data.info) {
						toastr.error(response.data.info);
						return false;
					}

					toastr.success(response.data.infoSuccess);
				})
				.catch((error) => {
					this.error = data.messageFailed;
					console.log(error.response.data.errors);
					toastr.error(this.error);
				});
		},

		/**
		 * Añade un nuevo usuario a la lista de usuarios
		 *
		 * @param {Event} event
		 */
		addNewUser: function(event) {
			// Crea un usuario clonando la entrada del formulario actual
			const user = {
				name: this.name,
				lastname: this.lastname,
				email: this.email,
				phone: this.phone,
				dni: this.dni,
				company: this.company,
				position: this.position,
			};

			// Si la dirección de correo del usuario ya está incluida en la lista
			// no permite que se vuelva a incluir en el listado de usuarios
			var userAlreadySelected = this.users.find(
				(selectedUser) => selectedUser.email == user.email
			);

			if (typeof userAlreadySelected !== "undefined") {
				this.error = email.dataset.messageEmailExists;
				toastr.error(this.error);
				return false;
			}

			// Lo añade a la lista de usuario del documento
			this.users.push(user);

			// Si se ha marcado que se guarde el firmamente en la lista de contactos
			if (this.addUserToContactList) {
				this.saveUserAsContact(user);
			}

			// Limpia el formulario
			this.clearNewUser();
		},

		/**
		 * Busca si existe ya contacto registrado con esa dirección de correo
		 *
		 * @param {String} email    La dirección de correo del contacto
		 */
		findContactByEmail: function(email) {
			if (!email) return;

			// El formulario de datos
			const form = this;

			// Obtiene la dirección de la solicitud
			const request = document.getElementById("email").dataset
				.requestEmail;

			// Consulta si la dirección de correo corresponde a un contacto del usuario
			axios
				.post(request, {
					email: email,
				})
				.then((response) => {
					if (response.data.data) {
						const contact = response.data.data;

						// Completa el formulario con los datos
						form.name = contact.name;
						form.lastname = contact.lastname;
						form.email = contact.email;
						form.phone = contact.phone;
						form.dni = contact.dni;
						form.company = contact.company;
						form.position = contact.position;

						// Añade el contacto a la lista de firmantes
						toastr.success(
							document.getElementById("email").dataset
								.messageEmailLoad
						);
					}
				})
				.catch(() => {
					// HTTP 404 No existe el contacto
				});
		},

		/**
		 * Busca si existe ya contacto registrado con esa número de teléfono
		 *
		 * @param {String} phone    El número de teléfono
		 */
		findContactByPhone: function(phone) {
			if (!phone) return;

			// El formulario de datos
			const form = this;

			// Obtiene la dirección de la solicitud
			const request = document.getElementById("phone").dataset
				.requestPhone;

			// Consulta si la dirección de correo corresponde a un contacto del usuario
			axios
				.post(request, {
					phone: phone,
				})
				.then((response) => {
					if (response.data.data) {
						const contact = response.data.data;

						// Completa el formulario con los datos
						form.name = contact.name;
						form.lastname = contact.lastname;
						form.email = contact.email;
						form.phone = contact.phone;
						form.dni = contact.dni;
						form.company = contact.company;
						form.position = contact.position;

						// Añade el contacto a la lista de firmantes
						toastr.success(
							document.getElementById("email").dataset
								.messageEmailLoad
						);
					}
				})
				.catch(() => {
					// HTTP 404 No existe el contacto
				});
		},
		// Muestra el modal donde se listan todos los documentos que se van a compartir
		showAllDocuments: function() {
			this.$bvModal.show("documents-list-on-sharing");
		},
	},
	computed: {
		/**
		 * Comprueba si se ha superado el número de firmantes que pueden ser seleccinados
		 * de acuerdo con el plan de subscripción actual
		 *
		 * @return {Boolean}
		 *
		 */
		maxSignerExceed: function() {
			return this.users.length >= this.maxUsers;
		},

		/**
		 * Comprueba si los datos del nuevo firmanete añadido son válidos
		 *
		 * Se exige una dirección de correo válida o, alternativamente,
		 * un número de teléfono
		 *
		 */
		whenInvalidData: function() {
			return !mailRegex.test(this.email) && !this.phone;
		},

		/**
		 * Comprueba si la dirección de correo es válida
		 *
		 */
		emailIsInvalid: function() {
			return !mailRegex.test(this.email);
		},
	},
});
