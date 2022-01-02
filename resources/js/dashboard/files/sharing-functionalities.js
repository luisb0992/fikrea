const logAccessToSharing = id => {
    // Registrar la compartición
    axios.post(route('dashboard.file-sharing.log'), {id: id})
        .catch(error => {
            console.error(error);
        });
};

$('tbody').on('click', '.btn-action-copy-url', function () {
    const id = $(this).attr('data-id');

    const token = $('input#token-' + id).val();
    const title = $('input#title-' + id).val();
    const description = $('input#description-' + id).val();

    const url = route('workspace.set.share', {token: token});

    // Si estamos en un sistema Android, la app se encarga de manejar la compartición de archivos
    if (window.AndroidShareHandler) {
        window.AndroidShareHandler.share(url);

        // Registrar que fue compartido, para mostrar en el historial
        logAccessToSharing(id);
    } else if (navigator.share) {
        navigator.share({title: title, text: description, url: url}).then(() => {
            // Registrar que fue compartido, para mostrar en el historial
            logAccessToSharing(id);

            console.info(`[OK] La dirección ${url} se ha compartido con éxito`);
        }).catch(error => {
            console.error(`[ERROR] No se ha podido compartir. ${error}`);
        });
    } else if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
            // Registrar que fue compartido, para mostrar en el historial
            logAccessToSharing(id);

            toastr.success('Copiado al portapapeles');
            console.info(`[OK] Se ha copiado la url ${url} en el portapapeles`);
        }).catch(error => {
            console.error(`[ERROR] No se ha podido copiar la URL en el portapapeles. ${error}`);
        });
    }
});
