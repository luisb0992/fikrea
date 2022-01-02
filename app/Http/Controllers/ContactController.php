<?php

/**
 * Controlador de Contactos
 *
 * @author    javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

use App\Models\Contact;
use App\Models\Guest;

use Fikrea\ModelAndView;

class ContactController extends Controller
{
    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Muestra la vista para editar o crear un contacto
     *
     * @param int|null $id El id del contacto
     *                     o null para crear uno nuevo
     *
     * @return string                           Una vista
     */
    public function edit(?int $id = null): string
    {
        // Si se está visualziando un contacto ya existente
        if ($id) {
            // Obtenemos el contacto
            $contact = Contact::findOrFail($id);

            // Comprobamos si el usuario actual puede visualizarlo
            $this->authorize('view', $contact);
        }

        $mav = new ModelAndView('dashboard.contacts.contact');

        return $mav->render(
            [
                'contact' => $id ? Contact::findOrFail($id) : null,
            ]
        );
    }

    /**
     * Muestra la vista con la lista de contactos existentes
     *
     * @return string
     */
    public function list(): string
    {
        // Obtenemos el usuario
        $user = Auth::user() ?? Guest::user();

        // Muestra la lista de contactos del usuario
        $mav = new ModelAndView('dashboard.contacts.contact-list');

        return $mav->render(
            [
                'contacts' => $user->contacts()->paginate(10),
            ]
        );
    }

    /**
     * Guarda el contacto
     *
     * @param Request $request La solicitud
     *
     * @return RedirectResponse|JsonResponse    Una redirección o una respuesta json
     */
    public function save(Request $request)
    {
        // Obtener el usuario autenticado para que el contacto se añada a su lista
        $user = Auth::user() ?? Guest::user();

        // Obtiene el id del contacto o null si es un nuevo contacto
        $id = $request->input('id');

        // Validamos los datos de entrada
        $data = $request->validate(
            [
                'id'        => 'integer|nullable',
                'name'      => 'string|nullable|max:100',
                'lastname'  => 'string|nullable|max:255',
                'email'     => $id ? 'email|nullable|max:100' : 'email|nullable|required_without:phone|max:100',
                'phone'     => $id ? 'string|nullable|max:50' : 'string|nullable|required_without:email|max:50',
                'dni'       => 'string|nullable|max:20',
                'company'   => 'string|nullable|max:255',
                'position'  => 'string|nullable|max:100'
            ]
        );

        $contactAlreadyExists = null;

        // Comprueba si el usuario ya tiene dada de alta la dirección de correo del contacto
        if ($data['email']) {
            $contactAlreadyExists = $user->contacts()
                ->where('email', $data['email'])
                ->where('id', '!=', $id)
                ->first();
        }

        if ($request->ajax()) {
            if ($contactAlreadyExists) {
                return response()->json(['info' => Lang::get('El contacto ya existe')]);
            }
        } else {
            if ($contactAlreadyExists) {
                return redirect()->back()
                    ->with('error', Lang::get('El contacto ya existe'));
            }
        }

        // Guardar el contacto
        if ($id) {
            // Obtenemos el contacto ya existente
            $contact = Contact::findOrFail($id);

            // Comprueba si el usuario actual puede actualizar el contacto
            $this->authorize('update', $contact);

            // Actualiza el contacto existente
            $contact->update($data);
        } else {
            // Crea un nuevo contacto
            $user->contacts()->create($data);
        }

        // Redirigir para poder seguir introduciendo más contactos
        if ($request->ajax()) {
            return response()->json(['infoSuccess' => Lang::get('El contacto se ha guardado con éxito')]);
        } else {
            return redirect()->back()->with('message', Lang::get('El contacto se ha guardado con éxito'));
        }
    }

    /**
     * Elimina un contacto
     *
     * @param int $id El id del contacto a eliminar
     *
     * @return RedirectResponse                 Una redirección
     */
    public function delete(int $id): RedirectResponse
    {
        // Obtener el usuario autenticado
        $user = Auth::user() ?? Guest::user();

        // Obtenermos el contacto a eliminar
        $contact = Contact::findOrFail($id);

        // Comprueba si el usuario actual puede eliminar
        $this->authorize('delete', $contact);

        // Eliminamos el contacto
        $contact->delete();

        // Redirigir a la lista de contactos
        return redirect()->back()
            ->with('message', Lang::get('El contacto se ha eliminado con éxito'));
    }

    /**
     * Encuentra un contacto por su dirección de correo
     *
     * @param Request $request La solicitud
     *
     * @return JsonResponse                     Una respuesta JSON con el contacto
     *                                          o HTTP/404 si no existe
     */
    public function findByEmail(Request $request): JsonResponse
    {
        // Obtiene la dirección de correo del contacto
        $email = $request->input('email');

        // Obtiene el usuario actual
        $user = Auth::user() ?? Guest::user();

        // Obtiene el contacto
        $contact = $user->contacts->where('email', '=', $email)->first();

        // Lo envía
        return $contact ?
            response()->json($contact)
            :
            response()->json(Lang::get('El contacto no existe'), 404);
    }

    /**
     * Encuentra un contacto por su dirección de correo y si no se encuentra
     * no devuelve error sino un aviso
     *
     * @param Request $request La solicitud
     *
     * @return JsonResponse                     Una respuesta JSON con el contacto
     *                                          o HTTP/404 si no existe
     */
    public function findByEmailWithoutError(Request $request): JsonResponse
    {
        // Obtiene la dirección de correo del contacto
        $email = $request->input('email');

        // Obtiene el usuario actual
        $user = Auth::user() ?? Guest::user();

        // Obtiene el contacto
        $contact = $user->contacts->where('email', '=', $email)->first();

        // Lo envía
        return $contact ? response()->json(['data' => $contact]) :
            response()->json(['info' => Lang::get('El contacto no existe')]);
    }

    /**
     * Encuentra un contacto por su número de teléfono
     *
     * @param Request $request La solicitud
     *
     * @return JsonResponse                     Una respuesta JSON con el contacto
     *                                          o HTTP/404 si no existe
     */
    public function findByPhone(Request $request): JsonResponse
    {
        // Obtiene el número de teléfono del contacto
        $phone = $request->input('phone');

        // Obtiene el usuario actual
        $user = Auth::user() ?? Guest::user();

        // Obtiene el contacto
        $contact = $user->contacts->where('phone', '=', $phone)->first();

        // Lo envía
        return $contact ?
            response()->json($contact)
            :
            response()->json(Lang::get('El contacto no existe'), 404);
    }

    /**
     * Encuentra un contacto por su número de teléfono y si no lo encuentra
     * no devuelve un error sino un aviso
     *
     * @param Request $request La solicitud
     *
     * @return JsonResponse                     Una respuesta JSON con el contacto
     *                                          o HTTP/404 si no existe
     */
    public function findByPhoneWithoutError(Request $request): JsonResponse
    {
        // Obtiene el número de teléfono del contacto
        $phone = $request->input('phone');

        // Obtiene el usuario actual
        $user = Auth::user() ?? Guest::user();

        // Obtiene el contacto
        $contact = $user->contacts->where('phone', '=', $phone)->first();

        // Lo envía
        return $contact ? response()->json(['data' => $contact]) :
            response()->json(['info' => Lang::get('El contacto no existe')]);
    }
}
