<?php

/**
 * LoginController
 *
 * Controlador de inicio de sesión del sitio
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Validation\ValidationException;
use \Illuminate\Contracts\Auth\StatefulGuard;

use App\Events\FailedLoginAttemptEvent;

use Fikrea\ModelAndView;

use App\Models\User;

class LoginController extends Controller
{
    /**
     * Traits por defecto
     */
    use RedirectsUsers, ThrottlesLogins;

    /**
     * Página a la que los usuarios son redirigidos tras realizar el login
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Muestra el formulario de login
     *
     * @return string|RedirectResponse          Una vista o una redirección
     */
    public function showLoginForm()
    {
        $mav = new ModelAndView('dashboard.auth.login');
        
        return $mav->render();
    }

    /**
     * Maneja el login de la aplicación
     *
     * @param  Request  $request                La solicitud
     *
     * @return RedirectResponse|Response|JsonResponse
     *
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        // Valida los datos de entrada
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Valida los valores proporcionados
     *
     * @param  Request  $request                La solicitud
     *
     * @return void
     *
     * @throws ValidationException              Los datos no son válidos
     */
    protected function validateLogin(Request $request): void
    {
        $request->validate(
            [
                // Valida el usuario (email)
                'email'     => 'required|string',
                // Valida la contraseña
                'password'  => 'required|string',
            ]
        );
    }

    /**
     * Intenta el inicio de sesión
     *
     * El método devuelve true si el inicio de sesión es válido
     *
     * @param  Request  $request                La solicitud
     *
     * @return bool                             true si las credenciales del usuario son correctas
     *                                          false en caso contratio
     *
     * @throws ValidationException              Usuario no ha validado su cuenta
     */
    protected function attemptLogin(Request $request): bool
    {
        // Obtenemos el usuario que está intentando autenticarse
        $user = User::where('email', $request->input('email'))->first();

        // Comprueba si el usuario no ha validado aún su cuenta
        if ($user && !$user->email_verified_at) {
            throw ValidationException::withMessages([
                'session' => [ Lang::get('El usuario no ha sido validado') ],
            ]);
        }
        
        // Si la cuenta de usuario no está activa, no se permite el login
        if ($user && !$user->active) {
            return false;
        }

        /**
         * Efectúa la validación de las credenciales
         *
         * El primer argumento es un array que contiene las credenciales:
         *
         * ['email' => 'javi@gestoy.com, 'password' => 'pass']
         *
         * El segundo argumento es un valor booleano que vale true para recordar la sesión de usuario
         * y false en caso contrario. Para ello se examina la presencia del valor 'remember' o no
         */
        return $this->guard()->attempt($this->credentials($request), $request->filled('remember'));
    }

    /**
     * Obtiene las credenciales para la solicitud
     *
     * @param  Request  $request                La solicitud
     *
     * @return array                            las credenciales para la solicitud
     */
    protected function credentials(Request $request): array
    {
        return $request->only('email', 'password');
    }

    /**
     * Devuelve el campo que se usa en la autenticación
     *
     * @return string                           El campo utilizado para autentira al usuario
     */
    public function username():string
    {
        return 'email';
    }

    /**
     * Envia la respuesta una vez el usuario ha sido autenticado
     *
     * @param  Request  $request                La solicitud
     *
     * @return Response|RedirectResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        return $request->wantsJson() ?
            new Response('', 204) : redirect()->intended($this->redirectPath());
    }

    /**
     * Envía la respuesta de acceso no válido
     *
     * @param  Request $request                 La solicitud
     *
     * @return Response                         La respuesta
     *
     * @throws ValidationException              Inicio de sesión no valido
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        /**
         * Aqui chequeamos por que no se ha podido autenticar
         */

        // Obtenemos el usuario que está intentando autenticarse
        $user = User::where('email', $request->input('email'))->first();

        if ($user) {
            // lanzamos evento de notificación al usuario
            event(new FailedLoginAttemptEvent($user, $request));

            throw ValidationException::withMessages([
                'session' => [Lang::get('Comprueba los datos e inténtelo de nuevo')],
            ]);
        }

        // Esta info nunca se brinda en un login porque se brinda para ataques
        // solo se pone credenciales invalidas o algo relacionado
        throw ValidationException::withMessages([
            'session' => [Lang::get('Comprueba los datos e inténtelo de nuevo')],
        ]);
    }

    /**
     * Cierra la sesión del usuario
     *
     * @param  Request  $request                La solicitud
     *
     * @return Response                         La respuesta
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $request->wantsJson() ? new Response('', 204) : redirect()->route('dashboard.login');
    }

    /**
     * Obtiene la guarda
     *
     * @return StatefulGuard
     */
    protected function guard():StatefulGuard
    {
        return Auth::guard();
    }
}
