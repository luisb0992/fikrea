/**
 * Selecciona los usuarios a los cuales se les va a compartir el archivo o lista de archivos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

const mailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

const { default: Axios } = require("axios");

new Vue({
    el: '#app',
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
        addUserToContactList : false,

        // El listado de archivos compartidos
        files: [],

        // Los destinatios de los archivos
        users: [],

        // El número máximo de usuarios que se pueden seleccionar
        // y que dependerá del plan de susbcripción
        maxUsers  : null,

        // El mensaje de error caundo se produce
        error       : null,
    },
    /**
     * Cuando la instancia está montada
     *
     */
    mounted: function () {
        // Fija la lista de archivos compartidos
        if (document.getElementById('files')) {
            this.files = JSON.parse(document.getElementById('files').value);
        }

        // Fija el número máximo de firmantes que pueden ser seleccionados
        if (document.getElementById('max-users')) {
            this.maxUsers = document.getElementById('max-users').value;
        }
    },
    methods: {
        /**
         * Elimina un usuario
         *
         * @param {Number} index    El índice del usuario a eliminar en la tabla
         */
        removeUser: function (index) {
            this.users.splice(index, 1);
        },

        /**
         * Comparte los archivos con los usuarios indicados
         *
         * @param {Event} event
         *
         */
        share: function (event) {
            axios.post(route('dashboard.save.file.sharing'), {
                users: this.users,      // La lista de usuarios con los que se comparte
                files: this.files,      // La lista de archivos compartidos
                title: this.title,
                description: this.description
            })
                .then(() => {
                    // Redirige a la lista de comparticiones del usuario
                    location.href = route('dashboard.files.sharing');
                })
                .catch(error => {
                    console.error(error);
                });
        },

        /**
         * Añade un contacto desde la lista de contactos a la lista de usuarios
         *
         * @param {Event} event
         */
        addContactToSigners: function (event) {

            const userData = event.target.dataset;

            // Obtiene los datos del contacto
            const user =
                {
                    name        : userData.name,
                    lastname    : userData.lastname,
                    email       : userData.email,
                    phone       : userData.phone,
                    dni         : userData.dni,
                    company     : userData.company,
                    position    : userData.position
                };

            // Si la dirección de correo del usuario ya está incluida en la lista
            // no permite que se vuelva a incluir en el listado de usuarios
            var userAlreadySelected = this.users.find(selectedUser => selectedUser.email == user.email);

            if (typeof userAlreadySelected !== 'undefined') {
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
        clearNewUser: function () {
            this.name       = null;
            this.lastname   = null;
            this.email      = null;
            this.phone      = null;
            this.dni        = null;
            this.company    = null;
            this.position   = null;

            this.addUserToContactList = false;

            this.error      = null;
        },

        /**
         * Guarda un usuario en la lista personal de contactos
         *
         * @param {User} user   El usuario
         */
        saveUserAsContact: function (user) {

            axios.post(form.action, {
                id          : null,
                name        : user.name,
                lastname    : user.lastname,
                email       : user.email,
                phone       : user.phone,
                dni         : user.dni,
                company     : user.company,
                position    : user.position
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
         * Añade un nuevo usuario a la lista de usuarios
         *
         * @param {Event} event
         */
        addNewUser: function (event) {

            // Crea un usuario clonando la entrada del formulario actual
            const user =
                {
                    name        : this.name,
                    lastname    : this.lastname,
                    email       : this.email,
                    phone       : this.phone,
                    dni         : this.dni,
                    company     : this.company,
                    position    : this.position
                };

            // Si la dirección de correo del usuario ya está incluida en la lista
            // no permite que se vuelva a incluir en el listado de usuarios
            var userAlreadySelected = this.users.find(selectedUser => selectedUser.email == user.email);

            if (typeof userAlreadySelected !== 'undefined') {
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
        // Muestra el modal donde se listan todos los archivos que se van a compartir
        showAllFiles: function(){
            this.$bvModal.show('files-list-on-sharing');
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

            return this.users.length >= this.maxUsers;
        },
        /**
         * Comprueba si los datos del nuevo firmanete añadido son válidos
         *
         * Se exige una dirección de correo válida o, alternativamente,
         * un número de teléfono
         *
         */
        whenInvalidData: function () {

            return !mailRegex.test(this.email) && !this.phone;
        },
        /**
         * Comprueba si la dirección de correo es válida
         *
         */
        emailIsInvalid: function () {

            return !mailRegex.test(this.email);
        },
    }
});
