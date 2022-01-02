/**
 * Selecciona los usuarios que interveniene como censo en el evento
 *
 * @author LuisBarDev <luisvardev@gmail.com> <luisbardev.com>
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
        address     : null,

        // Si se añade o no el usuario a la lista de contactos del usuario
        addSignerToContactList : false,

        // La lista de los usuarios
        signers     : [],

        // El número máximo de usuarios que se pueden seleccionar¡
        maxSigners  : null,

        // El mensaje de error cuando se produce
        error       : null,

        // mensajes de la app
        messages: {
            serverError: null
        },
    },

    /**
     * Carga la tabla de usuarios
     */
    mounted: function () {
        this.arrayFromList();

        // El contexto actual
        const $this = this;

        // maximo de usuarios permitidos
        $this.maxSigners = Infinity;

        // Obtiene la lista de usuarios que intervienen en el evento
        $this.signers = JSON.parse(document.getElementById('signers').dataset.signers);

        // cargar mensajes de la app
        this.messages.serverError = document.getElementById('messages').dataset.serverError;
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
                    position    : signerData.position,
                    address     : signerData.address
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
         * Elimina un usuario
         *
         * @param {Number} index    El índice del usuario a eliminar en la tabla
         */
        removeSigner: function (index) {
            this.signers.splice(index, 1);
        },

        /**
         * Añade un nuevo usuario a la lista de usuarios
         *
         * @param {Event} event
         */
        addNewSigner: function (event) {

            // Crea un usuario clonando la entrada del formulario actual
            const signer =
                {
                    name        : this.name,
                    lastname    : this.lastname,
                    email       : this.email,
                    phone       : this.phone,
                    dni         : this.dni,
                    company     : this.company,
                    position    : this.position,
                    address     : this.address,
                };

            // Si la dirección de correo del usuario ya está incluida en la lista
            // no permite que se vuelva a incluir en el listado de usuarios
            var signerAlreadySelected = this.signers.find(selectedSigner => selectedSigner.email == signer.email);

            if (typeof signerAlreadySelected !== 'undefined') {
                this.error = email.dataset.messageEmailExists;
                toastr.error(this.error);
                return false;
            }

            // Lo añade a la lista de usuario
            this.signers.push(signer);

            // Si se ha marcado que se guarde el usuario en la lista de contactos
            if (this.addSignerToContactList) {

                // Guarda el usuario en la lista de contactos del usuario
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
         * Limpia el formulario de creación de usuarios
         */
        clearNewSigner: function () {
            this.name       = null;
            this.lastname   = null;
            this.email      = null;
            this.phone      = null;
            this.dni        = null;
            this.company    = null;
            this.position   = null;
            this.address    = null;
            this.error      = null;
            this.addSignerToContactList = false;
        },

        /**
         * Editar la direccion de un elemento agregado como participante al censo
         *
         * @param {*} index             El index del array
         */
        editAddress(index) {
            this.signers[index].address = document.getElementById('addressEditable'+index).value;
        },

        /**
         * Guarda un usuario en la lista personal de contactos
         *
         * @param {Signer} signer   El usuario
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
         */
         saveCensus(event) {

            // contexto actual
            const $this = this;

            // dataset necesaria del evento
            const dataset = {
                urlSaveCensus: event.currentTarget.dataset.saveCensus,
                isDraft: event.currentTarget.dataset.draft,
            }

            // Muestra la animación
            HoldOn.open({theme: 'sk-circle'});

            axios.post(dataset.urlSaveCensus, {
                users: $this.signers,
                isDraft: dataset.isDraft
            })
            .then((response) => {

                // Oculta la animación
                HoldOn.close();

                // url para redirigir
                if (response.data.url) {
                    location.href = response.data.url;
                }else{
                    toastr.error($this.messages.serverError);
                }
            })
            .catch(error => {

                // muestra el error en la consola
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
                        let href = element.childNodes[1] ? element.childNodes[1].href : null;
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
         cancelCensusModal() {
            this.$bvModal.show('cancel-census');
        },
    },
    computed: {

        /**
         * Comprueba si se ha superado el número de usuarios que pueden ser seleccinados
         *
         * @return {Boolean}
         */
        maxSignerExceed: function () {
            return this.signers.length >= this.maxSigners;
        },

        /**
         * Comprueba si los datos del nuevo usuario añadido son válidos
         *
         * Se exige una dirección de correo válida o, alternativamente,
         * un número de teléfono
         *
         * @return {Boolean}
         */
        whenInvalidData: function () {
            return !mailRegex.test(this.email) && !this.phone;
        },

        /**
         * Comprueba si la dirección de correo es válida
         *
         * @return {Boolean}
         */
        emailIsInvalid: function () {
            return !mailRegex.test(this.email);
        },

        /**
         * Comrpueba si se han definido usuarios o no
         *
         * @return {Boolean}
         */
         ifNotUsers: function () {
            return this.signers.length == 0;
        },
    }
 });