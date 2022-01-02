<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Signature\DocumentController;

/**
 * Rutas para el manejo de los documentos
 *
 * /documents/...
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

/**
 * Todas las rutas que requieren idioma llevan el middleware 'language'
 * Para la manipulación de archivos se necesita subscripción
 */
Route::group(['middleware' => ['language', 'subscription']], function () {

      /**
       * Crear un nuevo documento de texto simple
       *
       * nombre: dashboard.document.edit
       */
      Route::get('edit/{id?}', [DocumentController::class, 'edit'])->name('dashboard.document.edit');

      /**
       * Extrae el texto de un archivo subido utilizando OCR (Reconocimiento óptico de carácteres)
       *
       * nombre: dashboard.document.ocr
       */
      Route::post('ocr', [DocumentController::class, 'ocr'])->name('dashboard.document.ocr');

      /**
       * Guarda un documento editado
       *
       * Se trata de un documento simple de texto que ha sido creado manualmente
       *
       * nombre: dashboard.document.save
       */
      Route::post('save', [DocumentController::class, 'save'])->name('dashboard.document.save');

      /**
       * Lista los documentos
       *
       * nombre: dashboard.document.list
       */
      Route::get('list', [DocumentController::class, 'list'])->name('dashboard.document.list');

      /**
       * Lista los documentos enviados
       *
       * nombre: dashboard.document.sent
       */
      Route::get('sent', [DocumentController::class, 'sent'])->name('dashboard.document.sent');

      /**
       * Lista los documentos eliminados
       *
       * nombre: dashboard.document.removed
       */
      Route::get('trash', [DocumentController::class, 'removed'])->name('dashboard.document.removed');

      /**
       * Descarga un documento
       *
       * nombre: dashboard.document.download
       */
      Route::get('download/{id}', [DocumentController::class, 'download'])->name('dashboard.document.download');

      /**
       * Obtiene el documento convertido en formato PDF
       * El argumento es el id del documento o el token de acceso del usuario firmante
       *
       * nombre: dashboard.document.pdf
       */
      Route::get('pdf/{token}', [DocumentController::class, 'pdf'])->name('dashboard.document.pdf');

      /**
       * Elimina uno o más documentos, enviándolos a la papelera
       *
       * nombre: dashboard.document.delete
       */
      Route::post('delete', [DocumentController::class, 'delete'])->name('dashboard.document.delete');

      /**
       * Elimina un documento de forma definiva
       *
       * nombre: dashboard.document.destroy
       */
      Route::post('destroy', [DocumentController::class, 'destroy'])->name('dashboard.document.destroy');

      /**
       * Recupera un documento eliminado
       *
       * nombre: dashboard.document.recover
       */
      Route::get('recover/{id}', [DocumentController::class, 'recover'])->name('dashboard.document.recover');

      /**
       * Selecciona las personas firmantes del documento
       * Se trata de una lista de personas, que pueden ser elegidas entre los contactos guardados o no,
       * y que deben firmar el documento
       *
       * nombre: dashboard.document.signers
       */
      Route::get('signers/{id}', [DocumentController::class, 'signers'])->name('dashboard.document.signers');

      /**
       * Obtiene los firmantes del documento en JSON
       *
       * nombre: dashboard.document.get.signers
       */
      Route::post('get-signers/{id}', [DocumentController::class, 'getSigners'])
            ->name('dashboard.document.get.signers');

      /**
       * Guarda los firmantes del documento
       *
       * nombre: dashboard.document.save.signers
       */
      Route::post('signers/{id}', [DocumentController::class, 'saveSigners'])->name('dashboard.document.save.signers');

      /**
       * Muestra la vista de validaciones requeridas para el documento
       *
       * Una vez se han definido los firmantes del documento, pasamos a definir que acciones de validación
       * y firma debe realizar cada uno de ellos. Por ejemplo para un firmante se le puede pedir:
       *
       * 1. Una firma digital manuscrita en el documento.
       * 2. Que adjunte un audio aprobando el documento.
       * 3. Que adjunte un video aprobando el documento.
       *
       * nombre: dashboard.document.validations
       */
      Route::get('validations/{id}', [DocumentController::class, 'validations'])
            ->name('dashboard.document.validations');

      /**
       * Guarda las validaciones del documento
       *
       * nombre: dashboard.document.save.validations
       */
      Route::post('validations/{id}', [DocumentController::class, 'saveValidations'])
            ->name('dashboard.document.save.validations');

      /**
       * Prepara un documento para su firma
       * El documento es procesado, generándose imágenes individuales de cada página que pueden ser firmadas
       *
       * nombre: dashboard.document.prepare
       */
      Route::get('prepare/{id}', [DocumentController::class, 'prepare'])
            ->name('dashboard.document.prepare');

      /**
       * Prepara un documento con las cajas de textos
       * Devuelve la vista para configurar las cajas de textos que deben cumplimentar los firmantes
       * sobre el documento
       *
       * nombre: dashboard.document.textboxs
       */
      Route::get('textboxs/{id}', [DocumentController::class, 'textBoxs'])
            ->name('dashboard.document.textboxs');

      /**
       * Guarda la configuración de la firma de un documento
       *
       * nombre: dashboard.document.config.save
       */
      Route::post('config/save/{id}', [DocumentController::class, 'saveConfigSignDocument'])
            ->name('dashboard.document.config.save');

      /**
       * Guarda la configuración de las cajas de textos de un documento
       *
       * nombre: dashboard.document.textboxs.save
       */
      Route::post('config/textboxs/{id}', [DocumentController::class, 'saveConfigBoxsDocument'])
            ->name('dashboard.document.textboxs.save');

      /**
       * Guarda un sello para estampar sobre un documento
       *
       * nombre: dashboard.document.config.stamp.save
       */
      Route::post('config/stamp/save', [DocumentController::class, 'saveStamp'])
            ->name('dashboard.document.config.stamp.save');

      /**
       * Elimina un sello para estampar en un documento
       *
       * nombre: dashboard.document.config.stamp.delete
       */
      Route::post('config/stamp/delete/{id}', [DocumentController::class, 'deleteStamp'])
            ->name('dashboard.document.config.stamp.delete');

      /**
       * Obtiene la configuración de la firma de un documento
       * bien proporcionando el id del documento o bien el token del usuario firmante para la firma
       * de ese documento
       *
       * nombre: dashboard.document.config.save
       */
      Route::post('config/get/{token}', [DocumentController::class, 'getConfigSignDocument'])
            ->name('dashboard.document.get.signs');

      /**
       * Obtiene la configuración de las cajas de texto de un documento
       * bien proporcionando el id del documento o bien el token del usuario firmante para la firma
       * de ese documento
       *
       * nombre: dashboard.document.get.boxs
       */
      Route::post('config/boxs/get/{token}', [DocumentController::class, 'getConfigBoxsDocument'])
            ->name('dashboard.document.get.boxs');

      /**
       * Obtiene el documento firmado
       *
       * nombre: dashboard.document.signed.get
       */
      Route::get('signed/{id}', [DocumentController::class, 'getSignedDocument'])
            ->name('dashboard.document.signed.get');

      /**
       * Obtiene una grabación de audio que valida un documento
       *
       * nombre: dashboard.audio.get
       */
      Route::get('audio/get/{id}', [DocumentController::class, 'getAudio'])
            ->name('dashboard.audio.get');

      /**
       * Obtiene una grabación de video que valida un documento
       *
       * nombre: dashboard.video.get
       */
      Route::get('video/get/{id}', [DocumentController::class, 'getVideo'])
            ->name('dashboard.video.get');

      /**
       * Obtiene una captura de pantalla que valida un documento
       *
       * nombre: dashboard.screen.get
       */
      Route::get('screen/get/{id}', [DocumentController::class, 'getScreen'])
            ->name('dashboard.screen.get');

      /**
       * Obtiene un documento identificativo que valida un documento
       *
       * nombre: dashboard.passport.get
       */
      Route::get('passport/get/{id}', [DocumentController::class, 'getPassport'])
            ->name('dashboard.passport.get');

      /**
       * Obtiene el estado de validación del documento
       *
       * nombre: dashboard.document.status
       */
      Route::get('status/{id}', [DocumentController::class, 'getValidationStatus'])
            ->name('dashboard.document.status');


      /**
       * Obtiene el certificado de validación del documento
       *
       * nombre: dashboard.document.certificate
       */
      Route::get('certificate/{id}', [DocumentController::class, 'certificate'])
            ->name('dashboard.document.certificate');

      /**
       * Reenvía una solicitud de firma de documento a los firmantes que aún no ha atendido
       * a la petición
       *
       * nombre: dashboard.document.send.request
       */
      Route::post('send/request/{id}', [DocumentController::class, 'sendDocumentRequest'])
            ->name('dashboard.document.send.request');

      /**
       * Muestra listado de visitas de los firmantes sobre el documento
       *
       * nombre: dashboard.document.history
       */
      Route::get('history/{document}', [DocumentController::class, 'getDocumentHistory'])
            ->name('dashboard.document.history');

      /**
       * Página para que el creador/autor de un documento realice una validación de audio
       *
       * nombre: dashboard.audio
       */
      Route::get('audio/{id}', [DocumentController::class, 'audio'])
            ->name('dashboard.audio');

      /**
       * Página para que el creador/autor de un documento guarde una validación de audio
       *
       * nombre: dashboard.audio.save
       */
      Route::post('audio/save/{id}', [DocumentController::class, 'saveAudio'])
            ->name('dashboard.audio.save');

      /**
       * Página para que el creador/autor de un documento realice una validación de video
       *
       * nombre: dashboard.video
       */
      Route::get('video/{id}', [DocumentController::class, 'video'])
            ->name('dashboard.video');

      /**
       * Página para que el creador/autor de un documento guarde una validación de video
       *
       * nombre: dashboard.video.save
       */
      Route::post('video/save/{id}', [DocumentController::class, 'saveVideo'])
            ->name('dashboard.video.save');

      /**
       * Página para que el creador/autor de un documento realice una validación
       * mediante documento identificativo
       *
       * nombre: dashboard.passport
       */
      Route::get('passport/{id}', [DocumentController::class, 'passport'])
            ->name('dashboard.passport');

      /**
       * Página para que el creador/autor de un documento guarde una validación de video
       * mediante documento identificativo
       *
       * nombre: dashboard.video.save
       */
      Route::post('passport/save/{id}', [DocumentController::class, 'savePassport'])
            ->name('dashboard.passport.save');

      /**
       * Página para que el creador/autor de un documento pueda crear/seleccionar un formulario
       * con datos especificos para ser validados por el receptor
       *
       * nombre: dashboard.document.formdata
       */
      Route::get('formdata/{id}', [DocumentController::class, 'formData'])
            ->name('dashboard.document.formdata');

      /**
       * Guardar los datos relacionados con el formulario de datos y los firmantes asignados
       * a la validacion
       *
       * nombre: dashboard.document.saveFormDataValidation
       */
      Route::post('formdata/save/{id}', [DocumentController::class, 'saveFormDataValidation'])
            ->name('dashboard.document.saveFormDataValidation');

      /**
       * Página para que el creador/autor de un documento realice una validación
       * mediante solicitud de documento
       *
       * @param id Debe ser un valor numérico por lo que debe especificarse para que
       *           no entre en conflictos con otras rutas como
       *           dashboard/document/request/{id} - Ruta actual
       *           dashboard/document/request/edit
       *           dashboard/document/request/list
       *           dashboard/document/request/save
       * nombre: dashboard.request
       */
      Route::get('request/{id}', [DocumentController::class, 'request'])
            ->name('dashboard.request')
            ->where('id', '[0-9]+');

      /**
       * Compartir un docmento seleccioando
       *
       * nombre: dashboard.document.share
       */
      Route::post('share', [DocumentController::class, 'shareDocument'])->name('dashboard.document.share');

      /**
       * Guardar una comparticion de documentos
       *
       * nombre: dashboard.save.document.sharing
       */
      Route::post('save/share', [DocumentController::class, 'saveShareDocument'])->name('dashboard.save.document.sharing');

      /**
       * Muestra una vista donde se puede visualizar una lista con las comparticiones de un conjunto de documentos
       *
       * nombre: dashboard.list.document.sharing
       */
      Route::get('list/sharing', [DocumentController::class, 'listShareDocument'])->name('dashboard.list.document.sharing');

      /**
       * Ver el historico de comparticiones y descargas del conjunto de documentos
       *
       * nombre: dashboard.history.document.sharings
       */
      Route::get('history/sharing/{id}', [DocumentController::class, 'historyShareDocument'])->name('dashboard.history.document.sharings');

      /**
       * Guardar un documento en la lista de archivos del usaurio
       *
       * nombre: dashboard.document.movetofiles
       */
      Route::post('copytofiles', [DocumentController::class, 'copyToFiles'])->name('dashboard.document.copytofiles');
});
