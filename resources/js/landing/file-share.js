const mailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

const {default: Axios} = require("axios");

new Vue({
    el: '#app',
    data: {
        // El listado de archivos compartidos
        files: [],

        // El mensaje de error cuando se produce
        error: null,
    },
    /**
     * Cuando la instancia está montada
     *
     */
    mounted: function () {
        // Fija la lista de archivos compartidos
        // this.files = JSON.parse(document.getElementById('files').value);
    },

    methods: {
        // Muestra el modal donde se listan todos los archivos que se van a compartir
        showAllFiles: function () {
            this.$bvModal.show('files-list-on-sharing');
        },
    },
});
