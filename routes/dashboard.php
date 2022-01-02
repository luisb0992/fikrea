<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashBoardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ScreenController;

/**
 * Rutas de la dashboard page, accesibles a través de la url:
 *
 * /dashboard/...
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

/**
 * Todas las rutas que requieren idioma llevan el middleware 'language'
 * Todas las rutas requieren que la subscripción del usuario se encuentre en vigor
 */
Route::group(['middleware' => ['language', 'subscription']], function () {

    /***********************************************************************************
     *  Página principal del Dashboard
    /***********************************************************************************

    /**
     * Página principal del dashboard de la aplicación
     * nombre: dashboard.home
     */
    Route::get('/', [DashBoardController::class, 'show'])->name('dashboard.home');

    /**
     * Marca la notificación como leída
     * nombre: dashboard.notification.read
     */
    Route::post('/notification/read', [DashBoardController::class, 'notificationRead'])
         ->name('dashboard.notification.read');

    /***********************************************************************************
     *  Configuración
    /***********************************************************************************

    /**
     * Página para cambiar la configuración del usuario
     * nombre: dashboard.config
     */
    Route::get('/config', [DashBoardController::class, 'config'])
         ->name('dashboard.config')
         ->middleware('auth');

    /**
     * Guarda la configuración del usuario
     * nombre: dashboard.config.save
     */
    Route::post('/config', [DashBoardController::class, 'saveConfig'])
         ->name('dashboard.config.save')
         ->middleware('auth');

    /***********************************************************************************
     *  Mi Perfil
    /***********************************************************************************

    /**
     * Muestra la página para modificar los datos del perfil de usuario de la aplicación
     * nombre: dashboard.profile
     */
    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('dashboard.profile');

    /**
     * Muestra la pantalla para recuperar una sesión (de un usuario invitado) anterior
     * Para ello se requerirá la dirección de correo que fue utilizada por el usuario invitado
     * en aquella sesión
     * nombre: dashboard.profile.session
     */
    Route::get('/profile/session', [ProfileController::class, 'session'])
        ->name('dashboard.profile.session');

    /**
     * Guarda el perfil del usuario
     * nombre: dashboard.profile.save
     */
    Route::post('/profile', [ProfileController::class, 'save'])
        ->name('dashboard.profile.save');

    /**
     * Envia un correo donde se comparte los datos de facturacion
     * nombre: dashboard.profile.shareBilling
     */
    Route::post('/profile/share/billing', [ProfileController::class, 'shareBilling'])
        ->name('dashboard.profile.shareBilling');

    /**
     * Gestiona los datos de facturacion para ser compartidos por url
     * nombre: dashboard.profile.shareBillingForLink
     */
    Route::post('/profile/share/billing/link', [ProfileController::class, 'shareBillingForLink'])
        ->name('dashboard.profile.shareBillingForLink');

    /**
     * Guarda la compañía para los datos de facturación del usuario
     * nombre: dashboard.profile.company.save
     */
    Route::post('/profile/company', [ProfileController::class, 'saveCompany'])
        ->name('dashboard.profile.company.save')
        ->middleware('auth');

    /**
     * Cambia la contraseña de acceso del usuario
     * nombre: dashboard.profile.password
     */
    Route::post('/profile/password', [ProfileController::class, 'changePassword'])
        ->name('dashboard.profile.password')
        ->middleware('auth');

    /**
     * Cambia la imagen  del perfil del usuario
     * nombre: dashboard.profile.image
     */
    Route::post('/profile/image', [ProfileController::class, 'uploadProfileImage'])
        ->name('dashboard.profile.image');

    /***********************************************************************************
     *  Mi Subscripción
    /***********************************************************************************

    /**
     * Muestra la vista que muestra la subscripción actual, su estado y los pedidos de renovación
     *
     * nombre: dashboard.subscription
     */
    Route::get('/subscription', [SubscriptionController::class, 'show'])
         ->name('dashboard.subscription');

    /***********************************************************************************
     *  Búsqueda de archivos y documentos
    /***********************************************************************************/

    /**
     * Busca los documentos y archivos con referencia al texto de consulta suministrado
     * nombre: dashboard.search.find.document
     */
    Route::get('/search/document/{query}', [SearchController::class, 'findDocument'])
         ->name('dashboard.search.find.document');

    /***********************************************************************************
     *  Contactos
    /***********************************************************************************/

    /**
     * Muestra la página para crear o editar un contacto
     * nombre: dashboard.contact
     */
    Route::get('/contact/edit/{id?}', [ContactController::class, 'edit'])
        ->name('dashboard.contact.edit');

    /**
     * Muestra la página para guardar un contacto
     * nombre: dashboard.contact.save
     */
    Route::post('/contact/save', [ContactController::class, 'save'])
        ->name('dashboard.contact.save');

    /**
     * Muestra la página para eliminar un contacto
     * nombre: dashboard.contact.delete
     */
    Route::get('/contact/delete/{id}', [ContactController::class, 'delete'])
        ->name('dashboard.contact.delete');

    /**
     * Muestra la página con todos los contactos
     * nombre: dashboard.contact.list
     */
    Route::get('/contact/list', [ContactController::class, 'list'])
        ->name('dashboard.contact.list');

    /**
     * Busca un contacto por su dirección de correo
    *
    * nombre: dashboard.contact.find.email
    */
    Route::post('contact/find/email', [ContactController::class, 'findByEmail'])
        ->name('dashboard.contact.find.email');

    /**
     * Busca un contacto por su dirección de correo y si no lo encuentra devuelve un aviso
    *
    * nombre: dashboard.contact.find.email.withouterror
    */
    Route::post('contact/find/email/info', [ContactController::class, 'findByEmailWithoutError'])
        ->name('dashboard.contact.find.email.withouterror');

    /**
     * Busca un contacto por su número de teléfono
    *
    * nombre: dashboard.contact.find.phone
    */
    Route::post('contact/find/phone', [ContactController::class, 'findByPhone'])
        ->name('dashboard.contact.find.phone');

    /**
     * Busca un contacto por su número de teléfono y sino lo encuentra devuelve un aviso
    *
    * nombre: dashboard.contact.find.phone.withouterror
    */
    Route::post('contact/find/phone/info', [ContactController::class, 'findByPhoneWithoutError'])
        ->name('dashboard.contact.find.phone.withouterror');

    /**
     * Página para grabar un audio como muestra para las validaciones de audio
     * de los firmantes
     *
     * nombre: dashboard.config.audio
     */
    Route::get('/config/audio', [DashBoardController::class, 'configAudio'])
        ->name('dashboard.config.audio')
        ->middleware('auth');

    /**
     * Ruta para guardar el audio que se ha grabado desde configuracion del usuario
     *
     * nombre: dashboard.config.audio
     */
    Route::post('/config/audio', [DashBoardController::class, 'saveExampleAudio'])
        ->name('dashboard.config.audio')
        ->middleware('auth');

    /**
     * Ruta para obtener un archivo audio y reproducirlo desde la config del usuario
     *
     * nombre: dashboard.config.get.audio
     */
    Route::get('/config/get/audio/{name}', [DashBoardController::class, 'getAudioFile'])
        ->name('dashboard.config.get.audio')
        ->middleware('auth');

    /**
     * Ruta para eliminar de la config del usuario la config del archivo de audio grabado
     *
     * nombre: dashboard.config.remove.audio
     */
    Route::post('/config/remove/audio/{name}', [DashBoardController::class, 'removeAudioFile'])
        ->name('dashboard.config.remove.audio')
        ->middleware('auth');

    /**
     * Página para grabar un video como muestra para las validaciones de video
     * de los firmantes
     *
     * nombre: dashboard.config.video
     */
    Route::get('/config/video', [DashBoardController::class, 'configVideo'])
        ->name('dashboard.config.video')
        ->middleware('auth');

    /**
     * Ruta para guardar el video que se ha grabado desde configuración del usuario
     *
     * nombre: dashboard.config.video
     */
    Route::post('/config/video', [DashBoardController::class, 'saveExampleVideo'])
        ->name('dashboard.config.video')
        ->middleware('auth');

    /**
     * Ruta para obtener un archivo video y reproducirlo desde la config del usuario
     *
     * nombre: dashboard.config.get.video
     */
    Route::get('/config/get/video/{name}', [DashBoardController::class, 'getVideoFile'])
        ->name('dashboard.config.get.video')
        ->middleware('auth');

    /**
     * Ruta para eliminar de la config del usuario la config del archivo de video grabado
     *
     * nombre: dashboard.config.remove.video
     */
    Route::post('/config/remove/video/{name}', [DashBoardController::class, 'removeVideoFile'])
        ->name('dashboard.config.remove.video')
        ->middleware('auth');

    /***********************************************************************************
     *  Comentarios en un proceso de validación
    /***********************************************************************************/

    /**
     * Guardar un comentario para cada proceso
     *
     * nombre: dashboard.comment.save
     */
    Route::post('validation/save/comment', [DashBoardController::class, 'saveComment'])
        ->name('dashboard.comment.save')
        ->middleware('auth');

    /***********************************************************************************
     *  Compartir archivos o documentos por redes sociales
    /***********************************************************************************/

    /**
     * Guardar una compartición de archivos por redes sociales
     *
     * nombre: dashboard.share.socialnetwork.save
     */
    Route::post('share/save/socialnetwork', [DashBoardController::class, 'saveShareSocialNetwork'])
        ->name('dashboard.share.socialnetwork.save')
        ->middleware('auth');

    /*--------------------------------------------------
     * GRABACIONES DE PANTALLA DEL USUARIO DE FIKREA
     ---------------------------------------------------*/

    /**
     * Vista para crear una grabación de pantalla
     *
     * nombre: dashboard.screen.edit
     */
    Route::get('screen/edit', [ScreenController::class, 'editScreen'])
        ->name('dashboard.screen.edit')
        ->middleware('auth');

    /**
     * Guarda una grabación de pantalla
     *
     * nombre: dashboard.screen.save
     */
    Route::post('screen/save', [ScreenController::class, 'saveScreenRecord'])
        ->name('dashboard.screen.save')
        ->middleware('auth');

    /**
     * Guarda un array de grabaciones de pantalla
     *
     * nombre: dashboard.screen.saveall
     */
    Route::post('screen/saveall', [ScreenController::class, 'saveScreenAllRecords'])
        ->name('dashboard.screen.saveall')
        ->middleware('auth');

    /**
     * Muestra el listado de las grabaciones de pantalla del usuario de fikrea
     *
     * nombre: dashboard.screen.list
     */
    Route::get('screen/list', [ScreenController::class, 'getScreens'])
        ->name('dashboard.screen.list')
        ->middleware('auth');

    /**
     * Actualiza una captura de pantalla en la base de datos
     *
     * Nombre y ubicación solamente son editables por ahora
     *
     * nombre: dashboard.screen.update
     */
    Route::post('screen/update/{screen}', [ScreenController::class, 'updateScreen'])
        ->name('dashboard.screen.update')
        ->middleware('auth');

    /**
     * Elimina una captura de pantalla de la base de datos
     *
     * nombre: dashboard.screen.destroy
     */
    Route::post('screen/destroy/{screen}', [ScreenController::class, 'destroyScreen'])
        ->name('dashboard.screen.destroy')
        ->middleware('auth');
    
    /*--------------------------------------------------
     * / GRABACIONES DE PANTALLA DEL USUARIO DE FIKREA
     ---------------------------------------------------*/

     /**
     * Muestra la lista de todos los sms
     * ELIMINAR
     * TEMPORAL PORQUE NO HAY INTERNET
     *
     * nombre: dashboard.sms.list
     */
     Route::get('/sms/list', [App\Http\Controllers\BackendController::class, 'smses'])
         ->name('dashboard.sms.list');
});
