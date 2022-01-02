<?php

/**
 * ProfileController
 *
 * Controlador para el perfil del usuario
 *
 * Tanto un usuario registrado (autenticado) como un usuario invitado (Guest) poseen perfil
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Cookie;

/**
 * Controladores requeridos
 */

use App\Http\Controllers\EmailController;

/**
 * Modelos requeridos
 */

use App\Models\User;
use App\Models\Guest;
use App\Models\Company;
use App\Models\CompanySharing;

/**
 * Fikrea
 */

use Fikrea\ModelAndView;

/**
 * Excepciones requeridas
 */

use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * @see https://github.com/umpirsky/Transliterator
 */

use Countries;
use Illuminate\Support\Str;
use Response;

class ProfileController extends Controller
{
    use Traits\Statistical;
    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Muestra la página para cambiar la información del perfil del usuario
     *
     * @return string|RedirectResponse          Una vista o redirección
     */
    public function show()
    {
        // Renderiza la vista de la página de actualización del perfil en el dashboard
        $mav = new ModelAndView('dashboard.profile.profile');
        $language = config('app.locale');
        $countries = [];

        try {
            /**
             * @see https://github.com/umpirsky/Transliterator
             */
            $countries = Countries::getList($language == 'cn' ? 'zh' : $language);
        } catch (\RuntimeException $e) {
            // Si falla se devuelven los paises en el idioma nativo de la app
            $countries = Countries::getList('es');
        }

        return $mav->render([
            'countries' => $countries,
        ]);
    }

    /**
     * Muestra la página para recuperar una sesisón anterior de un usuario invitado
     * Para ello el usuario necesitará el token
     *
     * @return string|RedirectResponse          Una vista o redirección
     */
    public function session(): string
    {
        $token = Cookie::get(config('session.cookie'));

        // Si no hay cookie de sesión (la cookie ha podido expirar)
        if (!$token) {
            return redirect()->route('dashboard.login');
        } else {
            // Renderiza la vista de la página de actualización del perfil en el dashboard
            $mav = new ModelAndView('dashboard.session');

            return $mav->render(
                [
                    // El token del usuario invitado
                    'guestToken' => $token,
                ]
            );
        }
    }

    /**
     * Recupera una sesión anterior
     *
     * Un usuario invitado puede recuperar la sesión para acceder a los datos que
     * almacenó y a la actividad que mantuvo en la misma
     *
     * @param string $token El token de la sesión a recuperar
     *                      Es el token del usuario invitado
     *
     * @return RedirectResponse                 Una redirección
     */
    public function recoverySession(string $token): RedirectResponse
    {
        // Obtiene el usuario al que pertenece el token suministrado (si existe)
        $oldUser = User::findByGuestToken($token);

        // Realiza el login con al antiguo usuario (al que pertenece el token dado)
        Auth::login($oldUser);

        return redirect()->route('dashboard.home');
    }

    /**
     * Envía a un usuario el token de una sesión anterior conocida la dirección de correo
     * que utilizó para operar
     *
     * @return RedirectResponse                 Una redirección
     */
    public function sendSessionToken(): RedirectResponse
    {
        // Obtiene la dirección de correo validada
        $data = request()->validate(
            [
                'email' => 'required|email|max:255',
            ]
        );

        $email = $data['email'];

        // Obtiene el usuario al que pertenece la dirección de correo
        try {
            $oldUser = User::where('email', $email)
                ->whereNotNull('guest_token')
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw ValidationException::withMessages(
                [
                    'email' => Lang::get('La dirección de correo no existe'),
                ]
            );
        }

        // Envía un correo al usuario con el token de sesión
        EmailController::sendSessionTokenRecoveryEmail($oldUser);

        return redirect()->route('dashboard.home');
    }

    /**
     * Guarda el perfil del usuario y los datos de facturacion
     *
     * @param Request $request La solicitud
     *
     * @return RedirectResponse                 Un redirección
     */
    public function save(Request $request): RedirectResponse
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        // Valida los datos de entrada
        $rules = [
            // El tipo de cuenta, personal (0) o de empresa (1)
            'type'      => 'required|integer|min:0|max:1',
            // Atributos generales
            'name'      => 'required|string|max:255',
            'lastname'  => 'nullable|string|max:255',
            'address'   => 'nullable|string|max:255',
            'phone'     => 'nullable|string|max:255',
            'city'      => 'nullable|string|max:255',
            'province'  => 'nullable|string|max:255',
            'country'   => 'nullable|string|max:255',
            'code_postal' => 'nullable|max:6',
            'dial_code'  => 'nullable|string|max:5',
            // Atributos para la cuenta de empresa
            'company'   => 'nullable|string|max:255',
            'position'  => 'nullable|string|max:255',
            // Atributos para la facturacion
            'companyName'       => 'nullable|string|max:255',
            'companyCif'        => 'nullable|string|max:50',
            'companyAddress'    => 'nullable|string|max:255',
            'companyPhone'      => 'nullable|string|max:255',
            'companyCity'       => 'nullable|string|max:255',
            'companyProvince'   => 'nullable|string|max:255',
            'companyCountry'    => 'nullable|string|max:255',
            'companyCodePostal' => 'nullable|string|max:6',
            'companyDialCode'   => 'nullable|string|max:5',
            'companyEmail'      => 'nullable|string|max:255',
        ];

        // Si el usuario es invitado se puede cambiar la dirección de correo
        // siempre que no coincida con otra ya existente ignorando el usuario actual
        if (Auth::guest()) {
            $rules['email'] = [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ];
        }

        $data = $request->validate($rules);

        // Actualiza su información
        $user->fill($data);

        // Guarda la información
        $user->save();

