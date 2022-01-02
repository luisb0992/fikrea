/**
 * Maneja la lista de archivos del usuario
 *
 * @author javieru <javi@gestoy.co>
 * @copyright 2021 Retail Servicios Externos
 */

new Vue({
	el: "#app",
	data: {
		// La lista de documentos seleccionados
		documents: [], // La lista de documentos a eliminar
		// Se almacena en almacenamiento local del navegador
		// lo que permite recordar esa lista al cambiar entre páginas del listado

		// los documentos de la tabla independientemente de su estado
		allDocuments: [],

		// urls
		request: {
			removeDocument: document.getElementById("requests").dataset.removeDocumentRequest,
			destroyDocument: document.getElementById("requests").dataset.destroyDocumentRequest,
		},

		// El id de un documento seleccionado (seleccion individual)
		idDocument: null,

		// El nombre del documento seleccionado (seleccion individual)
		nameDocument: null,

		// el id de la carpeta donde se almacena el documento si es movido a la lista de archivos
		parentId: null,

		// si se ha seleccioando un documento desde una seleccion multiple
		multipleSelection: false,

		// los datos de multiples documentos que se pueden copiar
		copyDocuments: [],

		// carga los documentos que pueden ser compartidos
		shareDocuments: [],
	},

	/**
	 * Cuando la instancia está montada
	 *
	 */
	mounted: function() {
		// Carga la lista de dpcumentos seleccionados del almacenamiento local
		// desde la clave "selected-documents"
		// Con ello se obtienen los documentos seleccionados en otras páginas diferentes a la actual
		this.documents = JSON.parse(localStorage.getItem("selected-documents")) || [];

		// Carga el almacenamiento
		this.diskSpace = {
			free: 		parseInt(document.getElementById("free-disk-space").value),
			used: 		parseInt(document.getElementById("used-disk-space").value),
			available: 	parseInt(document.getElementById("available-disk-space").value),
		};

		// cargar todos los documentos en un array configurable
		this.allDocuments = [...document.querySelectorAll(".document")];
	},
	methods: {

		/**
		 * Marcar o dsmarcar todos los inputs tipo check de la lista de documentos
		 *
		 * @param {bool} status 		True o false para marcar o desmarcar
		 */
		checkOrUncheckAllFields(status) {
			[...document.querySelectorAll(".document")].map((document) => document.checked = status);
		},

		/**
		 * Selecciona un documento
		 *
		 */
		select: function() {
			// Guarda la lista de archivos seleccionados en el almacenamiento local
			localStorage.setItem("selected-documents", JSON.stringify(this.documents));

			// cargar los documentos que puedens ser copiados y compartidos
			this.checkDocuments(true, true);
		},

		/**
		 * Selecciona todos los documentos
		 *
		 * @param {Event} event
		 */
		selectAll: function(event) {
			if (event.target.checked) {

				// Marca todos los documentos
				this.documents = [...document.querySelectorAll(".document")].map((document) => document.value);

				// marca todos como checked
				this.checkOrUncheckAllFields(true);
			} else {

				// marca todos con no checked
				this.checkOrUncheckAllFields(false);

				// Desmarca todos los documentos
				this.documents = [];
				this.copyDocuments = [];
				this.shareDocuments = [];
				this.multipleSelection = false;
			}

			// Guarda la lista de archivos seleccionados en el almacenamiento local
			this.select();
		},

		/**
		 * Limpia la lista de archivos seleccionados
		 *
		 */
		clearFiles: function() {

			// Desmarca todos los documentos
			this.documents = [];
			this.copyDocuments = [];
			this.shareDocuments = [];
			this.multipleSelection = false;

			// Elimina los elementos seleccionados del almacenamiento local
			localStorage.removeItem("selected-documents");

			// marca todos los input a false
			this.checkOrUncheckAllFields(false);

			// desmarca el input que checkea todos los demas
			document.getElementById('inputCheckAll').checked = false;
		},

		/**
		 * Muestra la modal de confirmación de eliminación de los documentos
		 *
		 * @param {Number} id      El id del documento a eliminar
		 */
		showAlertBeforeRemoveDocument: function(id) {
			// Si se ha suministrado un documento en concreto
			if (id) {
				this.documents = [id];
			}

			// Muestra la modal
			this.$bvModal.show("remove-document-confirmation");
		},
		/**
		 * Elimina el documento
		 * Lo envía a la papelera de reciclaje
		 *
		 */
		confirmRemoveDocument: function() {
			const $this = this;

			// Inicia la animación
			HoldOn.open({ theme: "sk-circle" });

			// Los documentos son definitivamente eliminados
			axios
				.post($this.request.removeDocument, {
					documents: $this.documents,
				})
				.then(() => {
					// Detiene la animación
					HoldOn.close();

					console.log(
						`Los documentos seleccionados han sido eliminados`
					);
					// Elimina cualquier selección de archivos existente
					localStorage.removeItem("selected-documents");
					// Recarga la página
					location.reload();
				})
				.catch((error) => {
					// Detiene la animación
					HoldOn.close();

					console.error(error);
				});
		},
		/**
		 * Muestra la modal de confirmación de eliminación definitiva del documento
		 *
		 * @param {Number|null} id      El id del documento concreto a eliminar definitivamente
		 *                              o null para elinminar todos los documentos seleccionados
		 */
		showAlertBeforeDestroyDocument: function(id) {
			// Si se ha suministrado un documento en concreto
			if (id) {
				this.documents = [id];
			}
			// Muestra la modal
			this.$bvModal.show("destroy-document-confirmation");
		},
		/**
		 * Elimina el documento definitivamente desde la papelera
		 *
		 * @return {Number} id      El id del documento a eliminar
		 *
		 */
		confirmDestroyDocument: function(id) {
			const $this = this;

			// Inicia la animación
			HoldOn.open({ theme: "sk-circle" });

			// Los documentos son definitivamente eliminados
			axios
				.post($this.request.destroyDocument, {
					documents: $this.documents,
				})
				.then(() => {
					// Detiene la animación
					HoldOn.close();

					console.log(
						`Los documentos seleccionados han sido eliminados`
					);
					// Elimina cualquier selección de archivos existente
					localStorage.removeItem("selected-documents");
					// Recarga la página
					location.reload();
				})
				.catch((error) => {
					// Detiene la animación
					HoldOn.close();

					console.error(error);
				});
		},

		/**
		 * Compartir documentos con usuarios
		 *
		 * @param {*} event         El evento del boton
		 */
		shareFiles(event) {

			// valdiar primero si hay documntos a compartir
			if (!this.shareDocuments.length) {
				toastr.warning(event.currentTarget.dataset.messageDocumentEmpty);
				return false;
			}

			// cargar los datos y formulario
			const form 		= document.getElementById("formSharing");
			const dataInput = document.getElementById("inputDataSharing");

			// cargar los documentos
			dataInput.value = this.shareDocuments.map(doc => doc.id);

			// enviar el formulario
			form.submit();
		},

		/**
		 * Copia un documento original - firmado a la lista de archivos
		 *
		 * @param {*} event 	El evento del boton
		 */
		copyDocumentToFiles(event) {
			// Url de guardado y listado de archivos
			const url = {
				urlCopyDocument: event.currentTarget.dataset.urlCopyDocument,
				urlListFiles: event.currentTarget.dataset.urlFiles,
			};

			// mensajes de la app
			const msj = {
				emptySelection: event.currentTarget.dataset.emptySelection,
			};

			// Inicia la animación
			HoldOn.open({ theme: "sk-circle" });

			// Los datos de el o los documentos
			let dataDocument = [];

			// si es una seleccion multiple de documentos
			if (this.multipleSelection || this.copyDocuments.length) {
				this.copyDocuments.forEach((document) => {
					dataDocument.push({
						idDocument: document.id,
						parentId: this.parentId,
					});
				});

				// Si es una seleccion simple de un documento
			} else if (this.idDocument) {
				dataDocument.push({
					idDocument: this.idDocument,
					parentId: this.parentId,
				});

				// Ninguna de las propiedades esta llena y se cancela la peticion
			} else {
				toastr.warning(msj.emptySelection);
				return false;
			}

			axios
				.post(url.urlCopyDocument, { dataDocument: dataDocument })
				.then((response) => {
					// Detiene la animación
					HoldOn.close();

					// si algun documento no pudo ser copiado
					if (response.data.failedProcess) {
						const msj = response.data.failedProcess;
						const infoMsj = response.data.infoFailedProcess;
						const documentNoCopy = response.data.documentNoCopy;

						const listDocumentNoCopy = documentNoCopy.forEach((document) => `<li>${document}</li>`);

						toastr.info(`
							${msj}. ${infoMsj} <hr>
							<ul>
							${listDocumentNoCopy}
							</ul>
						`);

						return false;
					}

					// si existe algun mensaje de validacion
					if (response.data.info) {
						toastr.error(response.data.info);
						return false;
					}

					// -------------------------------------------------------
					// -----> Si todo ha salido bien hasta este punto <-------
					// -------------------------------------------------------

					// mensaje exitoso
					toastr.success(response.data.successProcess);

					// Oculta el modal
					this.$bvModal.hide("move-document-to-files-modal");

					// ir a la pagina de archivos
					location.href = url.urlListFiles;
				})
				.catch((error) => {
					// Detiene la animación
					HoldOn.close();
					console.error(error);
				});
		},

		/**
		 * Cargar el documento seleccionado
		 * puede ser una seleccion multiple o individual
		 *
		 * @param {*} event 	El evento del boton
		 */
		loadDocumentSelected(event) {
			// indica si sera una subida multiple o individual
			// Si es individual se toman el id y el nombre del seleccioando
			// sino es multiple se toman los elementos del array (copyDocuments)
			if (!event.currentTarget.dataset.multiple) {
				this.idDocument 		= event.currentTarget.dataset.id;
				this.nameDocument 		= event.currentTarget.dataset.name;
				this.multipleSelection 	= false;
			} else {
				this.idDocument 		= null;
				this.nameDocument 		= null;
				this.multipleSelection 	= true;
			}
		},

		/**
		 * Comprobar cuales de los documentos puedens er copiados y cuales no
		 *
		 * -> Los documentos que no pueden ser copiados es porque anteriormente
		 * han sido copiados o porque no han sido enviados
		 *
		 * @param {*} document 		El documento a evaluar
		 */
		checkWhichDocumentsCanBeCopied(document){

			// si esta check
			if (document.checked) {

				// si se puede copiar se agrega al array
				if (document.dataset.copy == "yescopy") {

					const docExists = this.copyDocuments.filter(doc => doc.id == document.value).length;

					// si no esta repetido en el array se agrega
					if (!docExists) {
						this.copyDocuments.push({
							id: document.value,
							name: document.dataset.name,
						});
					}
				}

			// si no esta check
			}else{

				// si el array contiene algun dato
				if (this.copyDocuments.length) {
					const indice = this.copyDocuments.map(doc => doc.id).indexOf(document.value);
					if (indice != -1) {
						this.copyDocuments.splice(indice, 1);
					}
				}
			}
		},

		/**
		 * Comprobar cuales son los documentos que se pueden compartir
		 *
		 * -> los documentos que no se pueden compartir son los que
		 * no han sido marcados como enviados
		 *
		 * @param {*} document 		El documento a evaluar
		 */
		checkWhichDocumentsCanBeShared(document){

			// si esta check
			if (document.checked) {

				// si se puede compartir se agrega al array
				if (document.dataset.share == "yesshare") {

					const docExists = this.shareDocuments.filter(doc => doc.id == document.value).length;

					// si no esta repetido en el array se agrega
					if (!docExists) {
						this.shareDocuments.push({
							id: document.value,
						});
					}
				}

			// si no esta check
			}else{

				// si el array contiene algun dato
				if (this.shareDocuments.length) {
					const indice = this.shareDocuments.map(doc => doc.id).indexOf(document.value);
					if (indice != -1) {
						this.shareDocuments.splice(indice, 1);
					}
				}
			}
		},

		/**
		 * Verificar si los documentos seleccioandos cumplen con ciertas validaciones o no
		 * para luego ser procesados
		 *
		 * @param {*} copied 		Si es un proceso de copia
		 * @param {*} shared 		si es un proceso de comparticion
		 */
		checkDocuments(copied = false, shared = false){

			this.allDocuments.forEach(document => {

				// si se puede copiar a la lista de archivos
				if (copied) {
					this.checkWhichDocumentsCanBeCopied(document);
				}

				// si se puede compartir
				if(shared){
					this.checkWhichDocumentsCanBeShared(document);
				}
			});
		},
	},
});
