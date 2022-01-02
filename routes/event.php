<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

/**
 * Rutas para Gestionar un evento de la aplicacion como encuestas, votacion, firmas
 *
 * Url: dashboard/event
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
     * Muestra la vista para crear o editar un evento
     *
     * nombre: dashboard.event.edit
     */
    Route::get('edit/{id?}', [EventController::class, 'edit'])->name('dashboard.event.edit');

    /**
     * Muestra la vista listado de eventos
     *
     * nombre: dashboard.document.event.list
     */
    Route::get('list', [EventController::class, 'list'])->name('dashboard.event.list');

    /**
     * Muestra el listado de eventos como borradores y las plantillas
     *
     * nombre: dashboard.event.list.templatesAndDraft
     */
    Route::get('list/templatesdraft', [EventController::class, 'listTemplatesAndDraft'])->name('dashboard.event.list.templatesAndDraft');

    /**
     * Guarda los datod de un evento
     *
     * nombre: dashboard.event.save
     */
    Route::post('save/{id?}', [EventController::class, 'save'])->name('dashboard.event.save');

    /**
     * Muestra la lista de usuarios para llenar el censo del evento
     *
     * nombre: dashboard.event.census
     */
    Route::get('census/{id}', [EventController::class, 'census'])->name('dashboard.event.census');

    /**
     * Guardar el censo de participantes de un evento
     *
     * nombre: dashboard.event.save.census
     */
    Route::post('save/census/{id}', [EventController::class, 'saveCensus'])->name('dashboard.event.save.census');

    /**
     * Muestra una vista donde se encuentran las plantilals del usuario, asi como un formulario para construir
     * preguntas y respuestas
     *
     * nombre: dashboard.event.builder.questionsanswers
     */
    Route::get('builder/questionsanswers/{id}', [EventController::class, 'builderQuestionAnswers'])->name('dashboard.event.builder.questionsanswers');
});