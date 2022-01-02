new Vue({
    el: '#app',
    data: {
        files: [], // La relación de ficheros seleccionados
    },

    /**
     * Cuando la instancia está montada
     *
     */
    mounted: function () {
        // Carga la lista de archivos seleccionados del almacenamiento local desde la clave "selected-files"
        // Con ello se obtienen los archivos seleccionados en otras páginas diferentes a la actual
        const selected = JSON.parse(localStorage.getItem('selected-files')) || [];

        // Obtener toda la información que se necesita mostrar de los archivos seleccionados, pues en el almacenamiento
        // local únicamente se guarda el ID
        axios
            .post(route('dashboard.files.info'), {'selected': selected})
            .then(response => {
                this.files = response.data;
            })
            .catch(error => {
                console.error('Ocurrió un error');
            });
    },

    methods: {
        deselect: function (id) {
            this.files = this.files.filter((file) => {
                return file.id !== id
            });

            // Guarda la lista de archivos seleccionados en el almacenamiento local
            localStorage.setItem('selected-files', JSON.stringify(this.files.map((file) => {
                return file.id;
            })));

            // Determinar el total de archivos seleccionados
            const count = this.files.length;

            // Actualizar el contador de archivos seleccionados
            document.getElementById('selected-files-badge').innerHTML = count.toString();

            // Mostrar la opción en el menú únicamente si existen archivos seleccionados
            document.getElementById('selected-files-menu').style.display = count ? 'inherit' : 'none';
        }
    }
});