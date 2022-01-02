/**
 * Maneja el perfil del usuario
 * 
 * @author javieru <javi@gestoy.co>
 * @copyright 2021 Retail Servicios Externos
 */

const { default: Axios } = require("axios");

/**
 * Muestra la fortaleza de la contraseña
 * 
 * @link https://github.com/miladd3/vue-simple-password-meter
 */
import passwordMeter from 'vue-simple-password-meter';
import Select2 from 'v-select2-component';
import listCountries from './countries.json';

new Vue({
    el: '#app',
    components: { passwordMeter, Select2 },
    data: {
                                                            // Si el usuario actual es invitado o no
        guest                : document.getElementById('guest').value == '1',
                                                            // Si la cuenta es de personal (0)
                                                            // o es una cuenta de empresa (1)
        type                 : document.getElementById('type').value,

        password             : null,                        // La contraseña
        password_confirmation: null,                        // La confirmación de contraseña
        passwordHidden       : true,                        // Mostrar/Ocultar la contraseña
        passwordFieldType    : 'password',
        file                 : null,                        // El archivo de imagen del usuario
        profileImage         : profileImage.dataset.src,    // La imagen del usuario
        codeCountries: [],                                  // Lista (array) de paises
        dial_code: document.getElementById('select-country').dataset.dial
            ? document.getElementById('select-country').dataset.dial
            : '+34',  // Valor del codigo de pais a mostrar por usuario, si es null sera (+34)
        company_dial_code: document.getElementById('company-bdial').dataset.bdial
            ? document.getElementById('company-bdial').dataset.bdial
            : '+34',  // Valor del codigo de pais a mostrar por usuario en la facturacion, si es null sera (+34)

        optionsCountries:[],    // Listado de paises de mundo
        companyCountry: '',     // Pais de la compannia

        stateInitialBilling: {
            companyName: document.getElementById('companyName').value ?? null,
            companyEmail: document.getElementById('companyEmail').value ?? null,
            companyAddress: document.getElementById('companyAddress').value ?? null,
            companyCodePostal: document.getElementById('companyCodePostal').value ?? null,
            companyPhone: document.getElementById('companyPhone').value ?? null,
            companyCity: document.getElementById('companyCity').value ?? null,
            companyProvince: document.getElementById('companyProvince').value ?? null,
            companyCountry: document.getElementById('companyCountry').value ?? null
        },

        // si el perfil se comparte con firma digital o no
        shareWithSignature: false,

        // si el perfil se comparte via link
        shareWithCopyLink: false,

        // La data que se comparte cuando se require generar un link
        billingData: {
            title: null,
            comment: null
        }

    },
    mounted: function() {

        const self = this;

        /*
        //obtenemos lo que se muestra en el select2
        let checkbox = document.getElementById('buttonCopyProfileToCompany');
        
        //si se hace check el valor es igual al dial_code del profile
        checkbox.addEventListener("change", function() {
            if (!this.checked) return;
            self.company_dial_code = self.dial_code
        });
        */

        // Obtenemos los países que vienen desde la vista
        if (document.getElementById('data')) {
            // Creo el arreglo de valores para el select de los países
            Object.entries(JSON.parse(document.getElementById('data').dataset.countries)).forEach(([key, value]) => {
                self.optionsCountries.push({id:key, text:value});
            });
        }

        // La info del país de la compañía
        self.companyCountry  = JSON.parse(document.getElementById('data').dataset.billingCountry);
        self.stateInitialBilling.companyCountry  = JSON.parse(document.getElementById('data').dataset.billingCountry);

        // Inicia el autocompletado de direcciones postales con Google Maps
        this.googleMapsAddressAutoComplete();

        // Si es un usuario invitado y no la cookie de ocultar la modal lo permite
        // se muestra una modal informativa para que complete los datos del perfil
        if (this.guest && Cookies.get('hide-guest-modal') !== 'true') {
            this.showHelpModalForGuestUser();
        }
        // Generar formato para select2 con la lista de paises y sus codigos obtenidos
        // de importar un archivo json
        listCountries.forEach(country => {
            let newContry = {
                id: country.dial_code,
                code: country.code
            };
            this.codeCountries.push(newContry);
        });
    },
    methods: {
        /**
         * Eventos del componente select2 de vue js de ejemplo
         */
        myChangeEvent(val){
            console.log(val);
        },

        mySelectEvent({id, text}){
            console.log({id, text})
        },
        /**
         * / Eventos del componente select2 de vue js de ejemplo
         */

        /**
         * Botón mostrar/ocultar la contraseña
         *
         */
        tooglePassword : function () {
            this.passwordHidden = !this.passwordHidden;
            this.passwordFieldType = this.passwordHidden ? 'password' : 'text';
        },
        /**
         * Al pulsar el botón "Guardar" para guardar los datos del perfil del usuario
         * 
         * @param {Event} event 
         *
         */
        saveProfile: function (event) {
            // Se crea una cookie para indicar que el usuario ha modificado
            // el perfil que se ha proporcionado por defecto
            
            Cookies.set('user-has-changed-profile', 'true', {
                sameSite : 'strict',    // Atributo SameSite
                expires  : 24,          // 1 día
            }); 
        },
        /**
         * Autocompletado de la dirección postal con Google Maps
         * 
         */
        googleMapsAddressAutoComplete: function () {
            
            // Los Elementos del Formulario
            const componentForm = 
                {
                    locality                    : 'long_name',
                    administrative_area_level_2 : 'long_name',
                    country                     : 'long_name',
                };
             
            /**
             * Dirección postal del usuario
             */

            // Obtiene la dirección postal
            const inputAddress = document.getElementById('address');

            // El desglose de la dirección postal en campos individuales
            const domId = 
                {
                    locality                    : 'city',
                    administrative_area_level_2 : 'province',
                    country                     : 'country',
                };

            // Fija el autocompletado, restringiendo la búsqueda a direcciones postales
            const autocomplete = new google.maps.places.Autocomplete(
                inputAddress, {
                    types: ['address']
                }
            );

            // la solicitud de autocompletado retonará los componentes de la dirección
            // como localidad, provincia o código postal
            autocomplete.setFields(['address_component']);

            // Cuando un lugar es seleccionado, se autocompletan los datos
            autocomplete.addListener('place_changed', function () {

                // Obtiene el lugar
                let place = autocomplete.getPlace();

                // Coger los detalles de la dirección y meterlo en cada input del desglose de dirección
                for (let i = 0; i < place.address_components.length; i++) {
                    let addressType = place.address_components[i].types[0];
                    
                    if (componentForm[addressType]) {
                        let value = place.address_components[i][componentForm[addressType]];
                        if(value) {
                            document.getElementById(domId[addressType]).value = value;
                        }
                    }
                }

            });

            /**
             * Dirección postal de la empresa
             */

            // Obtiene la dirección postal
            const inputCompanyAddress = document.getElementById('companyAddress');

            // Sin no está definido el campo para la dirección psotal de la compañía termina
            if (!inputCompanyAddress) return;

            // El desglose de la dirección postal en campos individuales
            const domCompanyId = 
                {
                    locality                    : 'companyCity',
                    administrative_area_level_2 : 'companyProvince',
                    country                     : 'companyCountry',
                };

            // Fija el autocompletado, restringiendo la búsqueda a direcciones postales
            const companyAutocomplete = new google.maps.places.Autocomplete(
                inputCompanyAddress, {
                    types: ['address']
                }
            );

            // la solicitud de autocompletado retonará los componentes de la dirección
            // como localidad, provincia o código postal
            companyAutocomplete.setFields(['address_component']);

            // Cuando un lugar es seleccionado, se autocompletan los datos
            companyAutocomplete.addListener('place_changed', function () {
              
                // Obtiene el lugar
                let place = companyAutocomplete.getPlace();
         
                // Coger los detalles de la dirección y meterlo en cada input del desglose de dirección
                for (let i = 0; i < place.address_components.length; i++) {
                    let addressType = place.address_components[i].types[0];
                    
                    if (componentForm[addressType]) {
                        let value = place.address_components[i][componentForm[addressType]];
                        if(value) {
                            document.getElementById(domCompanyId[addressType]).value = value;
                        }
                    }
                }

            });
        },
        /**
         * Muestra una modal de ayuda para el usuario invitado
         * 
         */
        showHelpModalForGuestUser: function () {
            this.$bvModal.show('help-for-guest-user');
        },
        /**
         * Oculta la modal de ayuda para el usuario invitado
         *
         */
        hideHelpModalForGuestUser: function (event) {
            // Oculta la modal
            this.$bvModal.hide('help-for-guest-user');

            // Fija el foco en el nombre
            document.getElementById('name').focus();

            // Fija una cookie para que no se vuelva a mostrar esta modal
            // Duración una hora
            Cookies.set('hide-guest-modal', 'true', {
                sameSite : 'strict',    // Atributo SameSite
                expires  : 1/24,        // 1 hora
            });
        },
        /**
         * Recupera una sesión anterior
         * 
         * @param {Event} event
         *  
         */
        recoverySession: function (event) {
            const recoverySessionRequest = event.target.dataset.sessionRecoveryRequest;
            location.href= recoverySessionRequest;
        },
        /**
         * Cambia la contraseña del usuario
         * 
         * @param {Event} event
         * 
         */
        changeUserPassword: function (event) {

            // Obtiene el formulario
            let form = this;

            axios.post(formUserCredentials.action, 
                {
                    password                : form.password,
                    password_confirmation   : form.password_confirmation
                }
            )
            .then(response => {
                console.info(response.data.message);

                // Marca los campos de contraseña como válidos
                password.classList.remove('is-invalid');
                password.classList.add('is-valid');
                password_confirmation.classList.remove('is-invalid');
                password_confirmation.classList.add('is-valid');

                toastr.success(formUserCredentials.dataset.messageSuccess);
            })
            .catch(error => {
                
                if (error.response.status == 422) {
                    // Se muestran los errores producidos
                    let errors = error.response.data.errors.password.join(' ');
                    toastr.error(errors);

                    // Marca los campos de contraseña con error
                    password.classList.remove('is-valid');
                    password.classList.add('is-invalid');
                    password_confirmation.classList.remove('is-valid');
                    password_confirmation.classList.add('is-invalid');
                } else  {
                    toastr.error(formUserCredentials.dataset.messageFailed);
                }
            });

        },
        /**
         * Abre el explorador de archivos para subir la imagen del perfil del usuario
         * 
         * @param {Event} event
         *  
         */
        openFileBrowser: function (event) {
            document.querySelector('.custom-file-input').click();
        },
        /**
         * Sube el archivo de imagen del perfil
         * 
         * @param {Event} event 
         */
        uploadImage: function (event) {

            // Obtiene el formulario
            const form = this;

            // Obtiene el archivo
            const file = event.target.files[0];

            // Comprueba si es un archivo de imagen
            if (!this.isFileImage(file)) {
                toastr.error(imageForm.dataset.messageFailed);
                return false;
            }

            // Fija el nombre del archivo subido
            form.file = file.name;
            
            // Carga la vista previa de la imagen
            var reader = new FileReader();

            reader.readAsDataURL(file);
            reader.onload = function (event) {      
                form.profileImage = event.target.result;
            };

            var formData = new FormData();
            formData.append('image', file);   

            HoldOn.open({theme: 'sk-circle'});

            // Sube el archivo al servidor
            axios.post(
                imageForm.action, 
                formData,
                {
                    headers: 
                        {
                            'Content-Type': 'multipart/form-data'
                        }
                }
            )
            .then(response => {

                HoldOn.close();

                // Cambia el icono de la barra de navegación
                // de forma no reactiva pues el icono sólo se modificaría desde la pantalla de perfil 
                const newProfileImage = document.getElementById('profileImage').getAttribute('src');

                document.querySelector('.profile-icon').setAttribute('src', newProfileImage);

                console.info(response.data.message);
                toastr.success(imageForm.dataset.messageSuccess);
            })
            .catch(error => {
                HoldOn.close();

                console.log(error);
                
                toastr.error(imageForm.dataset.messageFailed);
            });

        },
        /**
         * Comprueba si un archivo es una imagen
         *
         * @param {String} file     Un archivo
         *
         * @return {Boolean}        true si es una imagen admitida 
         */
        isFileImage: function (file) {
            const acceptedImageTypes = ['image/gif', 'image/jpeg', 'image/png', 'image/tiff'];
            return file && acceptedImageTypes.includes(file['type'])
        },
        /**
         * Dar formato al option del select2 con imagenes de banderas por pais
         *
         * @param {data} objet              data del pais
         * @param {container} container     elemento html contenedor de select2
         *
         * @return {string}        html del option del select2 
         */
        changeTemplate: function (data, container) {
            let result = $("<span></span>");
            let img = data.code ? '<img src="https://flagcdn.com/w20/'+ data.code +'.png" srcset="https://flagcdn.com/w40/'+ data.code +'.png 2x" width="20" alt="'+ data.code +'">' : '';
            result.html(img+' '+data.id);
            return result;
        },

        setCompanyDialCode: function () {
            document.getElementById('company-bdial').value = this.company_dial_code;
        },

        /**
         * Se escucha el evento que ocurre en el HTML
         *
         * @param {event}              evento que ocurre en el HTML
         *
         */
        comprueba: function (event) {
            console.log(event);
        },

        soloLetras : function (e) {
            var key = e.keyCode || e.which,
            tecla = String.fromCharCode(key).toLowerCase(),
            letras = " áéíóúabcdefghijklmnñopqrstuvwxyz",
            especiales = [8, 37, 39, 46],
            tecla_especial = false;

            for (var i in especiales) {
                if (key == especiales[i]) {
                    tecla_especial = true;
                    break;
                }
            }

            if (letras.indexOf(tecla) == -1 && !tecla_especial) {
                return e.preventDefault();
            }
        },

        soloNumeros: function (e) {
            var key = e.keyCode || e.which,
                tecla = String.fromCharCode(key).toLowerCase(),
                letras = " 0123456789-",
                especiales = [8, 37, 39, 46],
                tecla_especial = false;

            for (var i in especiales) {
                if (key == especiales[i]) {
                    tecla_especial = true;
                    break;
                }
            }

            if (letras.indexOf(tecla) == -1 && !tecla_especial) {
                return e.preventDefault();
            }
        },

        copyProfileToCompany: function (event) {

            if(!event.target.checked) {
                this.companyCountry = null;
                this.company_dial_code = document.getElementById('company-bdial').dataset.bdial || '+34',
                document.getElementById('companyName').value        = null;
                document.getElementById('companyEmail').value       = null;
                document.getElementById('companyAddress').value     = null;
                document.getElementById('companyCodePostal').value  = null;
                document.getElementById('companyPhone').value       = null;
                document.getElementById('companyCity').value        = null;
                document.getElementById('companyProvince').value    = null;
                document.getElementById('companyCountry').value     = null;
                return;
            }

            this.companyCountry = document.getElementById('country').value;
            this.company_dial_code = this.dial_code;

            document.getElementById('companyName').value = document.getElementById('company').value;
            document.getElementById('companyEmail').value = document.getElementById('email').value;
            document.getElementById('companyAddress').value = document.getElementById('address').value;
            document.getElementById('companyCodePostal').value = document.getElementById('code_postal').value;
            document.getElementById('companyPhone').value = document.getElementById('phone').value;
            document.getElementById('companyCity').value = document.getElementById('city').value;
            document.getElementById('companyProvince').value = document.getElementById('province').value;
            document.getElementById('companyCountry').value = document.getElementById('country').value;
        },

        /**
         * Muestra la modal para compartir los datos de facturacion
         * 
         * @param {Event} event
         *  
         */
        shareBillingData: function (event) {

            if (this.checkEmptyFields()) {
                return this.$bvModal.show('not-empty-billing-data');
            }

            if (this.checkUserHasSavedPreviously()) {
                return this.$bvModal.show('not-save-billing-data');
            }

            // si el perfil se comparte con firma digital o no
            this.shareWithSignature = event.currentTarget.dataset.signature ? true : false;

            return this.$bvModal.show('share-billing-data');
        },

        /**
         * Verificar si algun campo antes de la comparticion esta vacio
         *
         * @returns bool        Un boleano
         */
        checkEmptyFields() {
            return document.getElementById('companyName').value == ''
                    || document.getElementById('companyEmail').value == ''
                    || document.getElementById('companyAddress').value == ''
                    || document.getElementById('companyCodePostal').value == ''
                    || document.getElementById('companyPhone').value == ''
                    || document.getElementById('companyCity').value == ''
                    || document.getElementById('companyProvince').value == ''
                    || document.getElementById('companyCountry').value == '';
        },

        /**
         * Verificar si el usuario ha guardado los cambios anteriormente antes de compartir
         *
         * @returns bool        Un boleano
         */
        checkUserHasSavedPreviously(){
            return  this.stateInitialBilling.companyName != document.getElementById('companyName').value
                    || this.stateInitialBilling.companyEmail != document.getElementById('companyEmail').value
                    || this.stateInitialBilling.companyAddress != document.getElementById('companyAddress').value
                    || this.stateInitialBilling.companyCodePostal != document.getElementById('companyCodePostal').value
                    || this.stateInitialBilling.companyPhone != document.getElementById('companyPhone').value
                    || this.stateInitialBilling.companyCity != document.getElementById('companyCity').value
                    || this.stateInitialBilling.companyProvince != document.getElementById('companyProvince').value
                    || this.stateInitialBilling.companyCountry != document.getElementById('companyCountry').value;
        },

        /**
         * Compartir los datos de facturacion via link
         *
         * @param {*} event     El evento del boton
         * @returns             Modal con la info a enviar
         */
        shareViaLink(event) {

            if (this.checkEmptyFields()) {
                return this.$bvModal.show('not-empty-billing-data');
            }

            if (this.checkUserHasSavedPreviously()) {
                return this.$bvModal.show('not-save-billing-data');
            }

            // si el perfil se comparte con firma digital o no
            this.shareWithSignature = event.currentTarget.dataset.signature ? true : false;

            // activar modal de formulario y se comparte con la firma digital
            if (this.shareWithSignature) {
                return this.$bvModal.show('share-billing-data-via-link');

            // se envia sin la firma digital
            }else{
                return this.getLinkShareBillingData(event);
            }
        },

        /**
         * Guardar una comparticion de datos de facturacion y devolver el link a copiar
         *
         * @param {*} event     El evento del boton
         */
        getLinkShareBillingData(event){

            const $this = this;

            HoldOn.open({theme: 'sk-circle'}); // Inicio de animacion

			axios.post(event.currentTarget.dataset.url, {
				title: this.billingData.title,
				comment: this.billingData.comment,
				signature: this.shareWithSignature,
			})
			.then((response) => {

                HoldOn.close();									// Detener animacion

                // ocultar el modal si ha sido abierto
                $this.$bvModal.hide('share-billing-data-via-link');

                // error generando el link
                if (response.data.infoSharing) {
                    toastr.error(response.data.infoSharing);
                    return false;
                }

                // la url obtenida a compartir en la response
                const url = response.data.urlSharing;
                $this.generateLink(url);
			})
			.catch((error) => {
				HoldOn.close(); // Detener animacion
				console.error(error);
			});
        },

        /**
         * Retorna un link para ser copiado y compartido
         *
         * @param {*} url       El link a compàrtir
         */
        generateLink(url) {

            // mensajes de la app
            const message = {
                shareTitle:     document.getElementById('messageShare').dataset.shareTitle,
                shareText:      document.getElementById('messageShare').dataset.shareText,
                notAvailable:   document.getElementById('messageShare').dataset.notAvailable,
                blocked:        document.getElementById('messageShare').dataset.blocked,
                notSupport:     document.getElementById('messageShare').dataset.notSupport,
            }

            // setear la url en el input indicado por si se necesita forzar la copia
            document.getElementById('sharingUrl').textContent = url;

            // Si estamos en un sistema Android, la app se encarga de manejar la compartición de archivos
            if (window.AndroidShareHandler) {
                window.AndroidShareHandler.share(url);
            } else if (navigator.share) {

                navigator.share({
                    title: message.shareTitle,
                    text : message.shareText,
                    url  : url,
                })
                .then(() => {
                    console.info(`[OK] La dirección ${url} se ha compartido con éxito`);
                })
                .catch(error => {
                    console.error(`[ERROR] No se ha podido compartir. ${error}`)
                    console.error(error);
                });

            } else if (navigator.clipboard) {

                navigator.clipboard.writeText(url)
                .then(() => {
                    toastr.success(message.shareText);
                    console.info(`[OK] La dirección ${url} se ha compartido con éxito`);
                })
                .catch(error => {

                    // se intenta copiar mediante una forma tradicional
                    this.btnForce();
                });

            }else{
                toastr.error(message.notAvailable);
            }
        },

        /**
         * Dar click al boton que fuerza la copia de la url a compartir
         */
        btnForce(){
            document.getElementById('btnCopiar').click();
        },

        /**
         * Forzar la copia de la url de comparticion de los datos de facturacion
         * Sino funciona se devuelve un mensaje de error donde se explica el porque
         */
        forceCopy(){

            // mensajes de la app
            const message = {
                shareText:      document.getElementById('messageShare').dataset.shareText,
                notSupport:     document.getElementById('messageShare').dataset.notSupport,
            }

            const range = document.createRange();
            range.selectNodeContents(document.getElementById('sharingUrl'));
            window.getSelection().removeAllRanges();
            window.getSelection().addRange(range);

            try {
                const copy = document.execCommand('copy'); //Intento el copiado

                if (copy) {
                    toastr.success(message.shareText);
                }else{
                    toastr.error(message.notSupport);
                }
            }
            catch(ex) {
                console.log(ex);
            }

            window.getSelection().removeAllRanges();
        },
    },
    computed: {
        /**
         * Habilita/Deshabilita el botón de cambiar la contraseña
         * 
         * @param {Event} event 
         */
        isPasswordChangeDisabled: function (event) {
            return !this.password || !this.password_confirmation; 
        }
    },
 });