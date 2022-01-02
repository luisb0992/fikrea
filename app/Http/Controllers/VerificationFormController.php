<?php

/**
 * Controlador para la certificación y verificacion de un formulario de datos
 * como proceso independiente y fuera del documento
 *
 * @author LuisBarDev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers;

// Modelos
use App\Models\FormTemplate;
use App\Models\VerificationForm;
use App\Models\FormData;
use App\Models\Guest;
use App\Models\User;

// Clases necesarias
use App\Enums\WorkspaceStatu;
use Fikrea\ModelAndView;

// herramientas
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PDF;

class VerificationFormController extends Controller
{
    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function list()
    {
        // Obtenemos el usuario actual
        $user = Auth::user() ?? Guest::user();

        // Obtenemos las verificaciones de datos del usuario actual
        $verificationForm = VerificationForm::verificationByUser($user);

        $mav = new ModelAndView('dashboard.verificationform.list');

        return $mav->render(
            [
                // formularios paginados según configuración
                'verificationForm' => $verificationForm->paginate(config('documents.pagination')),

                // El espacio ocupado por archivos y documentos
                'diskSpace' => $user->diskSpace,
            ]
        );
    }

    /**
     * Renderizar la vista para crear o editar un formulario de datos
     *
     * @param integer|null $id          id del formulario o null si es una creacion
     * @return string                   la vista de la verificacion
     */
    public function edit(?int $id = null): string
    {
        // Si se está visualizando un formulario ya existente
        if ($id) {
            // Obtenemos la solicitud de documentos
            $verificationForm = VerificationForm::findOrFail($id);

            // Comprobamos si el usuario actual puede visualizarlo
            $this->authorize('edit', $verificationForm);
        }

        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        // obtener Todas las plantillas de formularios
        $formTemplates = FormTemplate::all();

        // obtener plantillas del sistema
        $appFormTemplates = $formTemplates->whereNull('user_id')->groupBy('template_number');

        // obtener plantillas del creadas por el usuario
        $userFormTemplates = $formTemplates->where('user_id', $user->id)->groupBy('template_number');

        $mav = new ModelAndView('dashboard.verificationform.edit');

        return $mav->render(
            [
                'verificationForm'  => $verificationForm ?? null,
                'appFormTemplates'  => $appFormTemplates,             // plantillas de formulario del sistema
                'userFormTemplates' => $userFormTemplates,            // plantillas de formulario del usuario
                'characterTypes'    => config(                     // tipos de caracteres para validacion del formulario
                    'validations.form-validations.character-types'
                )
            ]
        );
    }

    /**
     * Guarda la verificacion de datos
     *
     * @return JsonResponse         La respuesta en formato json
     */
    public function save(?int $id = null): JsonResponse
    {
        // usuario de la app o usuario invitado
        $user = Auth::user() ?? Guest::user();

        // La lista de documentos requeridos
        $request = request()->input('formDataValidate');

        // valor para validar el "guardar y continuar " o "solo guardar"
        $saveAndContinue = request()->input('saveAndContinue');

        // El nombre y el comentario
        $name    = $request[0]['name'];
        $comment = $request[0]['comment'];

        if ($id) {
            // Obtiene la verificacion de datos
            $verificationForm = VerificationForm::findOrFail($id);

            // actualizar la verificacion de datos
            $verificationForm->update([
                'name'      => $name,
                'comment'   => $comment,
                'status'    => WorkspaceStatu::PENDIENTE
            ]);

            // eliminar la previa verificacion de datos
            $verificationForm->fieldsRow()->delete();

            // eliminar la plantilla si fue guardada
            // $verificationForm->user->formTemplate()->delete();

            // si el usuario selecciona guardar la platilla
            if ($saveAndContinue == 'true') {
                // setear el ultimo template_number del Formulario de plantillas para el formulario a guardar
                $request = FormTemplate::getClearFormDataWithTemplateNumber($request, $user->id);

                // Guardar los inputs y asignarlos a una nueva plantilla de formulario para el usuario
                $user->formTemplate()->createMany($request);
            }

            // setear el ultimo template_number del Formulario de datoss para el formulario a guardar
            $request = FormData::getClearFormDataWithTemplateNumber($request, $user->id);

            // guardar los inputs o fila de inputs pertenecientes al formulario
            $verificationForm->fieldsRow()->createMany($request);
        } else {
            // Crea una nueva verificacion de datos
            $verificationForm = $user->verificationForm()->create([
                'name'      => $name,
                'comment'   => $comment,
                'status'    => WorkspaceStatu::PENDIENTE
            ]);

            // setear el nuevo id creado
            $id = $verificationForm->id;

            // si el usuario selecciona guardar la platilla
            if ($saveAndContinue == 'true') {
                // setear el ultimo template_number del Formulario de plantillas para el formulario a guardar
                $request = FormTemplate::getClearFormDataWithTemplateNumber($request, $user->id);

                // Guardar los inputs y asignarlos a una nueva plantilla de formulario para el usuario
                $user->formTemplate()->createMany($request);
            }

            // setear el ultimo template_number del Formulario de datoss para el formulario a guardar
            $request = FormData::getClearFormDataWithTemplateNumber($request, $user->id);

            // guardar los inputs o fila de inputs pertenecientes al formulario
            $verificationForm->fieldsRow()->createMany($request);
        }

        return response()->json(['id' => $id]);
    }

    /**
     * Seleccionar usuarios a añadir a la verificacion de datos
     *
     * @param integer $id           El id de la verificacion de datos
     * @return string               la vista de seleccion de usuarios
     */
    public function selectSigners(int $id): string
    {
        // Obtenemos la solicitud de documentos
        $verificationForm = VerificationForm::findOrFail($id);

        // Verifica si el usuario actual está autorizado para definir las personas "firmantes"
        $this->authorize('signers', $verificationForm);

        $mav = new ModelAndView('dashboard.verificationform.select-signers');

        return $mav->render([
            'verificationForm' => $verificationForm,
        ]);
    }

    /**
     * Guarda y asigna los usuarios a la vaerificacion de datos correspondiente
     * fuera de un proceso del documento
     *
     * @param integer $id               El id de la verificacion de datos
     * @return JsonResponse             La respuesta en formato json
     */
    public function saveSigners(int $id): JsonResponse
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        // Obtenemos la verificacion de datos
        $verificationForm = VerificationForm::findOrFail($id);

        // Verifica si el usuario actual está autorizado a configurar la verificacion de datos
        $this->authorize('signers', $verificationForm);

        // Obtenemos los usuarios "firmantes" que deben certificar los datos
        $signers = request()->input('signers');

        // Obtenemos las direcciones de correo de los firmantes ya existentes
        $registerEmails = $verificationForm->signers->map(fn ($signer) => $signer->email)->toArray();

        // Obtenemos las direcciones de correo de los firmantes enviados
        $sendedEmails = array_map(fn ($signer) => $signer['email'], $signers);

        // Si la lista de firmantes enviados incluye un email ya registrado se elimina el firmante
        // con el objeto de que no se dupliquen esos firmantes
        foreach ($signers as $index => $signer) {
            if (in_array($signer['email'], $registerEmails)) {
                unset($signers[$index]);
            }
        }

        // Si se han quitado firmantes de la lista deben ser eliminados
        foreach ($verificationForm->signers as $signer) {
            if (!in_array($signer->email, $sendedEmails)) {
                $signer->delete();
            }
        }

        // Añade sólo los firmantes nuevos, es decir, que no estaban en la lista de firmantes
        // Para cada firmante se genera un token de acceso
        foreach ($signers as &$signer) {
            $signer['token'] = Str::random(64);
        }

        // Relaciona los firmantes con la verificacion de datos
        // y crea el proceso para cada firmante
        $verificationForm->signers()->createMany($signers)->each(
            function ($signer) {
                $signer->process()->create([]);
            }
        );

        // Volvemos a obtener la verificacion de datos actualizada ya con la lista de firmantes
        $verificationForm = VerificationForm::findOrFail($id);

        // envia la notificacion y comparte la verificacion
        $this->sendNotificationToUserAndSigner($verificationForm, $user);

        return response()->json([
            'message'   => Lang::get('Firmantes guardados con éxito')
        ]);
    }

    /**
     * Muestra la vista con el estado de la verificacion de datos
     *
     * @param integer $id                  El id de la verificacion
     * @return string                      la vista del estado de la verificacion
     */
    public function verificationStatus(int $id): string
    {
        // Obtenemos la verificacion de datos
        $verificationForm = VerificationForm::findOrFail($id);

        $this->authorize('status', $verificationForm);

        $mav = new ModelAndView('dashboard.verificationform.status');

        return $mav->render([
            'verificationForm' => $verificationForm
        ]);
    }

    /**
     * Envía una nueva verificaion de datos al usuario que no la ha atendido
     *
     * @param int $id El id de la verificacion de datos
     *
     * @return JsonResponse                     Un respuesta JSON
     */
    public function verificationSend(int $id): JsonResponse
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        // Obtenemos la verificacion de datos
        $verificationForm = VerificationForm::findOrFail($id);

        // Verifica si el usuario actual está autorizado para enviar la verificacion
        $this->authorize('status', $verificationForm);

        // Obtenemos los usuarios únicos que aún no han completado la solicitud
        $signers = $verificationForm->signers->where('verificationform_at', null);

        // Notificar a los firmantes que no sean el creador/autor
        // Se envía un email/SMS al firmante con un enlace a su espacio de usuario
        $signers->filter(fn ($signer) => !$signer->creator)->each(
            function ($signer) use ($user) {
                if ($signer->email) {
                    // Si se ha proporcionado el correo del firmante se notifica por email
                    EmailController::sendWorkSpaceAccessEmail($user, $signer);
                } elseif ($signer->phone) {
                    // Si no se ha proporcionado un correo, pero si su teléfono, se notifica por SMS
                    SmsController::sendWorkSpaceAccessSms($user, $signer);
                }
            }
        );

        // Se registra una nueva compartición de la verificacion
        $sharing = $verificationForm->sharings()->create([
            'signers' => json_encode([
                'signers' => $signers
                    ->filter(fn ($signer) => !$signer->creator)
                    ->map(fn ($signer) => $signer->id)
            ]),
            'type'      => 1,
            'sent_at'   => Carbon::now()
        ]);

        return response()->json($signers);
    }

    /**
     * Enviar notificacion a los usuarios que fueron asignados a la verificacion
     * de datos y al usuario propietario de la verificacion, ademas registra la
     * comparticion a los usuarios
     *
     * @param [type] $verificationForm              La verificacion de datos a enviar
     * @param [type] $user                          El usuario propietario
     * @return void
     */
    public function sendNotificationToUserAndSigner(VerificationForm $verificationForm, User $user)
    {
        // Notificar a los firmantes
        // Se envía un email/SMS a cada firmante con un enlace a su espacio de usuario
        $verificationForm->signers->filter(fn ($signer) => !$signer->creator)->each(
            function ($signer) use ($user) {
                if ($signer->email) {
                    // Si se ha proporcionado el correo del firmante se notifica por email
                    EmailController::sendWorkSpaceAccessEmail($user, $signer);
                } elseif ($signer->phone) {
                    // Si no se ha proporcionado un correo, pero si su teléfono, se notifica por SMS
                    SmsController::sendWorkSpaceAccessSms($user, $signer);
                }
            }
        );

        // Se envía un correo al creador de la compartición
        EmailController::confirmVerificationFormShared($verificationForm->user, $verificationForm);

        // Se registra una nueva compartición de la verificacion de datos
        $verificationForm->sharings()->create([
            'signers' => json_encode([
                'signers' => $verificationForm->signers
                    ->filter(fn ($signer) => !$signer->creator)
                    ->map(fn ($signer) => $signer->id)
            ]),
            'type'  => 1,
            'sent_at' => Carbon::now()
        ]);
    }

    /**
     * Obtiene el certificado en PDF del proceso de verificacion de datos
     *
     * @param int $id El id de la verificacion de datos
     *
     * @return Response                         Una respuesta HTTP - el certificado
     */
    public function verificationCertificate(int $id)
    {
        // Obtenemos la verificacion de datos
        $verificationForm = VerificationForm::findOrFail($id);

        // Verifica si el usuario actual está autorizado para generar y descargar el certificado
        $this->authorize('certificate', $verificationForm);

        // Genera un archivo PDF, cargando la vista correspondiente
        $pdf = PDF::loadView('dashboard.verificationform.partials.pdf.certificate', [
            'verificationForm'  => $verificationForm
        ]);

        // Genera la descarga del certificado
        return $pdf->download("certificate-verification-form-{$id}.pdf");
    }

    /**
     * Ver todo el historial para la verificacion d datos
     *
     * @param integer $id               El id de la verificacion
     * @return string                   La vista para el historial
     */
    public function verificationHistory(int $id): string
    {
        // Obtenemos la verificacion de datos
        $verificationForm = VerificationForm::findOrFail($id);

        $this->authorize('history', $verificationForm);

        $mav = new ModelAndView('dashboard.verificationform.history');

        return $mav->render([
            'verificationForm' => $verificationForm
        ]);
    }
}
