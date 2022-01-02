<?php

/**
 * ResetPasswordController
 *
 * Controlador de recuperación de la contraseña
 *
 * El proceso de recuperación de contraseña consiste en enviar un mnesaje
 * a la dirección de correo registrada y que en ella se incluya un enlace con
 * un código de validación (token) que permite redefinir una nueva contraseña
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Lang;

use Fikrea\ModelAndView;

use App\Models\User;

/**
 * Excepciones requeridas
 */
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ResetPasswordController extends Controller
{
    /**
     * Redirige a los usuarios después de completar el formulario de recuerdo de contraseña
     *
     * @var string
     */
    protected $redirectTo = '/landing/login';

    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest');
    }

    /**
     * Envía la solicitud de cambio de contraseña al usuario
     *
     * Al usuario se le manda un mensaje de correo con un enlace que permite
     * cambiar la contraseña
     *
     * @return string                           Una vista
     */
    public function rememberme():string
    {
        // Renderiza la vista de la página de recuerdo de la contraseña
        $mav = new ModelAndView('dashboard.auth.rememberme');

        return $mav->render();
    }

    /**
     * Envía la solicitud de cambio de contraseña al usuario
     *
     * Al usuario se le manda un mensaje de correo con un enlace que permite
     * cambiar la contraseña
     *
     * @param Request $request                  La solicitud
     *
     * @return mixed
     */
    public function sendChangePasswordRequest(Request $request):RedirectResponse
    {
        // Valida los datos de entrada
        $validator = Validator::make($request->all(), [
            // La dirección de email es requerida
            'email' => 'required|email',
        ]);

        $validator->validate();
        
        // Si la validación es correcta

        $data = $validator->valid();

        // Se trata de obtener el usuario de email dado
        try {
            $user = User::getUserByEmail($data['email']);
        } catch (ModelNotFoundException $e) {
            // Si el usuario no existe
            throw ValidationException::withMessages(
                [
                    'email' => Lang::get('La dirección de correo no se encuentra registrada')
                ]
            );
        }

        // Crea un token de recordatorio de contraseña
        User::createValidationToken($user);

        // Se crear una solicitud para cambiar la contraseña
        // Se envía un correo con un enlace para realizar esta acción
        event(new PasswordReset($user));

        return redirect()->route('dashboard.rememberme.done');
    }

    /**
     * Muestra la vista con el formulario para el cambio de la contraseña
     *
     * @param string $validationCode            El token o código de validación  para cambiar la contraseña
     *
     * @return string                           Una vista
     */
    public function changePassword(string $validationCode):string
    {
        // Renderiza la vista de la página de cuenta confirmada con éxito
        $mav = new ModelAndView('dashboard.auth.password-change');

        return $mav->render(
            [
                'validationCode' => $validationCode,
            ]
        );
    }

    /**
     * Muestra la vista que confirma que se ha enviado un correo
     * para la recuperación de la contraseña de acceso a la cuenta
     *
     * @return string                           Una vista
     */
    public function remembermeDone():string
    {
        $mav = new ModelAndView('dashboard.auth.rememberme-done');

        return $mav->render();
    }

    /**
     * Valida los datos de entrada
     *
     * @param array $data                       Los datos de entrada
     *
     * @return Validator                        Un validador
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            // Un token es requerido
            'validationCode' => 'required|string',
            //
            // La regla 'confirmed' obliga a que los valores
            // de los campos password y password_confirmation sean iguales
            //
            // @link https://laravel.com/docs/8.x/validation#rule-confirmed
            //
            // La expresión regular obliga a incluir algún carácter de los siguientes:
            //
            //  una mayúscula (A-Z)
            //  una minúscula (a-z)
            //  una cifra numérica (0-9)
            //  un símbolo a elección entre los sigientes: . @ $ ! % * # ? &
            //
            'password'  => 'required|string|min:8|max:32|
                            confirmed|
                            regex:/[a-z]/|
                            regex:/[A-Z]/|
                            regex:/[0-9]/|
                            regex:/[.@$!%*#?&]/',
        ]);
    }

    /**
     * Actualiza la contraseña del usuario
     *
     * Se utiliza un código de validación o token para relacionar la información enviada
     * con la nueva contraseña con el usuario del sistema
     *
     * @param Request $request                  La solicitud
     *
     * @return string                           Una vista
     */
    public function updatePassword(Request $request)
    {
        $this->validator($request->all())->validate();
   
        // Si la validación es correcta, es decir, la contraseña cumple el formato
        // y coincide con el valor confirmado

        $data = $request->all();
        
        // Se trata de obtener el usuario por el token o código de validación proporcionado
        try {
            // Obtiene el usuario
            $user = User::getUserByValidationCode($data['validationCode']);
        } catch (ModelNotFoundException $e) {
            // Si el código de verificación no existe, se devuelve un error HTTP/404
            abort(404);
        }
        
        // Cambia la contraseña del usuario
        $user->changePassword($data['password']);

        // Renderiza la vista de contraseña de la cuenta cambiada con éxito
        $mav = new ModelAndView('dashboard.auth.password-change-done');

        return $mav->render();
    }
}