        // Cuando es un usuario invitado estos datos no vienen del formulario
        if (!Auth::guest()) {
            // Obtiene los datos de la compañía
            $companyData = [
                'user_id'   => $user->id,
                'name'      => $data['companyName'],
                'cif'       => $data['companyCif'],
                'address'   => $data['companyAddress'],
                'phone'     => $data['companyPhone'],
                'city'      => $data['companyCity'],
                'province'  => $data['companyProvince'],
                'country'   => $data['companyCountry'],
                'code_postal' => $data['companyCodePostal'],
                'dial_code' => $data['companyDialCode'],
                'email'     => $data['companyEmail'],
            ];

            // Si no había una compañía a la que facturar para el usuario se crea una
            // En caso contrario se utiliza la ya existente
            $company = $user->billing ?? new Company;

            // Completa la compañía con sus datos y la guarda
            $company->fill($companyData);
            $company->save();
        }

        // Envía la respuesta
        return redirect()->back()
            ->with('message', Lang::get('El perfil se ha guardado con éxito'));
    }


    /**
     * Cambia la contraseña del usuario
     *
     * @param Request $request La solicitud
     *
     * @return JsonResponse                     Un redirección
     */
    public function changePassword(Request $request): JsonResponse
    {
        // Valida los datos de entrada
        $data = $request->validate(
            [
                // La expresión regular obliga a incluir algún carácter de los siguientes:
                //
                //  una mayúscula (A-Z)
                //  una minúscula (a-z)
                //  una cifra numérica (0-9)
                //  un símbolo a elección entre los sigientes: . @ $ ! % * # ? &
                //
                'password'  => 'nullable|string|min:8|max:32|
                                confirmed|
                                regex:/[a-z]/|
                                regex:/[A-Z]/|
                                regex:/[0-9]/|
                                regex:/[.@$!%*#?&]/',
            ]
        );

        // Obtiene el usuario actual
        $user = Auth::user();

        // Guarda la información
        $user->changePassword($data['password']);

        // Envía la respuesta
        return response()->json(
            [
                'message'  => "New Password saved for user {$user->email}",
            ]
        );
    }

    /**
     * Sube y almacena la imagen del perfil del usuario
     *
     * @param Request $request La solicitud
     *
     * @return JsonResponse                     Una respuesta JSON
     */
    public function uploadProfileImage(Request $request): JsonResponse
    {
        // Valida la imagen
        request()->validate(
            [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]
        );

        // Obtiene la imagen y su contenido en base64
        $image    = $request->file('image');
        $contents = base64_encode(file_get_contents($image->getRealPath()));

        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        $user->image = $contents;

        // Guarda la imagen
        $user->save();

        return response()->json(
            [
                'message'  => "Se ha guardado la imagen del usuario {$user->email}",
            ]
        );
    }
    /**
     * Guarda el correo con el cual se compartiran los daros de facturacion
     *
     * @param Request $request
     *
     * @return RedirectResponse                 Una redirección
     */
    public function shareBilling(Request $request): RedirectResponse
    {
        // Obtiene la dirección de correo validada
        $request->validate([
            'shareEmail' => 'required|email|max:255',
        ]);

        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        $data               = $user->billing ? collect($user->billing)->except(
            ['id', 'created_at', 'updated_at']
        )->toArray() : [];
        $data['title']      = isset($request->title) ? $request->title : null;
        $data['comment']    = isset($request->comment) ? $request->comment : null;
        $data['signature']  = isset($request->signature) ? true : false;
        $data['token']      = Str::random(64);

        $companySharing = CompanySharing::create($data);

        if ($companySharing) {
            EmailController::shareBillingEmail($companySharing, $request->shareEmail, $user);
        }

        // Redirigir a la vista del perfil
        return redirect()->back()->with('message', Lang::get('Ha compartido sus datos de facturacion con éxito'));
    }

    /**
     * Genera un link para compartir los datos de facturacion del usuario
     *
     * @return JsonResponse
     */
    public function shareBillingForLink(): JsonResponse
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        $data               = $user->billing ? collect($user->billing)->except(
            ['id', 'created_at', 'updated_at']
        )->toArray() : [];
        $data['title']      = request()->title;
        $data['comment']    = request()->comment;
        $data['signature']  = request()->signature;
        $data['token']      = Str::random(64);

        $companySharing = CompanySharing::create($data);

        if ($companySharing) {
            return response()->json([
                'successSharing' => Lang::get('Link generado correctamente, ya puede pegarlo'),
                'urlSharing' => route('billing.viewBillingData', ['token' => $companySharing->token]),
            ]);
        } else {
            return response()->json([
                'infoSharing' => Lang::get('Ha ocurrido un error, intente más tarde'),
            ]);
        }
    }

    /**
     * Muestra los datos de facturacion del usuario
     *
     * @param $token          El token de acceso
     *
     * @return string         Una vista
     */
    public function viewBillingData(string $token)
    {
        // Renderiza la vista de la página de actualización del perfil en el dashboard
        $language = config('app.locale');
        $countries = [];

        // Obtiene los datos de facturacion
        $company = CompanySharing::findByToken($token);

        try {
            /**
             * @see https://github.com/umpirsky/Transliterator
             */
            $countries = Countries::getList($language == 'cn' ? 'zh' : $language);
        } catch (\RuntimeException $e) {
            // Si falla se devuelven los paises en el idioma nativo de la app
            $countries = Countries::getList('es');
        }

        $mav = new ModelAndView('workspace.sharingdata.sharing-billing-data');

        // Añade las estadísticas básicas a la vista
        // y que son compartidas por todas las vistas pues se muestran en el menú del backend
        $this->appendStats($mav);

        return $mav->render([
            'countries' => $countries,
            'company' => $company
        ]);
    }
}
