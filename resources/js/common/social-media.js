/**
 * Guarda las comparticiones mediante alguna rd social seleccioanda
 *
 * @author LuisBarDev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

/**
 * Plugin global para guardar una comparticion en una red social
 */
const saveShareAccordingToTheSocialNetwork = {
	install(Vue, options) {

        // cargar axios
        const { default: Axios } = require("axios");

        // -----------------------------------------------------------------
        // ------------------ Endpoint para cada red social ----------------
        // -----------------------------------------------------------------

        // compartir por facebook
        const shareToFaceboook = (url) => {
            return `https://www.facebook.com/sharer/sharer.php?u=${url}`;
        }

        // compartir por twitter
        const shareToTwitter = (hashtag = null, text = null, url) => {
            return `https://twitter.com/intent/tweet?hashtags=${hashtag}&text=${text}&url=${url}`;
        }

        // compartir por linkedin
        const shareToLinkedin = (url) => {
            return `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
        }

        // compartir por whatsapp
        const shareToWhatsapp = (text = null, url) => {
            return `https://api.whatsapp.com/send?text=${text}${url}`;
        }

        // -----------------------------------------------------------------
        // ----------- Procesos de guardado de la informcion ---------------
        // -----------------------------------------------------------------

        /**
         * Guardar la comparticion a una red social especifica
         *
         * @param {String} urlSave           La url donde se guarda la data
         * @param {Object} socialData        La data a ser guardada
         * @param {String} token             El token de acceso de la ruta a compartir
         */
        const saveSocialMedia = (urlSave, socialData, token) => {

            // la url o ruta a compartir con el token
            const routeShare = document.getElementById('divShareSocialNetwork').dataset.route;

            if (!routeShare) {
                HoldOn.close();
                return false;
            }

            // convertir a string y reemplazar el token
            stringRoute = routeShare.toString();
            stringRoute = stringRoute.replace('token', token);

            Axios.post(urlSave, {
                url:            stringRoute,
                social_network: socialData.social,
                text:           socialData.text,
                hashtag:        socialData.hashtag,
                type:           socialData.type,
                id:             socialData.id
            }).then((response) => {

                // cierra la animacion
                HoldOn.close();

                // si todo salio bien se comparte
                if (response.data.success) {

                    let urlShare = null;

                    // facebook
                    if (socialData.social == 1) {
                        urlShare = shareToFaceboook(stringRoute);

                    // Twitter
                    }else if(socialData.social == 2) {
                        urlShare = shareToTwitter(socialData.hashtag, socialData.text, stringRoute);

                    // Linkedin
                    }else if(socialData.social == 3) {
                        urlShare = shareToLinkedin(stringRoute);

                    // Whatsapp
                    }else if(socialData.social == 4) {
                        urlShare = shareToWhatsapp(socialData.text, stringRoute);

                    // No existe
                    }else{
                        return false;
                    }

                    // abrir una nueva ventana
                    if (urlShare) {
                        const winOpen = window.open(urlShare, '_blank');
                        if (winOpen) {
                            winOpen.focus();
                        }
                    }
                }

            }).catch((error) => {
                HoldOn.close();
                console.error(error);
            });
        }

        /**
         * Guardar y compartir mediante redes sociales
         *
         * @param {*} event         El evento del boton
         * @returns                 una nueva comparticion, si algo sale mal devuelve un mensaje
         */
		Vue.prototype.saveShareSocialMedia = (event) => {

            // el div que contiene lo necesario
            const divShareSocialNetwork = document.getElementById('divShareSocialNetwork');

            if (!divShareSocialNetwork) {
                return false;
            }

            // la url donde se guarda el proceso
            const urlSave = divShareSocialNetwork.dataset.urlSave;

            // La url donde se guarda la comparticion de un archivo al historial
            const urlSaveShareFile = divShareSocialNetwork.dataset.urlSaveShareFile;

            // datos del link social
            const socialData = {
                id:         event.target.dataset.id,            // el id del proceso que se va a compartir (obligatorio)*
                text:       event.target.dataset.text,          // un texto a mostrar (opcional)
                hashtag:    event.target.dataset.hashtag,       // un hashtag a mostrar (opcional)
                social:     event.target.dataset.social,        // el tipo de red social a evaluar (obligatorio)*
                type:       divShareSocialNetwork.dataset.type, // el tipo de proceso que se va a compartir (obligatorio)*
            }

            // mensajes de la app
            const messageSocialData = {
                errorSocial:    divShareSocialNetwork.dataset.errorSocial,
                errorUrl:       divShareSocialNetwork.dataset.errorUrl,
                errorType:      divShareSocialNetwork.dataset.errorType,
            }

            // si el social no existe
            if (!socialData.social) {
                toastr.error(messageSocialData.errorSocial);
                return false;
            }

            // si el tipo no existe
            if (!socialData.type) {
                toastr.error(messageSocialData.errorType);
                return false;
            }

            // si el id no existe
            if (!socialData.id) {
                toastr.error(messageSocialData.errorType);
                return false;
            }

            // open animacion
            HoldOn.open({ theme: "sk-circle" });

            // si es una comparticion de arvhivo, se almacena la comparticion primero
            // para guardar en el historial y generar el token
            if (socialData.type == 'file') {
                Axios.post(urlSaveShareFile, {
                    no_contacts:    true,
                    title:          socialData.text,
                    description:    null,
                    files:          [{'id': socialData.id}]
                }).then((response) => {

                    // obtenemos el token de la respuesta
                    // y gendramos un nuevo registro de comparticion en redes sociales
                    if (response.data.token) {
                        saveSocialMedia(urlSave, socialData, response.data.token);
                    } else {
                        HoldOn.close();
                        toastr.error(messageSocialData.errorUrl);
                    }

                }).catch(error => {
                    console.error(error);
                    HoldOn.close();
                });
            }
		};
	},
};

Vue.use(saveShareAccordingToTheSocialNetwork);
