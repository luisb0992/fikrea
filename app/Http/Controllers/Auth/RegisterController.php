<?php

/**
 * RegisterController
 *
 * Controlador de registro de usuarios
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use App;
use App\Models\User;
use App\Models\Guest;

use Fikrea\ModelAndView;
use Illuminate\Http\RedirectResponse;

class RegisterController extends Controller
{
    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest');
    }

    /**
     * Muestra el formulario de registro
     *
     * @return string                           Una vista
     */
    public function showRegistrationForm():string
    {
        $mav = new ModelAndView('dashboard.auth.register');
        
        return $mav->render();
    }

    /**
     * Actualiza los datos del usuario
     *
     * El usuario que se registra ya tiene la cuenta creada, únicamente hay que actualizar sus datos
     *
     * @param  array  $data                     Un array con los datos del usuario
     *
     * @return User                             El usuario
     */
    protected function updateUser(array $data):User
    {
        $user = Guest::user();

        $user->name             = $data['name'];
        $user->lastname         = $data['lastname'];
        $user->email            = $data['email'];
        $user->password         = Hash::make($data['password']);
        $user->validation_code  = Str::random(64);                  // Obtiene un código de validación de 64 carácteres
        $user->locale           = App::getLocale();
 
        $user->save();

        return $user;
    }

    /**
     * Maneja la solicitud de registro de un nuevo usuario
     *
     * @param Request $request                  La solicitud
     *
     * @return Response                         La respuesta
     */
    public function register(Request $request):RedirectResponse
    {
        // Obtiene el usuario invitado que se quiere registrar
        $user = Guest::user();

        // Valida los datos del usuario
        $userData = request()->validate([
            'name'      => 'required|string|max:255',
            'lastname'  => 'nullable|string|max:255',
            //
            // El email debe ser único pero se ignora el valor que ya tiene el usuario qur se registra
            //
            'email'     => "required|email|max:255|unique:users,email,{$user->id}",
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
        
        // Si la validación de los datos es correcta, se actualiza el usuario
        // con los datos de la solicitud
        $user = $this->updateUser($userData);

        // Se registra el usuario y se lanzan los eventos asociados a este proceso
        // que incluye el envío de un correo para la validación de la cuenta
        event(new Registered($user));

        // Se invalida la sesión actual para que el usuario valide la cuenta
        // y pueda acceder ya a la aplicación como usuario registrado
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // El usuario ha sido registrado y se redirige a la página indicada
        return redirect()->route('dashboard.registration.succesfully');
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return StatefulGuard
     */
    protected function guard():StatefulGuard
    {
        return Auth::guard();
    }

    /**
     * Muestra la vista de cuenta de usuario creada con éxito
     * La cuenta queda pendiente de ser validada
     *
     * @return void
     */
    public function userAccountCreatedSuccesfully()
    {
        // Renderiza la vista de la página de cuenta confirmada con éxito
        $mav = new ModelAndView('dashboard.auth.registration-done');

        return $mav->render();
    }

    /**
     * Verifica la cuenta de usuario con el código de validación proporcionado
     *
     * @param string $validationCode            El código de validación de usuario
     *
     * @return string                           Una vista
     */
    public function verifyUserAccount(string $validationCode)
    {
        // Buscar el usuario con el código de verificación dado
        try {
            $user = User::getUserByValidationCode($validationCode);
        } catch (ModelNotFoundException $e) {
            // Si no existe usuario con el código de validación dado
            // devuelve la página de error 404
            return abort(404);
        }

        // Verifica lña cuenta del usuario
        $user->verifyAccount();

        // Renderiza la vista de la página de cuenta confirmada con éxito
        $mav = new ModelAndView('dashboard.auth.account-verified');

        return $mav->render();
    }
}
