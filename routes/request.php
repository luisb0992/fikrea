<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Request\DocumentRequestController;
use App\Http\Controllers\WorkSpaceController;

/**
 * Rutas para las solicitudes de documentos a los usuarios
 *
 * /document/request/...
 *
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

/**
 * Todas las rutas que requieren idioma llevan el middleware 'language'
 * La subscripción es necesaria
 */
Route::group(['middleware' => ['language', 'subscription']], function () {
    /**
     * Muestra la vista para crear una solicitud de documentos
     *
     * nombre: dashboard.document.request.edit
     */
     Route::get('edit/{id?}', [DocumentRequestController::class, 'edit'])
          ->name('dashboard.document.request.edit');

    /**
     * Muestra la vista con la vista de solicitudes de documentos enviadas
     *
     * nombre: dashboard.document.request.list
     */
     Route::get('list', [DocumentRequestController::class, 'list'])
          ->name('dashboard.document.request.list');

    /**
     * Guarda una solicitud de documentos
     *
     * nombre: dashboard.document.request.save
     */
     Route::post('save', [DocumentRequestController::class, 'save'])
         ->name('dashboard.document.request.save');

    /**
     * Selecciona la lista de firmantes o lista de usuarios a los que se les solicita la documentación
     *
     * nombre: dashboard.document.request.signers
     */
     Route::get('signers/{id?}', [DocumentRequestController::class, 'signers'])
         ->name('dashboard.document.request.signers');

    /**
     * Obtiene la lista de firmamntes o lista de usuarios a los que se les solicita la documentación
     *
     * nombre: dashboard.document.request.get.signers
     */
     Route::post('get/signers/{id}', [DocumentRequestController::class, 'getSigners'])
         ->name('dashboard.document.request.get.signers');

    /**
     * Guarda la lista de firmantes o lista de usuarios a los que se les solicita la documentación
     *
     * nombre: dashboard.document.request.save.signers
     */
     Route::post('save/signers/{id}', [DocumentRequestController::class, 'saveSigners'])
         ->name('dashboard.document.request.save.signers');

    /**
     * Genera una URL mediante la cual se puede aportar la documetación requerida
     *
     * nombre: dashboard.document.request.generate.url
     */
     Route::post('generate/url/{id}', [DocumentRequestController::class, 'generateUrl'])
         ->name('dashboard.document.request.generate.url');

    /**
     * Finaliza el proceso de solicitud de documento mediante generacion de URL
     *
     * nombre: dashboard.document.request.generate.url.save
     */
     Route::post('generate/url/save/{id}', [DocumentRequestController::class, 'saveDocumentRequestByUrl'])
         ->name('dashboard.document.request.generate.url.save');

    /**
     * Muestra la vista para conocer el estado actual de una solicitud de documentos
     *
     * nombre: dashboard.document.request.status
     */
     Route::get('/request/status/{id}', [DocumentRequestController::class, 'requestStatus'])
           ->name('dashboard.document.request.status');

    /**
     * Descarga un archivo que forma parte de una solicitud de documentos
     *
     * nombre: dashboard.document.request.download.file
     */
     Route::get('/request/download/fle/{id}', [DocumentRequestController::class, 'downloadFile'])
           ->name('dashboard.document.request.download.file');

    /**
     * Descarga todos los archivos que forman parte de una solicitud de documentos
     *
     * nombre: dashboard.document.request.download.files
     */
     Route::get('/request/download/files/{id}', [DocumentRequestController::class, 'downloadFiles'])
          ->name('dashboard.document.request.download.files');

    /**
     * Muestra la vista con el historico de visitas sobre una solicitud de documentos
     *
     * nombre: dashboard.document.request.history
     */
     Route::get('/request/history/{id}', [DocumentRequestController::class, 'historyDocumentRequest'])
           ->name('dashboard.document.request.history');

     /**
       * Reenvía una solicitud de firma de documento a los firmantes que aún no ha atendido
       * a la petición
       *
       * nombre: dashboard.document.request.send.request
       */
      Route::post('send/document/request/{id}', [DocumentRequestController::class, 'sendDocumentRequest'])
            ->name('dashboard.document.request.send.request');
      
      /**
       * Muestra la vista donde se deben seleccionar los documentos que deben
       * aportar el o los firmantes  que deban validarse mediante
       * "Solicitud de Documentos"
       *
       * @param id El documento
       *
       * nombre: dashboard.document.request.validations
       */
      Route::get('validations/{id?}', [DocumentRequestController::class, 'validationsDocumentRequest'])
           ->name('dashboard.document.request.validations');

      /**
       * Guarda las solicitudes creadas para cada firmante como
       * nueva forma de validación
       *
       * @param id El documento
       *
       * nombre: dashboard.document.request.validations
       */
      Route::post('validations/{id?}', [DocumentRequestController::class, 'saveValidationsDocumentRequest']);

    /**
     * Devuelve el pdf generado con el certificado en el proceso de solicitud de documentos
     *
     * @param id La solicitud de documentos
     *
     * nombre: dashboard.document.request.certificate
     */
     Route::get('certificate/{id}', [DocumentRequestController::class, 'certificate'])
          ->name('dashboard.document.request.certificate');
});
