<?php

/**
 *
 * Rutas Web por defecto
 *
 * @author    javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

use App\Http\Controllers\MediaTypeController;
use Illuminate\Support\Facades\Route;

/**
 * Controladores necesarios
 */

use App\Http\Controllers\LandingController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use App\Models\ShortLink;

/**
 * Rutas de autenticación
 */
Auth::routes();

/**
 * Redirecciona a la landing page
 */
Route::get(
    '/',
    function () {
        return redirect()->route('landing.home');
    }
);

/**
 * Página que muestra la política de privacidad
 *
 */
Route::view('/privacity-policy', 'landing.privacity-policy');

/**
 * Descarga un archivo bien por su token (para cualquier usuario) o por su id (solamente para el usuario creador)
 * El parámetro id puede ser el id del archivo (int) o un token (string)
 *
 * nombre: file.download
 */
Route::get('download/{id}', [FileController::class, 'download'])
    ->name('file.download');

/**
 * Ver los datos de facturaion del usuario que los compartio
 *
 * nombre: billing.viewBillingData
 */
Route::get('billing/data/{token}', [ProfileController::class, 'viewBillingData'])
    ->name('billing.viewBillingData');

/**
 * Descarga un conjunto de archivos bien por su token (para cualquier usuario)
 * o por su id (solamente para el usuario creador)
 *
 * El parámetro id puede ser el id del archivo (int) o un token (string)
 *
 * nombre: file.set.download
 */
Route::get('download/set/{id}', [FileController::class, 'downloadSet'])
    ->name('file.set.download');

/**
 * Rutas utilitarias globales
 */

// Ruta hacia el fichero de internacionalización para el Datatables
Route::get(
    'datatables-i18n',
    function () {
        return response()->file(base_path('node_modules/datatables.net-plugins/i18n/es_es.json'));
    }
)->name('datatables-i18n');

/**
 * Listado en formato JSON de los tipos MIME
 *
 * nombre: media-types-list
 */
Route::get('media-types-list', [MediaTypeController::class, 'index'])->name('media-types-list');


/**
 * Ruta intermedia para redirigir a los firmantes a su área de trabajo
 * que acceden desde una url acortada que se ha enviado por sms
 *
 * La función recibe el código del ShortLink, haciendo model bindings
 * por el atributo 'code' de esta clase
 *
 * nombre: redirect.sms.url
 */
Route::get('fs/{short}', function(ShortLink $short) {
    return redirect()->to($short->link);
})->name('redirect.sms.url');
