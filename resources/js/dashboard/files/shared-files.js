const {default: Axios} = require("axios");

new Vue({
    el: '#app',

    data: {
        // La posición del usuario datum WGS84
        position: {
            latitude: null,
            longitude: null,
        },

        file: {}, // El fichero que se mostrará en la vista previa
    },

    /**
     * Cuando la instancia está montada
     *
     */
    mounted: function () {
        const self = this;

        // Intentamos obtener la geolocalización del firmante
        this.getCurrentPosition()
            .then(() => {
                console.info(`[INFO] Se ha obtenido la posición del firmante con éxito`);
            })
            .catch(() => {
                console.info(`[INFO] No se ha podido obtener la posición del firmante`);
            });

        // Activar el plugin de bloqueo de pantalla
        HoldOn.open({theme: "sk-circle"});

        // Registrar el acceso al compartido
        const urlToLog = window.location.origin + window.location.pathname + '/log';

        axios.post(urlToLog, {position: self.position}).then(() => {
            HoldOn.close(); // desactivar el plugin de bloqueo de pantalla
            // toastr.success(self.success); // Mensaje de proceso completado
        }).catch((error) => {
            HoldOn.close();
            console.error(error);
        });
    },

    methods: {
        /**
         * Muestra el modal donde se listan todos los archivos que se van a compartir
         *
         * @param {int} id
         */
        preview: function (id) {
            // Obtener toda la información que se necesita mostrar del archivo seleccionado
            axios
                .get(route('dashboard.files.single-file-info', {'id': id}))
                .then(response => {
                    this.file = response.data;

                    this.$bvModal.show('shared-file-preview');
                })
                .catch(error => {
                    console.error('Ocurrió un error');
                });
        },

        clean: function () {
            // TODO: Borrar el fichero copiado temporalmente

            this.$bvModal.hide('shared-file-preview');
        },

        /**
         * Obtiene la posición actual
         *
         * @returns {Promise}       Una promesa con la posición calculada o un error
         */
        getCurrentPosition: function () {
            const self = this;

            return new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition((pos) => {
                    self.position.latitude = pos.coords.latitude;
                    self.position.longitude = pos.coords.longitude;

                    resolve(self.position);
                }, (error) => {
                    reject(error);
                }, {enableHighAccuracy: true, timeout: 5000, maximumAge: 0,});
            });
        },
    }
});
