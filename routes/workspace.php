<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WorkSpaceController;
use App\Http\Controllers\Signature\SignerVisitsController;

/**
 * Rutas para el manejo del espacio de trabajo de los documentos
 * para los usuarios firmantes de los mismos
 *
 * /workspace/...
 *
 * Todas las rutas de este grupo lleven un token único que identifica al documento
 * sobre el que se opera y al firmante
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

/**
 * Todas las rutas que requieren idioma llevan el middleware 'language'
 */
Route::group(['middleware' => ['language']], function () {
     /**
      * Muestra la vista principal de la página de "Workspace"
      * que es el espacio de trabajo de los usuarios, donde hacen sus validaciones
      * y firman sus documentos
      *
      * Para acceder a esta vista se necesita el token del firmante
      * Recibe además opcionalmente el id de la compartición o envío de correo, para identificar
      * mediante qué enlace o cuáles enlaces ha accedido a su área de trabajo en fikrea
      * 
      * @param String          token   El token del firmante
      * @param DocumentSharing sharing La compartición
      *
      * nombre: workspace.home
      */
     Route::get('/{token}/{sharing?}', [WorkSpaceController::class, 'show'])
      ->name('workspace.home')
        ->where('sharing', '[0-9]+');

     /**
      * Descarga el documento original por el token de acceso
      *
      * nombre: workspace.document.download
      */
     Route::get('/document/download/{token}', [WorkSpaceController::class, 'download'])
          ->name('workspace.document.download');

     /**
      * Descarga el documento firmado dado por el token de acceso
      *
      * nombre: workspace.document.download.signed
      */
     Route::get('/document/download/signed/{token}', [WorkSpaceController::class, 'downloadSigned'])
          ->name('workspace.document.download.signed');

     /**
      * Descarga los archivos aportados por el firmante dando el token de acceso
      *
      * nombre: workspace.document.download.files
      */
     Route::get('/document/download/files/{token}', [WorkSpaceController::class, 'downloadFiles'])
          ->name('workspace.document.download.files');

     /**
      * Descarga el informe que acredita que el firmante ha participado en un proceso de firma y validación
      *
      * nombre: workspace.signer.report.get
      */
     Route::get('/document/signer/report/{token}', [WorkSpaceController::class, 'signerReport'])
          ->name('workspace.signer.report.get');

     /**
      * Cancela el proceso por expreso deseo del usuario firmante
      *
      * nombre: workspace.signer.cancel
      */
     Route::post('/signer/cancel/{token}', [WorkSpaceController::class, 'cancel'])
          ->name('workspace.signer.cancel');

     /**
      * Muestra la vista que el proceso fue cancelado por el usuario firmante
      *
      * nombre: workspace.signer.canceled
      */
     Route::get('/signer/canceled/{token}', [WorkSpaceController::class, 'canceled'])
          ->name('workspace.signer.canceled');

     /**
      * Muestra la vista para validar (firmar) un documento mediante firma manuscrita digital
      *
      * nombre: workspace.validate.signature
      */
     Route::get('/signature/{token}', [WorkSpaceController::class, 'signature'])->name('workspace.validate.signature');

     /**
      * Muestra la vista para validar (firmar) un documento mediante edición de cajas de textos
      *
      * nombre: workspace.validate.textboxs
      */
     Route::get('/textboxs/{token}', [WorkSpaceController::class, 'textboxs'])
          ->name('workspace.validate.textboxs');

     /**
      * Muestra la vista para firmar una página concreta de un documento identificado por el token del firmante
      *
      * nombre: workspace.validate.signature.page
      */
     Route::get('/signature/{token}/page/{page}', [WorkSpaceController::class, 'page'])
          ->name('workspace.validate.signature.page');

     /**
      * Rechaza los documentos solicitados para firmar
      *
      * nombre: workspace.cancel.signature
      */
     Route::post('/signature/cancel/{token}', [WorkSpaceController::class, 'cancelRequestSignature'])
          ->name('workspace.cancel.signature');


     /**
      * Muestra la vista para responder a una solicitud de envío de documentos
      *
      * nombre:workspace.document.request
      */
     Route::get('/request/{token}', [WorkSpaceController::class, 'request'])
          ->name('workspace.document.request');

     /**
      * Muestra la vista para renovar los documentos que están cerca de expirar
      *
      * @param string $token El token de la solicitud de documentos
      *
      * nombre: workspace.document.request.renew
      */
     Route::get('/request/renew/{token}', [WorkSpaceController::class, 'requestRenewDocs'])
          ->name('workspace.document.request.renew');

     /**
      * Rechaza la solicitud del usuario firmante
      *
      * nombre: workspace.cancel.request
      */
     Route::post('/{token}', [WorkSpaceController::class, 'cancelRequest'])
          ->name('workspace.cancel.request');

     /**
      * Guarda una solicitud de envío de documentos
      *
      * nombre: workspace.document.request.save
      */
     Route::post('/request/{token}', [WorkSpaceController::class, 'saveRequest'])
          ->name('workspace.document.request.save');

     /**
      * Guarda las firmas del documento
      *
      * nombre: workspace.document.save
      */
     Route::post('document/save/{token}', [WorkSpaceController::class, 'saveSignedDocument'])
          ->name('workspace.document.save');

     /**
      * Guarda las cajas de texto de un documento
      *
      * nombre: workspace.textboxs.save
      */
     Route::post('textboxs/save/{token}', [WorkSpaceController::class, 'saveTextboxsDocument'])
          ->name('workspace.textboxs.save');

     /**
      * Muestra la vista para validar un documento mediante una grabación de audio
      *
      * nombre: workspace.validate.audio
      */
     Route::get('/audio/{token}', [WorkSpaceController::class, 'audio'])->name('workspace.validate.audio');

     /**
      * Guarda las grabaciones de audio del documento
      *
      * nombre: workspace.save.audio
      */
     Route::post('/audio/{token}', [WorkSpaceController::class, 'saveAudio'])->name('workspace.save.audio');

     /**
      * rechaza las grabaciones de audio del documento solicitado
      *
      * nombre: workspace.cancel.audio
      */
     Route::post('/audio_cancel/{token}', [
          WorkSpaceController::class,
          'cancelRequestAudio'
     ])->name('workspace.cancel.audio');

     /**
      * Muestra la vista para validar un documento mediante uan grabación de video
      *
      * nombre: workspace.validate.video
      */
     Route::get('/video/{token}', [WorkSpaceController::class, 'video'])
          ->name('workspace.validate.video');

     /**
      * Guarda las grabaciones de video del documento
      *
      * nombre: workspace.video.save
      */
     Route::post('/video/save/{token}', [WorkSpaceController::class, 'saveVideo'])
          ->name('workspace.video.save');

     /**
      * rechaza las grabaciones de video del documento solicitado
      *
      * nombre: workspace.cancel.video
      */
     Route::post('/video/cancel/{token}', [
          WorkSpaceController::class,
          'cancelRequestVideo'
     ])->name('workspace.cancel.video');

     /**
      * Muestra la vista para validar un documento mediante un documento identificativo
      * como el carné o cédula de identidad, pasaporte, carné de conducir
      *
      * name: workspace.validate.passport
      */
     Route::get('/passport/{token}', [WorkSpaceController::class, 'passport'])->name('workspace.validate.passport');

     /**
      * Guarda el o los documentos de acreditación de identidad como el Documento Nacional de Identidad
      * o el pasaporte
      *
      * name: workspace.save.passport
      */
     Route::post('/passport/{token}', [WorkSpaceController::class, 'savePassport'])->name('workspace.save.passport');

     /**
      * rechaza los pasaportes solicitado
      *
      * nombre: workspace.cancel.passport
      */
     Route::post('/passport/cancel/{token}', [
          WorkSpaceController::class,
          'cancelRequestPassport'
     ])->name('workspace.cancel.passport');

     /**
      * Muestra la vista donde se verifican y validan los datos solicitados por el usuario de fikrea
      * Contiene el formulario de datos con sus campos y respectivas valdiaciones
      *
      *name workspace.validate.formdata
      */
     Route::get('/formdata/{token}', [WorkSpaceController::class, 'formdata'])->name('workspace.validate.formdata');

     /**
      * Almacenar la valdiacion del formulario de datos
      *
      * name: workspace.document.formdata.save
      */
     Route::post('/formdata/save/{token}', [
          WorkSpaceController::class,
          'saveFormdata'
     ])->name('workspace.document.formdata.save');

     /**
      * Rechazar el formulario de datos solicitado
      *
      * name: workspace.cancel.fomrdata
      */
     Route::post('/formdata/cancel/{token}', [
          WorkSpaceController::class,
          'cancelRequestFormdata'
     ])->name('workspace.cancel.formdata');

     /**
      * Crea el comentario de la solicitud del documento
      *
      * name: workspace.store.comment
      */
     Route::post('/comment/{token}', [WorkSpaceController::class, 'createComment'])->name('workspace.store.comment');

     /**
      * Muestra la vista para realizar la verificación de datos como proceso independiente del documento
      *
      * name: 'workspace.verificationform.form
      */
     Route::get('verificationform/{token}', [WorkSpaceController::class, 'verificationForm'])->name('workspace.verificationform.form');

     /**
      * Cancelar el proceso de verificación de datos
      *
      * nombre: workspace.cancel.verificationform
      */
     Route::post('verificationform/cancel/{token}', [WorkSpaceController::class, 'cancelVerificationForm'])->name('workspace.cancel.verificationform');

     /**
      * Guardar la verificación de datos
      *
      * nombre: workspace.verificationform.save
      */
     Route::post('verificationform/save/{token}', [WorkSpaceController::class, 'verificationSave'])->name('workspace.verificationform.save');

     /**
      * Descargar un pdf con la informacion verificada o aportada
      *
      *nombre: workspace.verificationform.certificate
      */
     Route::get('verificationform/certificate/{token}', [WorkSpaceController::class, 'verificationCertificate'])->name('workspace.verificationform.certificate');

     /**
      * Validar si la verificación de datos se hizo o no
      *
      *nombre: workspace.verificationform.isdone
      */
     Route::get('verificationform/isdone/{token}', [WorkSpaceController::class, 'verificationIsDone'])->name('workspace.verificationform.isdone');

     /**
      * Mostrar la vista para visualizar y/o enviar algun comentario al solicitante
      *
      *nombre: workspace.comment.list
      */
     Route::get('comments/{token}', [WorkSpaceController::class, 'commentList'])->name('workspace.comment.list');

     /**
      * Muestra la vista que informa ha salido del espacio de trabajo
      *
      *nombre: workspace.exit
      */
     Route::get('exitworkspace/{token}', [WorkSpaceController::class, 'exitWorkspace'])->name('workspace.exit');

     /**
      * Guardar un comentario para cada proceso
      *
      * nombre: workspace.comment.save
      */
     Route::post('validation/save/comment', [WorkSpaceController::class, 'saveComment'])->name('workspace.comment.save');

    /**
     * Comparte un conjunto de archivos con cualquier usuario
     *
     * Proporciona un enlace a un archivo compartido. Estos enlaces son del tipo:
     *
     * https://www.fikrea.com/share/set/hWQRRa5kNn29ldOe6Wxw8lqQy1LqzxyrrjWZyIrQbZG1oU2gXjp8idUv2zEHsj5j
     *
     * donde aparece un token que identifica de forma unívoca al conjunto de archivos
     *
     * nombre: file.set.share
     */
    Route::get('share/set/{token}', [WorkSpaceController::class, 'fileSetShare'])
        ->name('workspace.set.share');

    // Registrar acceso al compartido
    Route::post('share/set/{token}/log', [WorkSpaceController::class, 'fileSetShareLog'])
        ->name('workspace.set.share.log');

    /**
      * Comparte un conjunto de documentos con cualquier usuario
      *
      * Proporciona un enlace a un documentos compartido. Estos enlaces son del tipo:
      *
      * https://www.fikrea.com/share/set/hWQRRa5kNn29ldOe6Wxw8lqQy1LqzxyrrjWZyIrQbZG1oU2gXjp8idUv2zEHsj5j
      *
      * donde aparece un token que identifica de forma unívoca al conjunto de documentos
      *
      * nombre: workspace.document.share
      */
     Route::get('document/share/{token}', [WorkSpaceController::class, 'documentShare'])->name('workspace.document.share');

     /**
      * Descargar un documento que se ha compartido mediante la comparticion hacia destinatarios
      *
      * nombre: workspace.document.download
      */
     Route::get('document/share/download/{token}', [WorkSpaceController::class, 'downloadDocumentShare'])->name('workspace.document.shared.download');

     /**
      * Descargar los datos de la facturacion de un usuario que ha compartido con un externo
      *
      * nombre: workspace.billing.download
      */
     Route::get('get/billing/{token}', [WorkSpaceController::class, 'downloadBillingData'])->name('workspace.billing.download');
});
