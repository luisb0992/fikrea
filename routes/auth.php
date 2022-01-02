<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;

/**
 * Rutas de autenticación
 *
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

/**
 * Todas las rutas que requieren idioma llevan el middleware 'language'
 *
 * Todas las rutas llevan el prefijo:
 *
 * /dashboard/...
 *
 */
Route::group(['middleware' => 'language'], function () {

    /**
     * Página login de la aplicación
     * nombre: dashboard.login
     */
    Route::get('/login', [LoginController::class, 'showLoginForm'])
         ->name('dashboard.login')
         ->middleware('guest');

    /**
     * Cierra de sesión de la aplicación
     * nombre: landing.logout
     */
    Route::get('/logout', [LoginController::class, 'logout'])
         ->name('dashboard.logout');

    /**
     * Página de registro de un nueva cuenta de usuario
     * nombre: dashboard.register
     */
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])
          ->name('dashboard.register');

    /**
     * Verifica la cuenta de usuario
     * nombre: dashboard.verify.user
     *
     * validationCode:    El código de verificación de la cuenta de usuario
     */
    Route::get('/verify/account/{validationCode}', [RegisterController::class, 'verifyUserAccount'])
         ->name('dashboard.verify.user');

    /**
     * El usuario se ha registrado con éxito
     * y debe validar la cuenta para poder acceder
     *
     * nombre: dashboard.registration.succesfully
     */
    Route::get('registration/done', [RegisterController::class, 'userAccountCreatedSuccesfully'])
         ->name('dashboard.registration.succesfully');

    /**
     * Muestra la vista para recordar la contraseña
     *
     * El usuario debe proporcionar la dirección de correo con la que se registró
     * y se le envía un correo con un enlace para ello
     *
     * nombre: dashboard.rememberme
     */
    Route::get('rememberme', [ResetPasswordController::class, 'rememberme'])
          ->name('dashboard.rememberme');

    /**
     * Muestra la vista que muestra que se ha enviado un correo de recuperación/cambio
     * de contraseña
     *
     * nombre: dashboard.rememberme.done
     */
    Route::get('rememberme/done', [ResetPasswordController::class, 'remembermeDone'])
          ->name('dashboard.rememberme.done');

    /**
     * Envía la solicitud para el cambio de contraseña
     *
     * Se envía un correo a la dirección de correo indicada
     * con un enlace que permite cambiar la contraseña
     *
     * nombre: dashboard.password.request
     */
    Route::post('password/request', [ResetPasswordController::class, 'sendChangePasswordRequest'])
         ->name('dashboard.password.request');

    /**
     * Muestra la vista del formulario para el cambio de contraseña
     *
     * nombre: dashboard.password.change
     */
    Route::get('password/change/{rememberToken}', [ResetPasswordController::class, 'changePassword'])
         ->name('dashboard.password.change');

    // Si no se ha proporcionado un token se redirige a HTTP/404
    Route::get('password/change', function () {
        abort(404);
    });

    /**
     * Actualiza la contraseña del usuario
     *
     * nombre: dashboard.password.update
     */
    Route::post('password-change', [ResetPasswordController::class, 'updatePassword'])
         ->name('dashboard.password.update');
});
