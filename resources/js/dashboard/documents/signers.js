/**
 * Selecciona los firmantes del documento
 * 
 * Un documento puede tener, además del propio autor, firmantes
 * 
 * @author javieru <javi@gestoy.co>
 * @copyright 2021 Retail Servicios Externos
 */

// La expresión regular de validación de una dirección de correo RFC 5322
const mailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

const { default: Axios } = require("axios");

new Vue({
    el: '#app',
    data: {

        // Los campos del formulario
        name        : null,
        lastname    : null,
        email       : null,
        phone       : null,
        dni         : null,
        company     : null,
        position    : null,

        // Si se añade o no el firmante a la lista de contactos del usuario
        addSignerToContactList : false,

        // La lista de los firmantes
        signers     : [],

        // El número máximo de firmantes que se pueden seleccionar
        // y que dependerá del plan de susbcripción
        maxSigners  : null,

        // El mensaje de error cuando se produce
        error       : null,

        // Los mensajes de la aplicación
        message     :
            {
                howToAddNewSigners : document.getElementById('messages').dataset.howToAddNewSigners,
            },

        // Id del Documento
        document : null
    },
    /**
     * Carga la tabla de firmantes
     */
    mounted: function () {
        // El contexto actual
        const $this = this;

        // Fija el número máximo de firmantes que pueden ser seleccionados
        const maxSigners = document.getElementById('max-signers').value;
        
        if (maxSigners) {
            $this.maxSigners = parseInt(maxSigners);
        } else {
            $this.maxSigners = Infinity;
        }
       
        // Obtiene la lista de firmantes del documento
        let signers = JSON.parse(document.getElementById('signers').dataset.signers);
        // Se omite el autor o creador del documento
        $this.signers = signers.filter(signer => !signer.creator);

        // Obtiene id del documento
        $this.document = document.getElementById('data').dataset.document;

        // Muestra un mensaje indicando que se pueden añadir firmantes a la lista
        toastr.info(this.message.howToAddNewSigners);

        $this.arrayFromList();
    },
    methods: {
        /**
         * Añade un contacto desde la lista de contactos a la lista de firmantes
         * 
         * @param {Event} event 
         */
        addContactToSigners: function (event) {

            const signerData = event.target.dataset;

            // Obtiene los datos del contacto
            const signer = 
                {
                    name        : signerData.name,
                    lastname    : signerData.lastname,
                    email       : signerData.email,
                    phone       : signerData.phone,
                    dni         : signerData.dni,
                    company     : signerData.company,
                    position    : signerData.position
                };

            // Si la dirección de correo del firmante ya está incluida en la lista
            // no permite que se vuelva a incluir en el listado de firmantes
            var signerAlreadySelected = this.signers.find(selectedSigner => selectedSigner.email == signer.email);

            if (typeof signerAlreadySelected !== 'undefined') {
                this.error = email.dataset.messageEmailExists;
                toastr.error(this.error);
                return false;
            }

            // Lo añade a la table de firmantes
            this.signers.push(signer);
        },
        /**
         * Elimina un firmante
         * 
         * @param {Number} index    El índice del firmante a eliminar en la tabla 
         */
        removeSigner: function (index) {
            this.signers.splice(index, 1);
        },
        /**
         * Añade un nuevo firmante a la lista de firmantes
         * 
         * @param {Event} event
         */
        addNewSigner: function (event) {

            // Crea un firmante clonando la entrada del formulario actual
            const signer = 
                {
                    name        : this.name,
                    lastname    : this.lastname,
                    email       : this.email,
                    phone       : this.phone,
                    dni         : this.dni,
                    company     : this.company,
                    position    : this.position
                };
            
            // Si la dirección de correo del firmante ya está incluida en la lista
            // no permite que se vuelva a incluir en el listado de firmantes
            var signerAlreadySelected = this.signers.find(selectedSigner => selectedSigner.email == signer.email);

            if (typeof signerAlreadySelected !== 'undefined') {
                this.error = email.dataset.messageEmailExists;
                toastr.error(this.error);
                return false;
            }

            // Lo añade a la lista de firmante del documento
            this.signers.push(signer);

            // Si se ha marcado que se guarde el firmante en la lista de contactos
            if (this.addSignerToContactList) {
                
                // Guarda el firmante en la lista de contactos del usuario
                this.saveSignerAsContact(signer);
                
                // Incrementa el contador de "Mis Contactos"
                const badgeContacts = document.querySelector('.badge-contacts')
                let contacts = badgeContacts.innerHTML.trim();
                badgeContacts.innerHTML= ++contacts;
            }

            // Limpia el formulario
            this.clearNewSigner();
        },
        /**
         * Busca si existe ya contacto registrado con esa dirección de correo
         * 
         * @param {String} email    La dirección de correo del contacto
         */
        findContactByEmail: function (email) {
            
            if (!email) return;

            // El formulario de datos
            const form = this;

            // Obtiene la dirección de la solicitud
            const request = document.getElementById('email').dataset.requestEmail;

            // Consulta si la dirección de correo corresponde a un contacto del usuario
            axios.post(request, {
                email       : email,
            })
            .then(response => {
                
                const contact = response.data;
       
                // Completa el formulario con los datos
                form.name       = contact.name;
                form.lastname   = contact.lastname;
                form.email      = contact.email;
                form.phone      = contact.phone;
                form.dni        = contact.dni;
                form.company    = contact.company;
                form.position   = contact.position;

                // Añade el contacto a la lista de firmantes
                toastr.success('Obtenido de la lista de contactos');
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
        findContactByPhone: function (phone) {
            
            if (!phone) return;

            // El formulario de datos
            const form = this;

            // Obtiene la dirección de la solicitud
            const request = document.getElementById('phone').dataset.requestPhone;

            // Consulta si la dirección de correo corresponde a un contacto del usuario
            axios.post(request, {
                phone       : phone,
            })
            .then(response => {
                
                const contact = response.data;
       
                // Completa el formulario con los datos
                form.name       = contact.name;
                form.lastname   = contact.lastname;
                form.email      = contact.email;
                form.phone      = contact.phone;
                form.dni        = contact.dni;
                form.company    = contact.company;
                form.position   = contact.position;

                // Añade el contacto a la lista de firmantes
                toastr.success('Obtenido de la lista de contactos');
            })
            .catch(() => {
                // HTTP 404 No existe el contacto
            });
        },
        /**
         * Limpia el formulario de creación de firmantes
         *  
         */
        clearNewSigner: function () {
            this.name       = null;
            this.lastname   = null;
            this.email      = null;
            this.phone      = null;
            this.dni        = null;
            this.company    = null;
            this.position   = null;

            this.addSignerToContactList = false;

            this.error      = null;
        },
        /**
         * Guarda un firmante en la lista personal de contactos
         * 
         * @param {Signer} signer   El firmante
         */
        saveSignerAsContact: function (signer) {
           
            axios.post(form.action, {
                id          : null,
                name        : signer.name,
                lastname    : signer.lastname,
                email       : signer.email,
                phone       : signer.phone,
                dni         : signer.dni,
                company     : signer.company,
                position    : signer.position
            })
            .then(() => {
                toastr.success(form.dataset.messageSuccess);
            })
            .catch(error => {
                this.error = error.response.data.errors.email[0];
                toastr.error(form.dataset.messageFailed);
            });
        },
        /**
         * Guarda los firmantes
         * 
         * @param {Event} event 
         */
        saveSigners: function () {
                           
            let saveSignersRequest    = document.getElementById('save-signers-request').value;

            let setValidationsRequest = document.getElementById('set-validations-request').value;

            // Inicia la animación
            HoldOn.open({theme: 'sk-circle'});

            axios.post(saveSignersRequest, {
                signers: this.signers
            })
            .then(() => {
                // Pasa a la pantalla de validaciones
                location.href = setValidationsRequest;
            })
            .catch(error => {
                console.error(error);
                
                // Oculta la animación
                HoldOn.close();
            });
        },
        /**
         * Comprueba se esta cambiando de ruta
         *       
         * Verifica si se esta cambiando de ruta al oprimir cualquiera opcion del sidebar 
         * Envia el modal con la pregunta, si esta seguro de cambiar de ruta
         */
        arrayFromList: function () {
            var self = this;
            //Array con cada value:
            var dataValue = [...document.querySelectorAll(`ul.vertical-nav-menu li`)]
                .forEach((element) => {
                    if (element.className != "app-sidebar__heading") {
                        let href = element.childNodes[1].href;
                        element.addEventListener("click", async (e) => {
                            e.preventDefault();
                            await self.$bvModal.show('link-sidebar-modal');
                            let link = document.getElementById('linkSidebarModal');
                            link.href = href;
                        });
                    }
                });
        },

        /**
         * Muestra la modal de cancelación del proceso
         */
        cancelSignerModal: function (id) {
            this.$bvModal.show('cancel-signer-modal');
        },

        /**
         * Cancela el proceso de firma sobre el documento
         * 
         * Se envía petición para eliminar el documento que se ha generado en los pasos anteriores
         */
        cancelProcess: function (event) {
            const $this = this;

            const request = event.target.dataset.request;
            const redirect = event.target.dataset.redirect;

            // Inicia la animación
            HoldOn.open({theme: 'sk-circle'});

            axios.post(
                request, {documents:[$this.document]}
            ).then(response => {
                location.href = redirect;
            }).catch(error=>{
                console.error(error);
                // Oculta la animación
                HoldOn.close();
            });
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
        maxSignerExceed: function () {

            return this.signers.length >= this.maxSigners;
        },
        /**
         * Comprueba si los datos del nuevo firmanete añadido son válidos
         * 
         * Se exige una dirección de correo válida o, alternativamente,
         * un número de teléfono
         * 
         * @return {Boolean}
         *
         */
        whenInvalidData: function () {

            return !mailRegex.test(this.email) && !this.phone;
        },
        /**
         * Comprueba si la dirección de correo es válida
         *
         * @return {Boolean}
         * 
         */
        emailIsInvalid: function () {

            return !mailRegex.test(this.email);
        },
    }
 });