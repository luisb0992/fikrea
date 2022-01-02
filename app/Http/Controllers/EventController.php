<?php

/**
 * Controlador para la gestion de eventos de la app
 *
 * @author LuisBarDev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers;

// Modelos necesarios
use App\Models\Event;
use App\Models\Guest;
use App\Models\PurposeEvent;

// herramientas
use Fikrea\ModelAndView;
use DB;
use App\Enums\Event\EventStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Str;

class EventController extends Controller
{
    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Devuelve la lista de eventos creados.
     *
     * @return string           Una vista
     */
    public function list(): string
    {
        // Obtenemos el usuario actual
        $user = Auth::user() ?? Guest::user();

        $mav = new ModelAndView('dashboard.event.list');

        return $mav->render([
            'events' => Event::isActive(),
        ]);
    }

    /**
     * Devuelve la lista de plantillas y borradores de los diferentes eventos
     *
     * @return string           Una vista
     */
    public function listTemplatesAndDraft(): string
    {
        // Obtenemos el usuario actual
        $user = Auth::user() ?? Guest::user();

        // eventos del usuario ordenados
        $events = Event::draftEventsByUser($user);

        $mav = new ModelAndView('dashboard.events.templatesdrafts');

        return $mav->render([

            // eventos segun el estado, modo borrador
            'events' => $events->paginate(config('documents.pagination')),

            // El espacio ocupado por archivos y documentos
            'diskSpace' => $user->diskSpace,
        ]);
    }

    /**
     * Devuelve la vista para crear o actualizar un evento
     *
     * @param integer $id           El id del evento
     * @return string
     */
    public function edit(int $id = null): string
    {
        $mav = new ModelAndView('dashboard.events.edit');

        if ($id) {

            // el evento
            $event = Event::findOrFail($id);

            // Comprobamos si el usuario actual puede visualizarlo
            $this->authorize('edit', $event);
        }

        return $mav->render([
            'event'     => $event ?? null,
            'purposes'  => PurposeEvent::isActive()
        ]);
    }

    /**
     * Guardar un evento en sus diferentes formas
     *
     * @param integer $id           El id del evento si existe
     * @return JsonResponse         La respuesta Json
     */
    public function save(int $id = null): JsonResponse
    {
        // usuario de la app o usuario invitado
        $user = Auth::user() ?? Guest::user();

        // la url donde se redirecciona luego de finalizar el proceso
        $url = null;

        if ($id) {

            $event = Event::findOrFail($id);

            // actualizar el evento
            $event->update([
                'title'         => request()->input('title'),
                'description'   => request()->input('description')
            ]);

            $url = route('dashboard.event.list');
        } else {

            // dd(request()->all());
            request()->validate([
                'title'             => 'required',
                'image'             => 'nullable|max:5128',
                'video'             => 'nullable',
                'start_date'        => 'required',
                'purpose_event_id'  => 'required',
                'type'              => 'required',
            ], [
                'title.required'            => Lang::get('El título del evento es requerido'),
                'start_date.required'       => Lang::get('La fecha inicial del evento es requerida'),
                'purpose_event_id.required' => Lang::get('El propósito del evento es requerida'),
                'type.required'             => Lang::get('El tipo de evento es requerido'),
            ]);

            // si es un borrador
            $isDraft = request()->input('isDraft');

            // determinar si es un evento publico
            $isPublic = Event::isPublic(request()->input('type'));

            // si es un borrador su status sera a modo borrador
            // sino se colocara en espera
            $status = $isDraft == "true" ? EventStatus::DRAFT_EVENT : EventStatus::SCHEDULED_EVENT;

            // los datos del evento
            $eventData = [
                'title'             => request()->input('title'),
                'description'       => request()->input('description'),
                'start_date'        => request()->input('start_date'),
                'end_date'          => request()->input('end_date'),
                'purpose_event_id'  => request()->input('purpose_event_id'),
                'type'              => request()->input('type'),
                'is_anonymous'      => request()->input('is_anonymous'),
                'kiosk_mode'        => request()->input('kiosk_mode'),
                'min_goal'          => request()->input('min_goal'),
                'max_goal'          => request()->input('max_goal'),
                'is_public'         => $isPublic,
                'event_status'      => $status,
            ];

            $eventId = DB::transaction(function () use ($user, $eventData) {

                // guardar los datos del evento
                $event = $user->events()->create($eventData);

                // guardar la imagen del evento si existe
                // dependiendo de lo que se obtenga guardar un blob o una url
                if (request()->image) {
                    $isFile  = request()->hasFile('image');
                    $file    = request()->file('image');
                    $urlFile = request()->image;
                    $event->saveEventFile($isFile, $file, $urlFile, 'image');
                }

                // guardar el video del evento si existe
                // dependiendo de lo que se obtenga guardar un blob o una url
                if (request()->video) {
                    $isFile  = request()->hasFile('video');
                    $file    = request()->file('video');
                    $urlFile = request()->video;
                    $event->saveEventFile($isFile, $file, $urlFile, 'video');
                }

                return $event->id;
            });

            // definir la url para continuar con el proceso o guardar
            // si todo sale bien en la transaccion se devuelve el id del evento con la ruta
            // sino ocurrio un problema al guardar los respectivos datos
            if ($eventId) {
                if ($isDraft == "true") {
                    $url = route('dashboard.event.list.templatesAndDraft');
                } else {

                    // si es un evento publico pasa directamente a contruir su formulario
                    if ($isPublic) {
                        $url = route('dashboard.event.builder.questionsanswers', ['id' => $eventId]);

                    // sino se envia a llenar el censo de participantes
                    } else {
                        $url = route('dashboard.event.census', ['id' => $eventId]);
                    }
                }
            }
        }

        return response()->json([
            'url'   => $url
        ]);
    }

    /**
     * Devuelve la vista de la creacion de un censo para un
     * evento especifico, en este caso un evento tipo privado
     *
     * @param integer $id           El id del evento
     * @return string               La vista para el censo
     */
    public function census(int $id): string
    {
        // Obtenemos el evento
        $event = Event::findOrFail($id);

        if ($event->cannotBeEdited()) {
            abort(403);
        }

        // Verifica si el usuario actual está autorizado para crear un censo de participantes
        $this->authorize('census', $event);

        $mav = new ModelAndView('dashboard.events.census');

        return $mav->render([
            'event' => $event,
        ]);
    }

    /**
     * Guardar el censo de participantes que intervienen en el evento
     *
     * @param integer $id               El id del evento
     * @return JsonResponse             Una respuesta json
     */
    public function saveCensus(int $id): JsonResponse
    {
        $event = Event::findOrFail($id);

        // si no puede ser editado
        if ($event->cannotBeEdited()) {
            abort(403);
        }

        // si es un borrador
        $isDraft = request()->input('isDraft');

        // los participantes del evento
        $users = request()->input('users');

        // se añade el token de acceso para cada ususario del evento
        foreach ($users as $user) {
            $user['token'] = Str::random(64);
        }

        $event->participants()->createMany($users);

        // la url a redireccionar
        $url = null;

        // si se guarda como borrador
        // sino se continua con el proceso
        if ($isDraft == "true") {
            $url = route('dashboard.event.list.templatesAndDraft');
        } else {
            $url = route('dashboard.event.builder.questionsanswers', ['id' => $id]);
        }

        return response()->json([
            'url' => $url
        ]);
    }

    /**
     * Devuelve la vista de creacion de preguntas y respuestas para un tipo de evento
     *
     * @param integer $id           El id del evento
     * @return string               La vista
     */
    public function builderQuestionAnswers(int $id): string
    {
        // Obtenemos el evento
        $event = Event::findOrFail($id);

        // si no puede ser editado
        if ($event->cannotBeEdited()) {
            abort(403);
        }

        // Verifica si el usuario actual está autorizado para crear un censo de participantes
        $this->authorize('builderQuestionAnswers', $event);

        $mav = new ModelAndView('dashboard.events.builder-question-answers');

        return $mav->render([
            'event' => $event,
        ]);
    }
}
