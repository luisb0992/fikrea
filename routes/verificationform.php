<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VerificationFormController;

/**
 * Rutas para la certificación y verificación de un formulario de datos
 * como proceso independiente y fura del documento
 * Url: dashboard/verificationform
 *
 * @author LuisBarDev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

/**
 * Todas las rutas que requieren idioma llevan el middleware 'language'
 * La subscripción es necesaria
 */
Route::group(['middleware' => ['language', 'subscription']], function () {

    /**
     * Muestra la vista para crear una verificación de datos
     *
     * nombre: dashboard.verificationform.edit
     */
    Route::get('edit/{id?}', [VerificationFormController::class, 'edit'])->name('dashboard.verificationform.edit');

    /**
    * Guarda una verificación de datos
    *
    * nombre: dashboard.verificationform.save
    */
    Route::post('save/{id?}', [VerificationFormController::class, 'save'])->name('dashboard.verificationform.save');

    /**
     * Muestra la vista listado de verificación de datos
     *
     * nombre: dashboard.document.verificationform.list
     */
    Route::get('list', [VerificationFormController::class, 'list'])->name('dashboard.verificationform.list');

    /**
     * Muestra la vista de seleccion de usuarios a asignar a la verificación de datos
     *
     * nombre: dashboard.verificationform.selectSigners
     */
    Route::get('select/signers/{id?}', [VerificationFormController::class, 'selectSigners'])->name('dashboard.verificationform.selectSigners');

    /**
     * Guardar la seleccion de usuarios a participar en la verificación de datos
     *
     * nombre: dashboard.verificationform.saveSigners
     */
    Route::post('save/signers/{id?}', [VerificationFormController::class, 'saveSigners'])->name('dashboard.verificationform.saveSigners');

    /**
     * Ver el estado de la verificación de datos
     *
     * nombre: dashboard.verificationform.status
     */
    Route::get('status/{id}', [VerificationFormController::class, 'verificationStatus'])->name('dashboard.verificationform.status');

    /**
     * Ver la historia de la verificación de datos
     *
     * nombre: dashboard.verificationform.history
     */
    Route::get('history/{id}', [VerificationFormController::class, 'verificationHistory'])->name('dashboard.verificationform.history');

    /**
     * Descargar el certificado para la verificación de datos
     *
     * nombre: dashboard.verificationform.certificate
     */
    Route::get('certificate/{id}', [VerificationFormController::class, 'verificationCertificate'])->name('dashboard.verificationform.certificate');

    /**
     * Enviar la verificación de datos una vez
     *
     * nombre: dashboard.verificationform.send
     */
    Route::post('send/{id}', [VerificationFormController::class, 'verificationSend'])->name('dashboard.verificationform.send');
});