<?php

/**
 * DashBoardController
 *
 * @author    javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

use App\Models\Guest;
use App\Models\Notification;
use App\Models\Signer;
use App\Models\File;
use Fikrea\ModelAndView;
use Lang;

class DashBoardController extends Controller
{
    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Muestra la página principal de la dashboard page o zona de usuario
     *
     * @return string                           Una vista
     */
    public function show(): string
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        // Renderiza la vista de la página de inicio del dashboard de usuario
        $mav = new ModelAndView('dashboard.home.home');

        return $mav->render(
            [
                // Las notificaciones que no han sido leídas
                'notifications' => $user->notifications->filter(fn ($notification) => !$notification->read_at)
            ]
        );
    }

    /**
     * Muestra la página para cambiar la información del perfil del usuario
     *
     * @return string
     */
    public function profile(): string
    {
        // Renderiza la vista de la página landing
        $mav = new ModelAndView('dashboard.profile');

        return $mav->render();
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
                //  un símbolo a elección entre los siguientes: . @ $ ! % * # ? &
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
        $user = Auth::user();

        $user->image = $contents;

        // Guarda la imagen
        $user->save();

        return response()->json(
            [
                'message'  => "Profile image saved for user {$user->email}",
            ]
        );
    }

    /**
     * Muestra la vista de configuración de las opciones del usuario
     *
     * @return string                           Una vista
     */
    public function config(): string
    {
        // Muestra la vista de configuración del usuario
        $mav = new ModelAndView('dashboard.config.config');

        return $mav->render();
    }

    /**
     * Guarda la configuración de las opciones del usuario
     *
     * @return JsonResponse                     Una respuesta JSON
     */
    public function saveConfig(): JsonResponse
    {
        // Obtiene el usuario
        $user = Auth::user();

        // Obtiene la configuración
        $config = request()->only('sign', 'audio', 'video', 'identificationDocument', 'notification');

        // Fija la nueva configuración del usuario
        $user->config = json_encode($config);

        // y la guarda
        $user->save();

        return response()->json($config);
    }

    /**
     * Marca una notificación como leída
     *
     * @return JsonResponse                     Una respuesta JSON
     */
    public function notificationRead(): JsonResponse
    {
        // Obtiene la notificación
        $notification = Notification::findOrFail(request()->input('id'));

        $this->authorize('read', $notification);

        $notification->read();

        return response()->json($notification);
    }

    /**
     * Muestra la vista de configuración
     * para grabar audio
     *
     * @return string                           Una vista
     */
    public function configAudio(): string
    {
        // Muestra la vista de configuración para grabar audio
        $mav = new ModelAndView('dashboard.config.config-audio');

        return $mav->render();
    }

    /**
     * Muestra la vista de configuración
     * para grabar video
     *
     * @return string                           Una vista
     */
    public function configVideo(): string
    {
        // Muestra la vista de configuración para grabar video
        $mav = new ModelAndView('dashboard.config.config-video');

        return $mav->render();
    }

    /**
     * Guarda un comentario a un proceso especifico
     *
     * @return string                           una vista
     */
    public function saveComment(): string
    {
        request()->validate([
            'id'             => 'required',
            'comment'        => 'required',
            'validationType' => 'required',
        ]);

        // el request
        $process = (object) [
            'id'             => request()->input('id'),
            'comment'        => request()->input('comment'),
            'validationType' => request()->input('validationType')
        ];

        // el firmante
        $signer = Signer::findOrFail($process->id);

        // si ya el comentario fue realizado se le notifica al usuario
        if ($signer->getIfCommentExists($process->validationType)) {
            return response()->json(['res' => 2]);
        }

        // Obtenemos la validación del proceso del firmante
        $validation = $signer->validations()
            ->filter(fn ($validation) => $validation->validation == $process->validationType)
            ->first();

        // guardar el comentario
        $validation->saveComment($process->comment);

        // la respuesta json
        return response()->json(['res' => 1]);
    }

    public function saveShareSocialNetwork(): JsonResponse
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        // validar primero el request
        request()->validate([
            'url'             => 'required',
            'social_network'  => 'required',
            'type'            => 'required',
            'id'              => 'required',
        ]);

        // dd(request()->all());
        // la data del request
        $data = [
            'user_id'           => $user->id,
            'url'               => request()->input('url'),
            'text'              => request()->input('text'),
            'social_network'    => request()->input('social_network'),
            'hashtag'           => request()->input('hashtag'),
        ];

        if (request()->input('type') == 'file') {
            $file = File::findOrFail(request()->input('id'));
            $file->shareSocialNetwork()->create($data);
        }

        return response()->json(['success' => Lang::get('URL generada correctamente')]);
    }
}
