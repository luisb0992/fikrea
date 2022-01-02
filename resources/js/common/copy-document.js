/**
 * Guarda y copiar un documento en todas las secciones que se amerite
 *
 * @author LuisBarDev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

/**
 * Plugin global para guardar y copiar un documento a files
 */
const saveAndCopyDocumentToFiles = {
	install(Vue, options) {

		// cargar axios
		const { default: Axios } = require("axios");

        let idDocument;

        Vue.prototype.onlyLoadDocumentSelected = (event) => {
            idDocument = event.currentTarget.dataset.id;

            if (document.getElementById('showNameDocument')) {
                document.getElementById('showNameDocument').textContent = event.currentTarget.dataset.name;
            }
        }

		Vue.prototype.onlyCopyDocumentToFiles = (event) => {

			// Url de guardado y listado de archivos
			const url = {
				urlCopyDocument: event.currentTarget.dataset.urlCopyDocument,
				urlListFiles:    event.currentTarget.dataset.urlFiles,
			};

			// mensajes de la app
			const msj = {
				emptySelection: event.currentTarget.dataset.emptySelection,
				error:          event.currentTarget.dataset.error,
			};

            // cargar la carpeta a guardar
			const parentId   = document.getElementById('parentID').value;

            if (!idDocument) {
                toastr.error(msj.error);
                return false;
            }

			// Inicia la animación
			HoldOn.open({ theme: "sk-circle" });

			// Los datos de el o los documentos
			let dataDocument = [];

			dataDocument.push({
				idDocument: idDocument,
				parentId: parentId,
			});

			Axios
				.post(url.urlCopyDocument, { dataDocument: dataDocument })
				.then((response) => {
					// Detiene la animación
					HoldOn.close();

					// si algun documento no pudo ser copiado
					if (response.data.failedProcess) {
						const msj = response.data.failedProcess;
						const infoMsj = response.data.infoFailedProcess;
						const documentNoCopy = response.data.documentNoCopy;

						const listDocumentNoCopy = documentNoCopy.forEach(
							(document) => `<li>${document}</li>`
						);

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
					// this.$bvModal.hide("move-document-to-files-modal");
                    document.getElementById("closeCopyToFilesModal").click();

					// ir a la pagina de archivos
					location.href = url.urlListFiles;
				})
				.catch((error) => {
					// Detiene la animación
					HoldOn.close();
					console.error(error);
				});
		};
	},
};

Vue.use(saveAndCopyDocumentToFiles);
