<?php

use App\Http\Controllers\FileSharingController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\FileController;

/**
 * Rutas para la subida de archivos de la aplicación
 *
 * /file/...
 *
 *
 * @author    javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

/**
 * Todas las rutas que requieren idioma llevan el middleware 'language'
 * Para la manipulación de archivos se necesita subscripción
 */
Route::group(
    ['middleware' => ['language', 'subscription']],
    function () {
        /**
         * Muestra la vista para subir archivos
         *
         * nombre: dashboard.file.upload
         */
        Route::get('upload', [FileController::class, 'upload'])->name('dashboard.file.upload');

        /**
         * Sube y guarda los archivos. Opcionalmente, si como parámetro en el query string se indica un ID de una carpeta
         * los ficheros son almacenados directamente en esta.
         *
         * nombre: dashboard.file.save
         */
        Route::post('save', [FileController::class, 'save'])->name('dashboard.file.save');

        /**
         * Lista los archivos
         *
         * @param count La cantidad de archivos a mostrar en el paginado
         *
         * nombre: dashboard.file.list
         */
        Route::get('list/{count?}', [FileController::class, 'list'])
            ->name('dashboard.file.list')
            ->where('count', '[0-9]+');

        /**
         * Muesta la vista para compartir los archivos
         *
         * nombre: dashboard.file.share
         */
        Route::get('share', [FileController::class, 'share'])->name('dashboard.file.share');

        /**
         * Elimina un archivo
         *
         * nombre: dashboard.file.delete
         */
        Route::get('delete/{id}', [FileController::class, 'delete'])->name('dashboard.file.delete');

        /**
         * Elimina un conjunto constituido por uno o más archivos
         *
         * nombre: dashboard.files.delete
         */
        Route::post('/delete/multiple', [FileController::class, 'deleteMultiple'])
            ->name('dashboard.files.delete');

        /**
         * Lleva un archivo al proceso de firma
         *
         * nombre: dashboard.file.sign
         */
        Route::get('sign/{id}', [FileController::class, 'sign'])
            ->name('dashboard.file.sign')
            ->where('id', '[0-9]+');

        /**
         * Guarda un conjunto de archivos seleccionados para ser compartidos
         * con uno o más destinatarios
         *
         * nombre: dashboard.share.file.set
         */
        Route::post('set/save', [FileController::class, 'saveFileSet'])
            ->name('dashboard.share.file.set');

        /**
         * Guarda la compartición de archivos realizada que comprende
         * un conjunto de archivos elegidos por el usuario y un conjunto
         * de destinatarios que recibierán un enlace para la descarga
         *
         * nombre: dashboard.save.file.sharing
         */
        Route::post('/save/sharing', [FileController::class, 'saveFileSharing'])
            ->name('dashboard.save.file.sharing');

        /**
         * Muestra vista con listado de archivos para firma múltiple
         *
         * nombre: dashboard.files.multiple.sign
         */
        Route::post('/sign/multiple', [FileController::class, 'showFilesToSignMultiple'])->name(
            'dashboard.files.multiple.sign'
        );

        /**
         * Envía los archivos firmables de la selección múltiple para ser convertidos en un documento pdf
         *
         * nombre: dashboard.files.multiple.sign.save
         */
        Route::post('/sign/multiple/save', [FileController::class, 'saveFilesToSignMultiple'])->name(
            'dashboard.files.multiple.sign.save'
        );

        /**
         * Envía lel nombre y el location del nuevo archivo generado tras la fusión de los archivos seleccionados
         *
         * nombre: dashboard.files.multiple.info.save
         */
        Route::post('/sign/multiple/save/info/file/{id}', [FileController::class, 'saveFilesInfo'])->name(
            'dashboard.files.multiple.info.save'
        );

        /**
         * Obtiene el contenido de un archivo en base64
         *
         * nombre: dashboard.files.get.content.b64
         */
        Route::get('/get/content/b64/{file}', [FileController::class, 'getFileBase64Content'])->name(
            'dashboard.files.get.content.b64'
        );

        Route::name('dashboard.files.')->group(
            static function () {
                // Listado de ficheros subidos en estado bloqueado
                Route::get('locked', [FileController::class, 'locked'])->name('locked');
                // Listado de ficheros subidos en estado bloqueado
                Route::get('selected', [FileController::class, 'selected'])->name('selected');
                // Listado de comparticiones
                Route::get('sharing', [FileController::class, 'sharing'])->name('sharing');
                // Listado de comparticiones (poblar información del Datatables)
                Route::get('sharing-data', [FileController::class, 'sharingDatatable'])->name(
                    'sharing-datatable'
                );
                // Obtiene un JSON con la información del fichero indicado
                Route::post('info', [FileController::class, 'info'])->name('info');
                // Selección de carpeta para mover dentro una selección múltiple de ficheros (formulario)
                Route::post('move', [FileController::class, 'multipleMove'])->name('multiple-move');
                // Mover una selección múltiple de ficheros hacia el interior de una carpeta (ejecutar acción)
                Route::post('do-move', [FileController::class, 'multipleDoMove'])->name('multiple-do-move');
                // Descarga de una selección múltiple de archivos
                Route::post('download/multiple', [FileController::class, 'downloadMultiple'])->name('download');
                // Obtiene un JSON con la información del fichero indicado
                Route::get('{id}/info', [FileController::class, 'singleFileInfo'])->name('single-file-info');
                // Selección de carpeta para mover dentro un fichero (formulario)
                Route::get('{id}/move', [FileController::class, 'move'])->name('move');
                // Historial de comparticiones
                Route::get('{id}/sharing-history', [FileController::class, 'sharingHistory'])->name('sharing-history');
                // Historial de comparticiones (poblar información del Datatables)
                Route::get('{id}/sharing-history-data', [FileController::class, 'sharingHistoryDatatable'])->name(
                    'sharing-history-datatable'
                );
                // Generar certificado de la compartición
                Route::get('{id}/certificate', [FileController::class, 'certificate'])->name('certificate');
                // Mover un fichero hacia el interior de una carpeta (ejecutar acción)
                Route::put('{id}/do-move', [FileController::class, 'doMove'])->name('do-move');
                // Editar un fichero o carpeta (ejecutar acción)
                Route::put('{id}', [FileController::class, 'update'])->name('update');
                // Editar un fichero o carpeta (formulario)
                Route::get('{id}', [FileController::class, 'edit'])->name('edit');
                // Listado del historial de acceso a un fichero (interfaz)
                Route::get('{id}/historial', [FileController::class, 'history'])->name('history');
                // Listado del historial de acceso a un fichero (poblar información del Datatables)
                Route::get('{id}/historial-datos', [FileController::class, 'historyDatatable'])->name(
                    'history-datatable'
                );
            }
        );

        /**
         * Rutas asociadas a la gestión de carpetas
         */
        Route::prefix('folders')->name('dashboard.folders.')->group(
            static function () {
                // Creación de una carpeta (ejecutar acción)
                Route::post('new', [FileController::class, 'storeFolder'])->name('store');
                // Creación de una carpeta (formulario)
                Route::get('new', [FileController::class, 'createFolder'])->name('create');
            }
        );
    }
);

Route::middleware('language')->prefix('sharing')->name('dashboard.file-sharing.')->group(
    function () {
        // Registrar el acceso a los ficheros de una compartición
        Route::post('{id}/log', [FileSharingController::class, 'log'])->name('log');
        // Eliminar una compartición
        Route::delete('{id}/destroy', [FileSharingController::class, 'destroy'])->name('destroy');
    }
);
