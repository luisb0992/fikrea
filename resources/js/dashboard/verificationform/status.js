/**
 * Muestra estado de una verificación de datos fuera del proceso de un documento
 *
 * @author LuisBarDev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

const { default: Axios } = require("axios");

new Vue({
	el: "#app",
	data: {
		verificationWasSharing: false,       // Si la verificación se ha vuelto a compartir o no
		                                // Es posible reenviar la verificación de datos a los usuarios firmantes que aún no
		                                // han completado la referida solicitud
	},
	mounted: function() {

        // Obtiene el idioma de la página
		const lang = document.querySelector("html").getAttribute("lang");
	},
	methods: {

		/**
		 * Envía la verificación de datos a los firmantes que aún no ha efectuado la validación
		 * No se admite más de un envío diario
		 *
		 * @param {Event} event
		 */
		sendVerification: function(event) {

			const $this = this;

			const sendVerificationform = event.target.dataset.sendVerificationform;

            // Muestra la animación
            HoldOn.open({theme: 'sk-circle'});

			Axios.post(sendVerificationform)
				.then(() => {

                    // Oculta la animación
                    HoldOn.close();

					// El documento ha sido compartido con los usuarios firmantes que aún no ha realizado los
					// procesos de validación propuestos por el autor/creador del documento
					$this.verificationWasSharing = true;
					$this.$bvModal.show("verification-sended-success");
				})
				.catch((error) => {

					console.error(error);

                    // Oculta la animación
                    HoldOn.close();
				});
		},
	},
});
