// Esto no se puede dar por hecho en todas las vistas
// Lo correcto es verificar que exista el elemento raíz para crear la instancia de vue
if (document.getElementById('app-menu')) {
    new Vue({
        el: '#app-menu',

        /**
         * Cuando la instancia está montada
         */
        mounted: function () {
            const menu = document.getElementById('selected-files-menu');

            // Únicamente aplicar esta lógica en las plantillas que muestran este menú
            if (null !== menu) {
                // Carga la lista de archivos seleccionados del almacenamiento local
                // desde la clave "selected-files"
                // Con ello se obtienen los archivos seleccionados en otras páginas diferentes a la actual
                const selectedFiles = localStorage.getItem('selected-files');
                const files = JSON.parse(selectedFiles) || [];

                // Determinar el total de archivos seleccionados
                const count = files.length;

                // Actualizar el contador de archivos seleccionados
                document.getElementById('selected-files-badge').innerHTML = count.toString();

                // Mostrar la opción en el menú únicamente si existen archivos seleccionados
                menu.style.display = count ? 'inherit' : 'none';
            }
        }
    });
}
