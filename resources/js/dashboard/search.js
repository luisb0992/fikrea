/**
 * Búsqueda de Archivos
 * 
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

/**
 * Códigos de Tecclado
 */
const Key =
    {
        Enter   : 13,
    };

/**
 * El botón de búsqueda
*/
const searchButton = document.querySelector('.search-icon');

/**
 * El cuadro de búsqueda
 */
const searchInput = document.querySelector('.search-input');

/**
 * Ejecuta la búsqueda
 */
if (searchButton) {
    searchButton.addEventListener('click', function (event) {

        // Obtiene el texto de búsqueda
        const searchText = searchInput.value;

        if (!searchText) return;

        // Realiza la solicitud
        const searchRequest = this.dataset.searchRequest.replace('query', searchText);

        location.href = searchRequest;
    });
}

/**
 * El pulsar una tecla en el cuadro de búsqueda
 */
if (searchInput) {
    searchInput.addEventListener('keydown', function (event) {
        // Al pulsar la tecla Enter se ejecuta la búsqueda
        if (event.keyCode == Key.Enter) searchButton.click();
    });
}