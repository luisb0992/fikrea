<?php

/**
 * WorkSpaceController
 *
 * Controlador del espacio de trabajo de los usuarios firmantes
 *
 * @author    javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers;

use App\Enums\ReasonCancel;
use App\Utils\FileUtils;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Lang;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Response;

/**
 * Modelos necesarios
 */

use App\Models\Document;
use App\Models\Signer;
use App\Models\Sign;
use App\Models\Validation;
use App\Models\Audio;
use App\Models\Video;
use App\Models\Capture;
use App\Models\Textbox;
use App\Models\Passport;
use App\Models\DocumentRequestFile;
use App\Models\DocumentRequest;
use App\Models\ReasonCancelRequest;
use App\Models\Notification;
use App\Models\WorkspaceComment;
use App\Models\FormData;
use App\Models\DocumentSharing;
use App\Models\DocumentRequestSharing;
use App\Models\DocumentSharingContact;
use App\Models\VerificationForm;
use App\Models\VerificationFormSharing;
use App\Models\FileSharing;
use App\Models\FileSharingContact;
use App\Models\Company;
use App\Models\CompanySharing;

/**
 * El proceso de firma de documento
 */

use App\Jobs\SignDocument;
use App\Jobs\TextboxsInDocument;

/**
 * Enumeraciones requeridas
 */

use App\Enums\ValidationType;
use App\Enums\WorkspaceStatu as EnumsWorkspaceStatu;
use App\Enums\SignerProcesType;

/**
 * Eventos lanzados
 */

use App\Events\SignerValidationDone;
use App\Events\SignerValidationCancel;
use App\Events\DocumentRequestDone;
use App\Events\DocumentRequestCancel;
use App\Events\DocumentRequestFileRenewed;
use App\Events\VerificationFormCancelEvent;
use App\Events\VerificationFormDoneEvent;

/**
 * Librerías Fikrea
 */

use Fikrea\ModelAndView;
use Fikrea\FaceRecognition;
use Fikrea\AppStorage;
use Fikrea\Environment;
use Fikrea\Mobile;

/**
 * Creación de archivos Zip al vuelo
 *
 * @link https://github.com/stechstudio/laravel-zipstream
 */

use Zip;
use STS\ZipStream\ZipStream;

/**
 * DomPDF
 */

use PDF;

/**
 * Trait UserDeviceTrait
 */

use App\Http\Controllers\Traits\UserDeviceTrait;
use App\Http\Controllers\Traits\HasVisits;
use App\Http\Controllers\Signature\Traits\DocumentProcess;
use Countries;

class WorkSpaceController extends Controller
{
    /**
     * Trait para el control de las visitas de los firmantes a las validaciones
     */
    use HasVisits;

    use UserDeviceTrait;

    /**
     * Trait para el procesamiento de los documentos
     */
    use DocumentProcess;

    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Muestra la vista principal del espacio de trabajo del usuario firmante
     *
     * @param string $token          El token de acceso
     * @param int    $sharing | null La compartición
     *
     * @return string
     */
    public function show(string $token, $sharing=null): string
    {
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        switch ($signer->signerProcess()) {
            case SignerProcesType::REQUEST_PROCESS:
                $sharing = $sharing ? DocumentRequestSharing::find($sharing) : null;
                break;
            case SignerProcesType::FORM_PROCESS:
                $sharing = $sharing ? VerificationFormSharing::find($sharing) : null;
                break;
            default:
                # VALIDATION_PROCESS
                $sharing = $sharing ? DocumentSharing::find($sharing) : null;
                break;
        }

        // Si el proceso de firma ha sido cancelado
        if ($signer->hasBeenCanceled()) {
            return $this->canceled();
        }

        // Marco todas las validaciones como inactivas, porque no estoy haciendo ninguna, solo estoy
        // en mi área de trabajo mirando lo que debo hacer
        $signer->markValidationsAsInactive();

        // Marcar que el firmante esta en el Home de su workspace
        $signer->activate();

        // si existen validaciones asignadas para un proceso de documentos
        if ($signer->validations()) {
            // Si el firmante ha finalizado sus validaciones no lo marco como activo
            if (!$signer->hasPendingValidations()) {
                // Marcar que el firmante esta en el Home de su workspace
                $signer->deactivate();
            }
        }

        $canCancelProcess = false;

        // Si es una solicitud las validaciones son nulas y la solicitud no
        // Marcamos la solicitud como inactiva
        if ((!$signer->validations() or !$signer->validations()->count()) && $signer->request()) {
            // Si es una solicitud y está en estado pendiente puedo cancelar este proceso
            if ($signer->process->isPending()) {
                $canCancelProcess = true;
            }
        } else {
            // Si hay al menos una validación en estado pendiente, puedo cancelar ese proceso
            if ($signer->validations() != null) {
                foreach ($signer->validations()->filter(
                    fn($validation) => $validation->validation
                        != \App\Enums\ValidationType::SCREEN_CAPTURE_VERIFICATION
                ) as $validation) {
                    if ($validation->pending()) {
                        $canCancelProcess = true;
                        break;
                    }
                }
            }

            // Si es un proceso de verificación de datos
            if ($signer->verificationForm()) {
                if ($signer->process->isPending()) {
                    $canCancelProcess = true;
                }
            }
        }

        // el creador de la solcitud (sea una verificación, solicitud o proceso del documento)
        // si es un proceso de validación
        // si es una solicitud de documentos
        // si es una verificacion de datos
        $creator = $signer->validations() ?
            $signer->document->user : ($signer->request() ? $signer->request()->user : $signer->verificationForm->user);

        // Traemos la relación entre el estado de la solicitud del documento
        //$documentR = $signer->request();

        // Opciones para cancelar o rechazar una solicitud
        $reasons = ReasonCancelRequest::all();

        // Comentarios
        $comments = WorkspaceComment::where('document_request_id', $signer->document_request_id)->with('signer')->get();

        // Renderiza la vista de la página home del espacio de trabajo de los usuarios
        $mav = new ModelAndView('workspace.home');

        // Si se llega desde un enlace con el id de la compartición se marca
        // como visitada en el momento exacto de la visita
        if ($sharing) {
            $sharing->visited();        // Se marca como visitada
        }

        return $mav->render(
            [
                'token'     => $token,                      // El token de acceso
                'creator'   => $creator,                    // El creador de la petición
                'signer'    => $signer,                     // El firmante
                'reasons'   => $reasons,                    // Motivos por el cual rechaza la solicitud
                'comments'  => $comments,                   // Comentarios de la solicitud
                'canCancelProcess' => $canCancelProcess,    // Si se puede cancelar este proceso o no
            ]
        );
    }

    /**
     * Guarda el motivo del rechazo de la solicitud
     *
     * @param Request $request La solicitud
     * @param string  $token   El token
     *
     * @return RedirectResponse                 Una redirección
     */
    public function cancelRequest(Request $request, string $token): RedirectResponse
    {
        $request->validate(
            [
                'reason_cancel_request_id' => 'required',
            ]
        );

        // Obtiene el firmanete por el token
        $signer = Signer::findByToken($token);

        // Obtenemos la validación
        $validation = Validation::find($request->validation);

        // Puede que la validación sea nula, cuando es un proceso de solicitud de documentos fuera del proceso de firma
        if (!$validation) {
            // Se marca el firmante como inactivo sobre un proceso de solicitud de documentos
            $signer->markAsInactive();

            // Si el id del motivo es mayor que 2
            if ($request->reason_cancel_request_id > \App\Enums\ReasonCancel::TECHNICAL_PROBLEMS) {
                // Modificamos el workspace_statu_id a cancelado
                $signer->process->update(['workspace_statu_id' => \App\Enums\WorkspaceStatu::CANCELADO]);
            }

            // Actualizamos el campo reason_cancel_request_id en la tabla validations
            $signer->process->update(['reason_cancel_request_id' => $request->reason_cancel_request_id]);
            $signer->reason_cancel_request_id = $request->reason_cancel_request_id;

            // Lanza los eventos relacionados con el rechazo de la solicitud de documentos
            event(new DocumentRequestCancel($signer->request(), $signer));
        } else {
            $validation->markAsInactive();   // La marcamos como Inactiva

            // Si el id del motivo es mayor que 2
            if ($request->reason_cancel_request_id > \App\Enums\ReasonCancel::TECHNICAL_PROBLEMS) {
                // Modificamos el workspace_statu_id a cancelado
                $validation->process->update(['workspace_statu_id' => \App\Enums\WorkspaceStatu::CANCELADO]);
            }

            // Actualizamos el campo reason_cancel_request_id en la tabla validations
            $validation->process->update(['reason_cancel_request_id' => $request->reason_cancel_request_id]);
            $validation->signer->reason_cancel_request_id = $request->reason_cancel_request_id;

            // Lanza los eventos relacionados con el rechazo de la validación
            event(new SignerValidationCancel($validation));
        }

        // Redirigir al WorkSpace
        return redirect()->back()
            ->with('message', Lang::get('La solicitud se ha rechazado con éxito'));
    }

    /**
     * Guarda el motivo del rechazo de la solicitud de tipo audio
     *
     * @param Request $request La solicitud
     * @param string  $token   El token
     *
     * @return RedirectResponse                 Una redirección
     */
    public function cancelRequestAudio(Request $request, string $token): RedirectResponse
    {
        $request->validate(
            [
                'reason_cancel_request_id' => 'required',
            ]
        );

        // Obtiene el firmanete por el token
        $signer = Signer::findByToken($token);

        // Obtenemos la validación
        $validation = Validation::findOrFail($request->validation);

        $validation->markAsInactive();   // La marcamos como Inactiva

        // Si el id del motivo es mayor que 2
        if ($request->reason_cancel_request_id > \App\Enums\ReasonCancel::TECHNICAL_PROBLEMS) {
            // Modificamos el workspace_statu_id a cancelado
            $validation->process->update(['workspace_statu_id' => \App\Enums\WorkspaceStatu::CANCELADO]);
        }

        // Actualizamos el campo reason_cancel_request_id en la tabla validations
        $validation->process->update(['reason_cancel_request_id' => $request->reason_cancel_request_id]);
        $validation->signer->reason_cancel_request_id = $request->reason_cancel_request_id;

        // Traemos el motivo por el cual es rechazada la solicitud
        $reason = ReasonCancelRequest::where('id', $request->reason_cancel_request_id)->first();

        // Lanza los eventos relacionados con el rechazo de la validación
        event(new SignerValidationCancel($validation));

        // Redirigir al WorkSpace
        return redirect()->back()
            ->with('message', Lang::get('La solicitud se ha rechazado con éxito'));
    }

    /**
     * Guarda el motivo del rechazo de la solicitud de tipo video
     *
     * @param Request $request La solicitud
     * @param string  $token   El token
     *
     * @return RedirectResponse                 Una redirección
     */
    public function cancelRequestVideo(Request $request, string $token): RedirectResponse
    {
        $request->validate(
            [
                'reason_cancel_request_id' => 'required',
            ]
        );

        // Obtiene el firmanete por el token
        $signer = Signer::findByToken($token);

        // Obtenemos la validación
        $validation = Validation::findOrFail($request->validation);

        $validation->markAsInactive();   // La marcamos como Inactiva

        // Si el id del motivo es mayor que 2
        if ($request->reason_cancel_request_id > \App\Enums\ReasonCancel::TECHNICAL_PROBLEMS) {
            // Modificamos el workspace_statu_id a cancelado
            $validation->process->update(['workspace_statu_id' => \App\Enums\WorkspaceStatu::CANCELADO]);
        }

        // Actualizamos el campo reason_cancel_request_id en la tabla validations
        $validation->process->update(['reason_cancel_request_id' => $request->reason_cancel_request_id]);
        $validation->signer->reason_cancel_request_id = $request->reason_cancel_request_id;

        // Traemos el motivo por el cual es rechazada la solicitud
        $reason = ReasonCancelRequest::where('id', $request->reason_cancel_request_id)->first();

        // Lanza los eventos relacionados con el rechazo de la validación
        event(new SignerValidationCancel($validation));

        // Redirigir al WorkSpace
        return redirect()->back()
            ->with('message', Lang::get('La solicitud se ha rechazado con éxito'));
    }

    /**
     * Guarda el motivo del rechazo de la solicitud de tipo pasaporte
     *
     * @param Request $request La solicitud
     * @param string  $token   El token
     *
     * @return RedirectResponse                 Una redirección
     */
    public function cancelRequestPassport(Request $request, string $token): RedirectResponse
    {
        $request->validate(
            [
                'reason_cancel_request_id' => 'required',
            ]
        );

        // Obtiene el firmanete por el token
        $signer = Signer::findByToken($token);

        // Obtenemos la validación
        $validation = Validation::findOrFail($request->validation);

        $validation->markAsInactive();   // La marcamos como Inactiva

        // Si el id del motivo es mayor que 2
        if ($request->reason_cancel_request_id > \App\Enums\ReasonCancel::TECHNICAL_PROBLEMS) {
            // Modificamos el workspace_statu_id a cancelado
            $validation->process->update(['workspace_statu_id' => \App\Enums\WorkspaceStatu::CANCELADO]);
        }

        // Actualizamos el campo reason_cancel_request_id en la tabla validations
        $validation->process->update(['reason_cancel_request_id' => $request->reason_cancel_request_id]);
        $validation->signer->reason_cancel_request_id = $request->reason_cancel_request_id;

        // Traemos el motivo por el cual es rechazada la solicitud
        $reason = ReasonCancelRequest::where('id', $request->reason_cancel_request_id)->first();

        // Lanza los eventos relacionados con el rechazo de la validación
        event(new SignerValidationCancel($validation));

        // Redirigir al WorkSpace
        return redirect()->back()
            ->with('message', Lang::get('La solicitud se ha rechazado con éxito'));
    }

    /**
     * Guarda el motivo del rechazo de la solicitud de tipo firma
     *
     * @param Request $request La solicitud
     * @param string  $token   El token
     *
     * @return RedirectResponse                 Una redirección
     */
    public function cancelRequestSignature(Request $request, string $token): RedirectResponse
    {
        $request->validate(
            [
                'reason_cancel_request_id' => 'required',
            ]
        );

        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        // Traemos la relación entre el estado de la solicitud y la validación del documento
        $validation = Validation::findOrFail($request->validation);

        $validation->markAsInactive();   // La marcamos como Inactiva

        // Si el id del motivo es mayor que 2
        if ($request->reason_cancel_request_id > \App\Enums\ReasonCancel::TECHNICAL_PROBLEMS) {
            // Modificamos el workspace_statu_id a cancelado
            $validation->process->update(['workspace_statu_id' => \App\Enums\WorkspaceStatu::CANCELADO]);
        }

        // Actualizamos el campo reason_cancel_request_id en la tabla validations
        $validation->process->update(['reason_cancel_request_id' => $request->reason_cancel_request_id]);
        $validation->signer->reason_cancel_request_id = $request->reason_cancel_request_id;

        // Traemos el motivo por el cual es rechazada la solicitud
        $reason = ReasonCancelRequest::where('id', $request->reason_cancel_request_id)->first();

        // Lanza los eventos relacionados con el rechazo de la validación
        event(new SignerValidationCancel($validation));

        // Redirigir al WorkSpace
        return redirect()->back()
            ->with('message', Lang::get('Su solicitud de rechazo del proceso se ha ejecutado con éxito'));
    }

    /**
     * Guarda el motivo del rechazo del formulario de datos
     *
     * @param Request $request La solicitud
     * @param string  $token   El token
     *
     * @return RedirectResponse                 Redireccion a una pagina anterior
     */
    public function cancelRequestFormdata(Request $request, string $token): RedirectResponse
    {
        // Validar el requerimiento del campo
        $request->validate(['reason_cancel_request_id' => 'required']);

        // Obtiene el firmanete por el token
        $signer = Signer::findByToken($token);

        // Obtenemos la validación
        $validation = Validation::findOrFail($request->validation);

        $validation->markAsInactive();   // La marcamos como Inactiva

        // Si el id del motivo es mayor que 2
        if ($request->reason_cancel_request_id > \App\Enums\ReasonCancel::TECHNICAL_PROBLEMS) {
            // Modificamos el workspace_statu_id a cancelado
            $validation->process->update(['workspace_statu_id' => \App\Enums\WorkspaceStatu::CANCELADO]);
        }

        // Actualizamos el campo reason_cancel_request_id en la tabla validations
        $validation->process->update(['reason_cancel_request_id' => $request->reason_cancel_request_id]);

        // Traemos el motivo por el cual es rechazada la solicitud
        $reason = ReasonCancelRequest::where('id', $request->reason_cancel_request_id)->first();

        $title = Lang::get(
            'La validación de :validation ha sido rechazada por :name :lastname
            <a href="mailto::email">:email</a>',
            [
                'validation' => (string)ValidationType::fromValue($validation->validation),
                'name' => $signer->name,
                'lastname' => $signer->lastname,
                'email' => $signer->email,
            ]
        );

        // Creamos el mensaje de por que se rechaza la solicitud
        Notification::create(
            [
                'user_id' => $validation->document->user_id,
                // El usuario que solicito el documento
                'title' => $title,
                // el titulo del mensaje
                'message' => $reason->reason,
                // El motivo
                'url' => route($request->route()->getName(), ['token' => $token]),
                // La ruta de la solicitud
                'type' => \App\Enums\NotificationTypeEnum::CANCELLED,
                'reason_cancel_request_id' => $request->reason_cancel_request_id
            ]
        );

        // Redirigir al WorkSpace
        return redirect()->back()->with('message', Lang::get('La solicitud se ha rechazado con éxito'));
    }

    /**
     * Obtiene, para su descarga, el documento original dado
     *
     * @param string $token El token de acceso al documento
     *
     * @return StreamedResponse  Un stream con el documento a descargar
     */
    public function download(string $token): StreamedResponse
    {
        // Obtenemos el documento
        $document = Document::findByToken($token);

        return Storage::disk(env('APP_STORAGE'))->download($document->original_path, $document->name);
    }

    /**
     * Obtiene, para su descarga, el documento firmado
     *
     * @param string $token El token de acceso al documento
     *
     * @return StreamedResponse   Un stream con el documento a descargar
     */
    public function downloadSigned(string $token): StreamedResponse
    {
        // Obtenemos el documento
        $document = Document::findByToken($token);

        // El nombre del documento firmado, es el original con la extensión pdf
        // El nombre del archivo pdf firmado
        $signedFile = implode('.', [pathinfo($document->name, PATHINFO_FILENAME), 'pdf']);

        return Storage::disk(env('APP_STORAGE'))->download($document->signed_path, $signedFile);
    }

    /**
     * Obtiene, para su descarga, los archivos aportados por el firmante en el proceso de validación
     * o solicitud de documentos
     *
     * @param string $token El token de acceso al documento
     *
     * @return ZipStream                        Un stream con el documento ZIP
     */
    public function downloadFiles(string $token): ZipStream
    {
        // Obtenemos el documento
        $signer = Signer::findByToken($token);

        // El nombre del archivo zip
        if ($signer->document) {
            // En un proceso de validación de un documento
            $zipFile = implode('.', [pathinfo($signer->document->name, PATHINFO_FILENAME), 'zip']);
        } elseif ($signer->request()) {
            // En una solicitud de documentos
            $zipFile = implode('.', [pathinfo($signer->request()->name, PATHINFO_FILENAME), 'zip']);
        }

        // Crea un nuevo archivo zip
        $zip = Zip::create($zipFile);

        // Añade los archivos de audio si los hay
        $audioFolder = Str::lower(config('validations.audio.folder'));

        foreach ($signer->audios as $audio) {
            $zip->add(AppStorage::path("{$audioFolder}/{$audio->path}"), "audios/{$audio->path}");
        }

        // Añade los archivos de video si los hay
        $videoFolder = Str::lower(config('validations.video.folder'));

        foreach ($signer->videos as $video) {
            $zip->add(AppStorage::path("{$videoFolder}/{$video->path}"), "videos/{$video->path}");
        }

        // Añade los archivos de captura de pantalla si los hay
        $captureFolder = Str::lower(config('validations.capture.folder'));

        foreach ($signer->captures as $capture) {
            $zip->add(AppStorage::path("{$captureFolder}/{$capture->path}"), "captures/{$capture->path}");
        }

        // Añade los documentos identificativos si los hay
        $passportFolder = Str::lower(config('validations.identification-document.folder'));

        foreach ($signer->passports as $passport) {
            // La imagen frontal del usuario
            if ($passport->user_image) {
                $zip->add(
                    AppStorage::path("{$passportFolder}/{$passport->user_image}"),
                    "documents/user-{$passport->user_image}"
                );
            }
            // El anverso del documento
            if ($passport->front_path) {
                $zip->add(
                    AppStorage::path("{$passportFolder}/{$passport->front_path}"),
                    "documents/front-{$passport->front_path}"
                );
            }
            // El reverso del documento
            if ($passport->back_path) {
                $zip->add(
                    AppStorage::path("{$passportFolder}/{$passport->back_path}"),
                    "documents/back-{$passport->back_path}"
                );
            }
        }

        // Los archivos o documentos aportados por el usuario en la solicitud de documentos
        if ($signer->request()) {
            $signer->request()->files->each(
                function ($file) use ($zip) {
                    $zip->add(
                        AppStorage::path($file->path),
                        "{$file->id}-{$file->name}"
                    );
                }
            );
        }

        // Genera la descarga del archivo zip
        return $zip;
    }

    /**
     * Obtiene el informe acredativo de que el firmante ha participado en el proceso
     * de firma y validación de un documento
     *
     * @param string $token El token del firmante
     *
     * @return Response                         Una respuesta
     */
    public function signerReport(string $token): Response
    {
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        // Obtener un informe de participación en el proceso de validación
        $pdf = PDF::loadView(
            'workspace.pdf.certificate',
            [
                'signer' => $signer,
            ]
        );

        // Descarga el informe
        return $pdf->download("report-{$signer->document->guid}.pdf");
    }

    /**
     * Muestra la vista para validar un documento mediante edición de cajas de textos
     *
     * @param string $token El token de acceso
     *
     * @return string                           Una vista
     */
    public function textboxs(string $token): string
    {
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        // Obtengo la validación de edición de cajas de texto ponerla inactiva
        $validation = $signer->validations()->filter(
            fn ($validation) => $validation->validation == ValidationType::TEXT_BOX_VERIFICATION
        )->first();

        // Si el firmante no debe realizar este tipo de validación se impide
        if (!$validation) {
            abort('403');
        }

        $validation->markAsInactive();

        // Carga la vista con las miniaturas de cada página de las que consta el documento
        $mav = new ModelAndView('workspace.textboxs');

        // Registra una visita del firmante a la vista de la página indicada
        $this->registerVisit($signer, $mav);

        // traducciones necesarias en vue js
        $langs = [
            Lang::get('Iniciales'),
            Lang::get('Nombre completo'),
            Lang::get('Identificación'),
            Lang::get('Cualquier texto'),
            Lang::get('Verificación'),
            Lang::get('Opciones'),
            Lang::get('Longitud máxima'),
        ];

        return $mav->render(
            [
                'token' => $token,                  // El token de acceso
                'signer' => $signer,                 // El firmante
                'document' => $signer->document,       // El documento
                'langs' => $langs,                  // Textos traducidos
            ]
        );
    }

    /**
     * Muestra la vista para validar un documento mediante firma manuscrita digital
     *
     * @param string $token El token de acceso
     *
     * @return string                           Una vista
     */
    public function signature(string $token): string
    {
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        // Obtengo la validación de firma manuscrita para ponerla inactica
        $validation = $signer->validations()->filter(
            fn ($validation) => $validation->validation == ValidationType::HAND_WRITTEN_SIGNATURE
        )->first();

        // Si el firmante no debe realizar este tipo de validación se impide
        if (!$validation) {
            abort('403');
        }

        $validation->markAsInactive();

        // Carga la vista con las ministuras de cada página de las que consta el documento
        $mav = new ModelAndView('workspace.document');

        // Obtiene las páginas del documento que aún no han sido firmadas por el usuario firmante
        $pages = $signer->signs->filter(
            function ($sign) {
                return !$sign->signed;
            }
        )->map(
            function ($sign) {
                return $sign->page;
            }
        )->unique()->toArray();

        return $mav->render(
            [
                'token' => $token,                  // El token de acceso
                'signer' => $signer,                 // El firmante
                'pages' => implode(',', $pages),    // Las páginas del documento que faltan de ser firmadas
                // separadas por comas
            ]
        );
    }

    /**
     * Muestra la vista de firma de una página concreta de un documento identificado por el token del firmante
     *
     * @param string $token El token del usuario firmante
     * @param int    $page  El número de página
     *
     * @return string                           Una vista
     */
    public function page(string $token, int $page): string
    {
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        // Obtengo la validación de firma manuscrita para activarla
        $validation = $signer->validations()->filter(
            fn ($validation) => $validation->validation == ValidationType::HAND_WRITTEN_SIGNATURE
        )->first();

        // Si el firmante no debe realizar este tipo de validación se impide
        if (!$validation || $validation->done()) {
            abort('403');
        }

        $validation->markAsActive();

        // Carga la vista con la página del documento
        $mav = new ModelAndView('workspace.page');

        // Registra una visita del firmante a la vista de la página indicada
        $this->registerVisit($signer, $mav);

        return $mav->render(
            [
                'token' => $token,                  // El token de acceso
                'signer' => $signer,                 // El firmante
                'page' => $page,                   // El número de la página del documento
            ]
        );
    }

    /**
     * Guarda las firmas del documento
     *
     * @param string $token El token del firmante para el documento dado
     *
     * @return JsonResponse                     Un respuesta JSON
     */
    public function saveSignedDocument(string $token): JsonResponse
    {
        // Obtenemos el usuario firmante por el token
        $signer = Signer::findByToken($token);

        // Obtenemos la ip y el agente de usuario
        $ip = request()->ip();
        $user_agent = request()->server('HTTP_USER_AGENT');

        // Obtiene las firmas
        $signs = collect(request()->input('signs'));

        // Obtenemos la posición del firmante datum WGS84
        $position = request()->input('position');

        // Actualizamos la visita realizada por el usuario
        $visit = request()->input('visit');
        $this->updateVisit($visit, $position);

        // Nos aseguramos que se tomarán sólo las firmas efectuadas que corresponden al firmante
        $signerSigns = $signs->filter(
            function ($sign) use ($signer) {
                return $sign['signer']['id'] == $signer->id;
            }
        );

        // Guardamos cada una de las firmas
        $signerSigns->each(
            function ($sign) use ($ip, $user_agent, $position) {
                // Obtenemos los datos de la firma previamente guardada
                $savedSign = Sign::findByCode($sign['id']);

                // Fijamos los datos de conexión
                $savedSign->ip = $ip;
                $savedSign->user_agent = $user_agent;

                // Si se ha incluído una firma
                if ($sign['sign']) {
                    // Fijamos los datos de geolocalizacióm
                    $savedSign->latitude = $position['latitude'];
                    $savedSign->longitude = $position['longitude'];
                    $savedSign->device = $this->getDevice();

                    // Fijamos la firma y la fecha en la que se ha realizado
                    $savedSign->sign = $sign['sign'];
                    $savedSign->signDate = new \DateTime;

                    // Marcamos que la firma ha sido efectuada
                    $savedSign->signed = true;
                } else {
                    // Si no se ha incluido la firma, se marca como "no firmada"
                    // y se dejan nulos los campos de conexión, localización
                    $savedSign->latitude = $savedSign->longitude = null;
                    $savedSign->ip = $savedSign->user_agent = null;

                    $savedSign->signDate = null;

                    $savedSign->signed = false;
                }

                // Guardamos la firma
                $savedSign->save();
            }
        );

        // Marcamos la validación como realizada
        $validation = Validation::where('user', $signer->id)
            ->where('validation', ValidationType::HAND_WRITTEN_SIGNATURE)
            ->first()
            ->validated();      // Inactivamos el proceso y demás

        // Obtiene y guarda las capturas de pantalla si se han realizado
        $captures = request()->input('captures');

        if ($captures) {
            // Guardo todas las capturas de pantallas que se han realizado
            foreach ($captures as $key => $capture) {
                // Asocia la captura de pantalla al usuatio, al firmante y al documento
                $capture['user_id'] = $signer->document->user->id;
                $capture['signer_id'] = $signer->id;
                $capture['document_id'] = $signer->document->id;

                // Información adicional para la verificación la captura de pantalla
                $capture['ip'] = $ip;
                $capture['user_agent'] = $user_agent;
                $capture['latitude'] = $position['latitude'];
                $capture['longitude'] = $position['longitude'];
                $capture['device'] = $this->getDevice();

                // Obtenemos el nombre y la ruta del archivo de captura de pantalla en la carpeta de
                // capturas de pantalla
                $captureFolder = config('validations.capture.folder');
                $captureFile = implode('.', [Str::random(64), config('validations.capture.file.extension')]);
                $capturePath = "{$captureFolder}/{$captureFile}";

                // Obtenemos los datos del archivo de captura de pantalla
                $captureDecoded = base64_decode(preg_replace('#data:[^;]+/[^;]+;base64,#', '', $capture['file']));

                // Guarda el archivo de captura de pantalla
                Storage::disk(env('APP_STORAGE'))->put($capturePath, $captureDecoded);

                $capture['path'] = $captureFile;

                // Guarda la captura de pantalla en la base de datos
                Capture::create($capture);
            }
            // Marcamos la validación como realizada
            $validationCapture = Validation::where('user', $signer->id)
                ->where('validation', ValidationType::SCREEN_CAPTURE_VERIFICATION)
                ->first();

            if ($validationCapture) {
                $validationCapture->validated();       // La marcamos como inactiva y demás desde acá
            }
        }

        // Lanza los eventos relacionados con la realización de la validación
        event(new SignerValidationDone($validation));

        // Pone en cola la creación del documento firmado ya que es un proceso que puede demorar tiempo
        // @see https://laravel.com/docs/8.x/queues#delayed-dispatching
        //
        // Para la ejecución de la cola en producción, el siguiente comando debe haber sido lanzado en segundo plano
        //
        // artisan queue:work
        //
        // @see https://laravel.com/docs/8.x/queues#running-the-queue-worker
        //
        SignDocument::dispatch($validation->document);

        // Devuelve una respuesta JSON con el firmante
        return response()->json($signer);
    }

    /**
     * Guarda las cajas de texto del documento
     *
     * @param string $token El token del firmante para el documento dado
     *
     * @return JsonResponse                     Un respuesta JSON
     */
    public function saveTextboxsDocument(string $token): JsonResponse
    {
        // Obtenemos el usuario firmante por el token
        $signer = Signer::findByToken($token);

        // Obtenemos la ip y el agente de usuario
        $ip = request()->ip();
        $user_agent = request()->server('HTTP_USER_AGENT');

        // Obtiene las cajas de texto
        $textboxs = collect(request()->input('textboxs'));

        // Obtenemos la posición del firmante datum WGS84
        $position = request()->input('position');

        // Actualizamos la visita realizada por el usuario
        $visit = request()->input('visit');
        $this->updateVisit($visit, $position);

        // Nos aseguramos que se tomarán sólo las firmas efectuadas que corresponden al firmante
        $signerBoxs = $textboxs->filter(
            function ($box) use ($signer) {
                return $box['signer']['id'] == $signer->id;
            }
        );

        // Guardamos cada una de las firmas
        $signerBoxs->each(
            function ($box) use ($ip, $user_agent, $position) {
                // Obtenemos los datos de la firma previamente guardada
                $savedBox = Textbox::findByCode($box['id']);

                // Fijamos los datos de conexión
                $savedBox->ip = $ip;
                $savedBox->user_agent = $user_agent;

                // Si se ha incluído una firma
                if ($box['text']) {
                    // Fijamos los datos de geolocalizacióm
                    $savedBox->latitude = $position['latitude'];
                    $savedBox->longitude = $position['longitude'];
                    $savedBox->device = $this->getDevice();

                    // Fijamos la firma y la fecha en la que se ha realizado
                    $savedBox->text = $box['text'];
                    $savedBox->signDate = new \DateTime;

                    // Marcamos que la caja ha sido completada
                    $savedBox->signed = true;
                } else {
                    // Si no se ha incluido el texto, se marca como "no completada"
                    // y se dejan nulos los campos de conexión, localización
                    $savedBox->latitude = $savedBox->longitude = null;
                    $savedBox->ip = $savedBox->user_agent = null;
                    $savedBox->signDate = null;
                    $savedBox->signed = false;
                }

                // Guardamos la caja de texto
                $savedBox->save();
            }
        );

        // Marcamos la validación como realizada
        $validation = Validation::where('user', $signer->id)
            ->where('validation', ValidationType::TEXT_BOX_VERIFICATION)
            ->first()
            ->validated();      // Inactivamos el proceso y demás

        // Obtiene y guarda las capturas de pantalla si se han realizado
        $captures = request()->input('captures');

        if ($captures) {
            // Guardo todas las capturas de pantallas que se han realizado
            foreach ($captures as $key => $capture) {
                // Asocia la captura de pantalla al usuatio, al firmante y al documento
                $capture['user_id'] = $signer->document->user->id;
                $capture['signer_id'] = $signer->id;
                $capture['document_id'] = $signer->document->id;

                // Información adicional para la verificación la captura de pantalla
                $capture['ip'] = $ip;
                $capture['user_agent'] = $user_agent;
                $capture['latitude'] = $position['latitude'];
                $capture['longitude'] = $position['longitude'];
                $capture['device'] = $this->getDevice();

                // Obtenemos el nombre y la ruta del archivo de captura de pantalla en la carpeta
                // de capturas de pantalla
                $captureFolder = config('validations.capture.folder');
                $captureFile = implode('.', [Str::random(64), config('validations.capture.file.extension')]);
                $capturePath = "{$captureFolder}/{$captureFile}";

                // Obtenemos los datos del archivo de captura de pantalla
                $captureDecoded = base64_decode(preg_replace('#data:[^;]+/[^;]+;base64,#', '', $capture['file']));

                // Guarda el archivo de captura de pantalla
                Storage::disk(env('APP_STORAGE'))->put($capturePath, $captureDecoded);

                $capture['path'] = $captureFile;

                // Guarda la captura de pantalla en la base de datos
                Capture::create($capture);
            }
            // Marcamos la validación como realizada
            $validationCapture = Validation::where('user', $signer->id)
                ->where('validation', ValidationType::SCREEN_CAPTURE_VERIFICATION)
                ->first();

            if ($validationCapture) {
                $validationCapture->validated();       // La marcamos como inactiva y demás desde acá
            }
        }

        // Lanza los eventos relacionados con la realización de la validación
        event(new SignerValidationDone($validation));

        // Pone en cola la creación del documento con las cajas de texto
        // ya que es un proceso que puede demorar tiempo
        // @see https://laravel.com/docs/8.x/queues#delayed-dispatching
        //
        // Para la ejecución de la cola en producción, el siguiente comando debe haber sido lanzado en segundo plano
        //
        // artisan queue:work
        //
        // @see https://laravel.com/docs/8.x/queues#running-the-queue-worker
        //
        TextboxsInDocument::dispatch($validation->document);

        // Devuelve una respuesta JSON con el firmante
        return response()->json(['code' => 1, 'signer' => $signer]);
    }

    /**
     * Muestra la vista para validar un documento mediante una grabación de audio
     *
     * @param string $token El token de acceso
     *
     * @return string                           Una vista
     */
    public function audio(string $token): string
    {
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        // Si la validación ha sido ya realizada, existen ya archivos de audio
        // y no permite volver que el proceso vuelva a ser realizado
        if ($signer->audios->isNotEmpty()) {
            abort('404');
        }

        // Obtenemos la validación de audio del firmante
        $validation = $signer->validations()->filter(
            fn ($validation) => $validation->validation == ValidationType::AUDIO_FILE_VERIFICATION
        )->first();

        // Si el firmante no debe realizar este tipo de validación se impide
        if (!$validation) {
            abort('403');
        }

        $validation->markAsActive();

        // Obtiene el texto que sirve de referencia para la locución que debe realizar el usuario
        // realizando las sustituciones oportunas
        $audioText = Str::of($signer->document->user->config->audio->text)
            ->replace(':date', (new \DateTime)->format('d/m/Y'))
            ->replace(':name', $signer->name ?? ':name')
            ->replace(':lastname', $signer->lastname ?? ':lastname')
            ->replace(':doc', $signer->dni ?? ':doc');

        // Obtiene el ejemplo de audio que puede utilizarse como referencia en esta validación
        $audioSample = $signer->document->user->config->audio->sample;

        // Renderiza la vista de la página para realizar la grabación de audio
        $mav = new ModelAndView('workspace.audio');

        // Registra una visita del firmante a la vista de la página indicada
        $this->registerVisit($signer, $mav);

        // Marcamos la validación como activa


        return $mav->render(
            [
                'token' => $token,          // El token de acceso
                'signer' => $signer,         // El firmante
                'audioText' => $audioText,      // El texto de referencia para la grabación
                'audioSample' => $audioSample,    // El audio de ejemplo que sirve de guión a la grabación
            ]
        );
    }

    /**
     * Guarda las grabaciones de audio para validar un documento
     *
     * @param string $token El token de acceso
     *
     * @return JsonResponse                     Un respuesta JSON
     */
    public function saveAudio(string $token): JsonResponse
    {
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        // Obtenemos la ip y el agente de usuario
        $ip = request()->ip();
        $user_agent = request()->server('HTTP_USER_AGENT');

        // Obtenemos la posición del firmante datum WGS84
        $position = request()->input('position');

        // Actualizamos la visita realizada por el usuario
        $visit = request()->input('visit');
        $this->updateVisit($visit, $position);

        // Obtenemos la lista de grabaciones de audio enviadas
        $audios = collect(request()->input('audios'));

        // Guardamos cada una de las grabaciones de audio efectuadas
        $audios->each(
            function ($audio) use ($signer, $ip, $user_agent, $position) {
                // Asocia la grabación de audio al usuario, al firmante y al documento
                $audio['user_id'] = $signer->document->user->id;
                $audio['signer_id'] = $signer->id;
                $audio['document_id'] = $signer->document->id;

                // Información adicional para la verificación del documento
                $audio['ip'] = $ip;
                $audio['user_agent'] = $user_agent;
                $audio['latitude'] = $position['latitude'];
                $audio['longitude'] = $position['longitude'];
                $audio['device'] = $this->getDevice();       // Dispositivo del firmante

                // Obtenemos el nombre y la ruta del archivo de audio en la carpeta de audios
                $audioFolder = config('validations.audio.folder');
                $audioFile = implode('.', [Str::random(64), config('validations.audio.file.extension')]);
                $audioPath = "{$audioFolder}/{$audioFile}";

                // Obtenemos los datos del archivo de audio
                $audioDecoded = base64_decode(preg_replace('#data:[^;]+/[^;]+;base64,#', '', $audio['file']));

                // Guarda el archivo de audio
                Storage::disk(env('APP_STORAGE'))->put($audioPath, $audioDecoded);

                $audio['path'] = $audioFile;

                // Guarda el audio en la base de datos
                Audio::create($audio);
            }
        );

        // Se marca la validación como realizada
        $validation = Validation::where('user', $signer->id)
            ->where('validation', ValidationType::AUDIO_FILE_VERIFICATION)
            ->first()
            ->validated();          // La marcamos como inactiva y demás desde acá

        // Lanza los eventos relacionados con la realización de la validación
        event(new SignerValidationDone($validation));

        return response()->json($validation);
    }

    /**
     * Muestra la vista para validar un documento mendiante una grabación de video
     *
     * @param string $token El token de acceso
     *
     * @return string                           Una vista
     */
    public function video(string $token): string
    {
        // Obtiene el firmanete por el token
        $signer = Signer::findByToken($token);

        // Si la validación ha sido ya realizada, existen ya archivos de video
        // y no permite volver que el proceso vuelva a ser realizado
        if ($signer->videos->isNotEmpty()) {
            abort('404');
        }

        // Obtenemos la validación de audio del firmante
        $validation = $signer->validations()->filter(
            fn ($validation) => $validation->validation == ValidationType::VIDEO_FILE_VERIFICATION
        )->first();

        // Si el firmante no debe realizar este tipo de validación se impide
        if (!$validation) {
            abort('403');
        }

        $validation->markAsActive();

        // Obtiene el texto que sirve de referencia para la locución que debe realizar el usuario
        // realizando las sustituciones oportunas
        $videoText = Str::of($signer->document->user->config->video->text)
            ->replace(':date', (new \DateTime)->format('d/m/Y'))
            ->replace(':name', $signer->name ?? ':name')
            ->replace(':lastname', $signer->lastname ?? ':lastname')
            ->replace(':doc', $signer->dni ?? ':doc');

        // Obtiene el ejemplo de video que puede utilizarse como referencia en esta validación
        $videoSample = $signer->document->user->config->video->sample;

        // Muestra la vista de la página para realizar la grabación de video
        $mav = new ModelAndView('workspace.video');

        // Registra una visita del firmante a la vista de la página indicada
        $this->registerVisit($signer, $mav);

        return $mav->render(
            [
                'token' => $token,          // El token de acceso
                'signer' => $signer,         // El firmante
                'videoText' => $videoText,      // El texto de referencia para la grabación
                'videoSample' => $videoSample,    // El video de ejemplo que sirve de guión a la grabación
            ]
        );
    }

    /**
     * Guarda las grabaciones de video para validar un documento
     *
     * @param Request $request La solicitud
     * @param string  $token   El token de acceso
     *
     * @return JsonResponse                     Un respuesta JSON
     */
    public function saveVideo(Request $request, string $token): JsonResponse
    {
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        // Obtenemos la ip y el agente de usuario
        $ip = request()->ip();
        $user_agent = $request->server('HTTP_USER_AGENT');

        // Obtenemos la posición del firmante datum WGS84
        $position = $request->input('position');

        // Actualizamos la visita realizada por el usuario
        $visit = request()->input('visit');
        $this->updateVisit($visit, $position);

        // Obtenemos la lista de grabaciones de video enviadas
        $videos = collect(request()->input('videos'));

        // Guardamos cada una de las grabaciones de video efectuadas
        $videos->each(
            function ($video) use ($signer, $ip, $user_agent, $position) {
                // Asocia la grabación de video al usuatio, al firmante y al documento
                $video['user_id'] = $signer->document->user->id;
                $video['signer_id'] = $signer->id;
                $video['document_id'] = $signer->document->id;

                // Información adicional para la verificación del documento
                $video['ip'] = $ip;
                $video['user_agent'] = $user_agent;
                $video['latitude'] = $position['latitude'];
                $video['longitude'] = $position['longitude'];
                $video['device'] = $this->getDevice();

                // Obtenemos el nombre y la ruta del archivo de video en la carpeta de videos
                $videoFolder = config('validations.video.folder');
                $videoFile = implode('.', [Str::random(64), config('validations.video.file.extension')]);
                $videoPath = "{$videoFolder}/{$videoFile}";

                // Obtenemos los datos del archivo de video
                $videoDecoded = base64_decode(preg_replace('#data:[^;]+/[^;]+;base64,#', '', $video['file']));

                // Guarda el archivo de video
                Storage::disk(env('APP_STORAGE'))->put($videoPath, $videoDecoded);

                $video['path'] = $videoFile;

                // Guarda el video en la base de datos
                Video::create($video);
            }
        );

        // Se marca la validación como realizada
        $validation = Validation::where('user', $signer->id)
            ->where('validation', ValidationType::VIDEO_FILE_VERIFICATION)
            ->first()
            ->validated();               // La marcamos como inactiva y demás desde acá

        // Lanza los eventos relacionados con la realización de la validación
        event(new SignerValidationDone($validation));

        return response()->json($validation);
    }

    /**
     * Muestra la vista para la validación de un documento adjuntando un documento identificativo
     * como el carné o cédula de identidad, pasaporte, carné de conducir
     *
     * @param string $token El token de acceso
     *
     * @return string                           Una vista
     */
    public function passport(string $token): string
    {
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        // Si la validación ha sido ya realizada, existen ya archivos de audio
        // y no permite volver que el proceso vuelva a ser realizado
        if ($signer->passports->isNotEmpty()) {
            abort('404');
        }

        // Obtenemos la validación de documento identificativo del firmante
        $validation = $signer->validations()->filter(
            fn ($validation) => $validation->validation == ValidationType::PASSPORT_VERIFICATION
        )->first();

        // Si el firmante no debe realizar este tipo de validación se impide
        if (!$validation) {
            abort('403');
        }

        // si la puede realizar, marcamos la misma como activa
        $validation->markAsActive();

        /*
        // En el entorno de producción
        // Si no es un dispositivo móvil o tablet no permite realizar la validación
        if (Environment::isProduction() && Mobile::isNotMobile()) {
            $mav = new ModelAndView('errors.custom');

            return $mav->render(
                [
                    'token' => $token,
                    'code' => 520,
                    'title' =>
                        Lang::get('El dispositivo de conexión no es válido'),
                    'message' =>
                        Lang::get('Únicamente puede acceder a esta página utilizando un móvil o una tablet'),
                ]
            );
        }
        */

        // Renderiza la vista de la página para realizar la validación
        // mediante un documento identificativo o acreditativo de la identidad de la persona
        $mav = new ModelAndView('workspace.passport');

        // Registra una visita del firmante a la vista de la página indicada
        $this->registerVisit($signer, $mav);

        return $mav->render(
            [
                'token' => $token,          // El token de acceso
                'signer' => $signer,        // El firmante
                // Si se deben aplicar técnicas de reconocimiento facial o no
                'useFacialRecognition' =>
                $signer->document->user->config->identificationDocument->useFacialRecognition,
            ]
        );
    }

    /**
     * Guarda los documentos de acreditación de identidad para validar un documento
     *
     * @param Request $request La solicitud
     * @param string  $token   El token de acceso
     *
     * @return JsonResponse                     Un respuesta JSON
     */
    public function savePassport(Request $request, string $token): JsonResponse
    {
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        // Obtenemos la ip y el agente de usuario
        $ip = request()->ip();
        $user_agent = $request->server('HTTP_USER_AGENT');

        // Obtenemos la posición del firmante datum WGS84
        $position = $request->input('position');

        // Actualizamos la visita realizada por el usuario
        $visit = request()->input('visit');
        $this->updateVisit($visit, $position);

        // Obtenemos la imagen frontal del usuario
        $userImage = request()->input('image');

        //
        // Para la imagen frontal del usuario
        //
        if ($userImage) {
            // Obtenemos la ruta de los archivos de documentos identificativos
            $identificationDocumentsFolder = config('validations.identification-document.folder');

            $imageInfo = (object)getimagesize($userImage);
            $userImageExtension = explode('/', $imageInfo->mime)[1];
            $userImageFile = implode('.', [Str::random(64), $userImageExtension]);
            $userImagePath = "{$identificationDocumentsFolder}/{$userImageFile}";

            // Obtenemos los datos del archivo de imagen
            $userFileDecoded = base64_decode(preg_replace('#data:[^;]+/[^;]+;base64,#', '', $userImage));

            // Guarda el archivo de imagen
            Storage::disk(env('APP_STORAGE'))->put($userImagePath, $userFileDecoded);
        }

        // Obtenemos la lista de documentos identificativos enviados
        $passports = collect(request()->input('passports'));

        // Para cada uno de los documentos identificados suministrados
        $passports->each(
            function ($passport) use (
                $signer,
                $ip,
                $user_agent,
                $position,
                $userImage,
                $userImageFile,
                $userImagePath
            ) {
                // Asocia el documento identificativo al usuario, al firmante y al documento
                $passport['user_id'] = $signer->document->user->id;
                $passport['signer_id'] = $signer->id;
                $passport['document_id'] = $signer->document->id;

                // Información adicional para la verificación del documento
                $passport['ip'] = $ip;
                $passport['user_agent'] = $user_agent;
                $passport['latitude'] = $position['latitude'];
                $passport['longitude'] = $position['longitude'];
                $passport['device'] = $this->getDevice();

                // Obtenemos la ruta de los archivos de documentos identificativos
                $identificationDocumentsFolder = config('validations.identification-document.folder');

                //
                // Para la imagen del anverso del documento
                //
                $imageInfo = (object)getimagesize($passport['front']);
                $frontExtension = explode('/', $imageInfo->mime)[1];
                $frontFile = implode('.', [Str::random(64), $frontExtension]);
                $frontPath = "{$identificationDocumentsFolder}/{$frontFile}";

                // Obtenemos los datos del archivo de imagen
                $frontFileDecoded = base64_decode(preg_replace('#data:[^;]+/[^;]+;base64,#', '', $passport['front']));

                // Guarda el archivo de imagen
                Storage::disk(env('APP_STORAGE'))->put($frontPath, $frontFileDecoded);

                $passport['front_path'] = $frontFile;

                //
                // Para la imagen del reverso del documento
                //
                $imageInfo = (object)getimagesize($passport['back']);
                $backExtension = explode('/', $imageInfo->mime)[1];
                $backFile = implode('.', [Str::random(64), $backExtension]);
                $backPath = "{$identificationDocumentsFolder}/{$backFile}";

                // Obtenemos los datos del archivo de imagen
                $backFileDecoded = base64_decode(preg_replace('#data:[^;]+/[^;]+;base64,#', '', $passport['back']));

                // Guarda el archivo de imagen
                Storage::disk(env('APP_STORAGE'))->put($backPath, $backFileDecoded);

                // Completa los datos del documento identificativo
                // con las rutas físicas de los archivos relativas a la carpeta de almacenamiento
                // de los documentos identificativos
                $passport['front_path'] = $frontFile;
                $passport['back_path'] = $backFile;

                // Obtiene el tamaño total de los archivos subidos
                // sumando el tamaño de la imagen frontal del usuario, más el anverso y reverso del documento
                $passport['size'] = strlen($userImage) + strlen($passport['front']) + strlen($passport['back']);

                // Si hay imagen frontal del usuario, se efectúa el reconocimiento facial
                // contra el anverso del documento únicamente
                if ($userImagePath) {
                    // Si el almacenamiento es S3 se deben copiar los archivos a procesar
                    // al almacenamiento público local, ya que estos archivos sólo pueden ser tratados en el servidor
                    if (AppStorage::isS3()) {
                        Storage::disk('public')->put($userImagePath, Storage::disk('s3')->get($userImagePath));
                        Storage::disk('public')->put($frontPath, Storage::disk('s3')->get($frontPath));
                    }

                    $recognition = FaceRecognition::compare(
                        Storage::disk('public')->path($userImagePath),
                        Storage::disk('public')->path($frontPath)
                    );

                    $passport['face_recognition'] = $recognition->match;

                    $passport['user_image'] = $userImageFile;
                }

                // Registra el documento identificativo en la base de datos
                Passport::create($passport);
            }
        );

        // Se marca la validación como realizada
        $validation = Validation::where('user', $signer->id)
            ->where('validation', ValidationType::PASSPORT_VERIFICATION)
            ->first()
            ->validated();               // La marcamos como inactiva y demás desde acá

        // Lanza los eventos relacionados con la realización de la validación
        event(new SignerValidationDone($validation));

        return response()->json($validation);
    }

    /**
     * Renderizar la vista donde se muestra el formulario de datos que
     * debe confirmar/modificar el firmante siguiendo lo que se le pidio
     *
     * @param string $token // El token del firmante
     * @return string
     */
    public function formdata(string $token): string
    {
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        // Obtenemos la validación de audio del firmante
        $validation = $signer->validations()->filter(
            fn ($validation) => $validation->validation == ValidationType::FORM_DATA_VERIFICATION
        )->first();

        // Si el firmante no debe realizar este tipo de validación se impide
        if (!$validation) {
            abort('403');
        }

        // Si la validación ha sido ya realizada
        // no permite volver que el proceso vuelva a ser realizado
        if ($signer->formdata()->first()->isDone()) {
            abort('404');
        }

        $validation->markAsActive();

        // Renderiza la vista de la página para realizar la validación
        $mav = new ModelAndView('workspace.formdata');

        // Registra una visita del firmante a la vista de la página indicada
        $this->registerVisit($signer, $mav);

        return $mav->render(
            [
                'token' => $token,              // El token de acceso
                'signer' => $signer,             // El firmante
                'formdata' => $signer->formdata,   // El formulario de datos
            ]
        );
    }

    /**
     * Almacenar la validacion del formulario de datos
     *
     * @param Request $request // la solicitud
     * @param string  $token   // el token del firmante
     * @return JsonResponse         // Un respuesta JSON
     */
    public function saveFormdata(Request $request, string $token): JsonResponse
    {
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        // Si la validación ha sido ya realizada
        // no permite volver que el proceso vuelva a ser realizado
        if ($signer->formdata()->first()->isDone()) {
            abort('404');
        }

        // Obtenemos la ip y el agente de usuario
        $ip = request()->ip();
        $user_agent = $request->server('HTTP_USER_AGENT');

        // Obtenemos la posición del firmante datum WGS84
        $position = $request->input('position');

        // Actualizamos la visita realizada por el usuario
        $visit = request()->input('visit');
        $this->updateVisit($visit, $position);

        // Obtenemos la lista de inputs
        $data = collect(request()->input('data'));

        // Guardamos cada uno de los datos
        $data->each(
            function ($input) use ($ip, $user_agent, $position, $signer) {
                // Información adicional para la verificación del documento
                $input['signer_id'] = $signer->id;
                $input['ip'] = $ip;
                $input['user_agent'] = $user_agent;
                $input['latitude'] = $position['latitude'];
                $input['longitude'] = $position['longitude'];
                $input['device'] = $this->getDevice();

                // actualizar el formulario en la BD
                $formdata = FormData::find($input['id']);

                // Comprobar si los textos recibidos son iguales
                // Si no lo son, se crea una copia del mismo
                if (!$formdata->textFieldAreTheSame($input['field_text'])) {
                    $formdata->saveFormDataBackup($input);
                }

                $formdata->update($input);
            }
        );

        // Se marca la validación como realizada
        $validation = Validation::where('user', $signer->id)
            ->where('validation', ValidationType::FORM_DATA_VERIFICATION)
            ->first()
            ->validated();           // La marcamos como inactiva y demás desde acá

        // Lanza los eventos relacionados con la realización de la validación
        event(new SignerValidationDone($validation));

        return response()->json($validation);
    }

    /**
     * Muestra la vista para responder a una solicitud de envío de documentos
     *
     * @param string $token El token de acceso
     *
     * @return string                           Una vista
     */
    public function request(string $token): string
    {
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        // Obtiene la solicitud de documentos
        $request = $signer->request();

        // Si no hay solicitud de documentos para
        // el firmante o ya ha sido realizada termina
        if (!$request || $request->done()) {
            abort(404);
        }

        // Obtenemos la validación de solicitud de documento del firmante y la marcamos como activa
        // Aqui puedo estar atendiendo una solicitud de documentos
        if ($signer->validations()) {
            $validation = $signer->validations()->filter(
                fn ($validation) => $validation->validation == ValidationType::DOCUMENT_REQUEST_VERIFICATION
            )->first();

            // Marcamos la validación como activa
            if ($validation) {
                $validation->markAsActive();
            }

            // Marcamos el firmante como activo sobre la solicitud
            $signer->markAsActive();
        } elseif ($signer->document_request_id) {
            // Marcamos el firmante como activo
            $signer->markAsActive();
        }

        // Carga la vista para aportar la docucmentación requerida
        $mav = new ModelAndView('workspace.requests.document-request');

        // Registra una visita del firmante a la vista de la página indicada
        if (!$signer->requestIsDone()) {
            $this->registerVisit($signer, $mav);
        }

        return $mav->render(
            [
                'token' => $token,                  // El token de acceso
                'signer' => $signer,                 // El firmante
                'request' => $request,                // La solicitud de documentos
            ]
        );
    }

    /**
     * Guarda la solicitud de documentos
     * o renueva algunos documentos en una solicitud ya realizada
     *
     * @param Request $request La solicitud
     * @param string  $token   El token de acceso
     *
     * @return JsonResponse                     Un respuesta JSON
     */
    public function saveRequest(Request $request, string $token): JsonResponse
    {
        if ($request->ajax()) {
            // Obtiene el firmante por el token
            $signer = Signer::findByToken($token);

            /**
             * Para diferenciar si se están aportando los documentos requeridos o
             * si se están renovando aquellos próximos a expirar
             * chequeamos los archivos aportados por el firmante
             * $signer->requestFiles
             */

            $renewing = false;
            if ($signer->requestFiles->count() > 0) {
                $renewing = true;
            }

            // Obtiene la solicitud de documentos
            $documentRequest = $signer->request();

            // Obtenemos la ip y el agente de usuario
            $ip = request()->ip();
            $user_agent = $request->server('HTTP_USER_AGENT');

            // Obtenemos la posición del firmante datum WGS84
            $position = $request->input('position');

            // Actualizamos la visita realizada por el usuario
            $visit = request()->input('visit');
            $this->updateVisit($visit, $position);

            // Obtenemos la lista de documentos enviados
            $documents = collect(request()->input('documents'));

            // Guardamos cada uno de los documentos
            $documents->each(
                function ($document) use (
                    $signer,
                    $ip,
                    $user_agent,
                    $position,
                    $documentRequest
                ) {
                    // Asocia cada documento al usuario, al firmante
                    // y a la solicitud de documentos
                    $document['user_id'] = $documentRequest->user->id;
                    $document['signer_id'] = $signer->id;
                    $document['document_request_id'] = $documentRequest->id;
                    $document['required_document_id'] = $document['document']['id'];

                    // Información adicional para la verificación del documento
                    $document['ip'] = $ip;
                    $document['user_agent'] = $user_agent;
                    $document['latitude'] = $position['latitude'];
                    $document['longitude'] = $position['longitude'];
                    $document['device'] = $this->getDevice();

                    // Obtenemos el nombre y la ruta del archivo en la carpeta de documentos solicitados
                    $requestDocumentsFolder = config('validations.request-document.folder');
                    $fileInfo = new \SplFileInfo($document['name']);
                    $requestDocumentFile = implode('.', [Str::random(64), $fileInfo->getExtension()]);
                    $document['path'] = "{$requestDocumentsFolder}/{$requestDocumentFile}";

                    // Obtenemos los datos del archivo
                    // chequeo si el contenido es un base64 válido
                    if (!request()->input('creator')) {
                        $fileDecoded = base64_decode(preg_replace('#data:[^;]+/[^;]+;base64,#', '', $document['file']));
                    } else {
                        if (str_starts_with($document['file'], 'data:')) {
                            $fileDecoded = base64_decode(
                                preg_replace('#data:[^;]+/[^;]+;base64,#', '', $document['file'])
                            );
                        } else {
                            $fileDecoded = Storage::disk(env('APP_STORAGE'))->get($document['file']);
                        }
                    }

                    // Guarda el documento solicitado
                    Storage::disk(env('APP_STORAGE'))->put($document['path'], $fileDecoded);

                    // Guarda el documento en la base de datos
                    DocumentRequestFile::create($document);
                }
            );

            // Si estoy aportando documentos
            if (!$renewing) {
                // Aquí se están aportando los documentos requeridos
                // Se marca la validación como realizada
                $validation = Validation::where('user', $signer->id)
                    ->where('validation', ValidationType::DOCUMENT_REQUEST_VERIFICATION)
                    ->first();

                if ($validation) {
                    $validation->validated();        // La marcamos como inactiva y demás desde acá
                } else {
                    // Es una solicitud de documentos independiente(no pertenece a validación)
                    // Se marca como realizada y se pone inactivo
                    $signer->process->done();
                    $signer->markAsInactive();
                }

                // Obtiene la solicitud de documentos
                $documentRequest = $signer->request();

                // Si quien aporta los documentos no es el creador se envia el correo
                // Lanza los eventos relacionados con la realización de la solicitud de documentos
                //if (!$signer->creator) {
                    event(new DocumentRequestDone($documentRequest, $signer));
                //}
            } else {
                // Aquí se está renovando algún documento aportado
                // Lanza los eventos relacionados con la renovación de documentos aportados
                event(new DocumentRequestFileRenewed($documentRequest, $signer));
            }

            return response()->json(
                [
                    "code" => 1,
                    'documentRequest' => $documentRequest
                ]
            );
        }
        return response()->json(["code" => -1]);
    }

    /**
     * Cancela el proceso por orden expresa del usuario firmante
     *
     * @param string $token El token de acceso
     *
     * @return JsonResponse                           Una respuesta JsonResponse
     */
    public function cancel(string $token): JsonResponse
    {
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        // Obtiene el motivo de la cancelación
        $subject = request()->input('subject');

        // Cancela el proceso
        $signer->cancel($subject);

        return response()->json($signer);
    }

    /**
     * Muestra la vista que el proceso ha sido cancelado
     *
     * @return string                           Una vista
     */
    public function canceled(): string
    {
        // Carga una vista mostrando que el proceso ha sio cancelado con éxito
        $mav = new ModelAndView('workspace.canceled');

        return $mav->render();
    }

    /**
     * Muestra la vista para renovar los documentos que expiran pronto
     * en una solicitud de documentos que ya ha sido atendida
     *
     * @param string $token El token de la solicitud de documentos
     *
     * @return string               La vista
     */
    public function requestRenewDocs(string $token): string
    {
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        // La solicitud a la que pertenece el archivo
        $request = $signer->request();

        // Verifico que pueda renovar la solicitud
        $this->authorize('renew', $request);

        // El token del firmante
        $token = $signer->token;

        // Carga la vista para aportar la docucmentación requerida
        $mav = new ModelAndView('workspace.requests.renew-document-request-file');

        $this->registerVisit($signer, $mav);

        return $mav->render(
            [
                'token' => $token,                  // El token de acceso
                'signer' => $signer,                 // El firmante
                'request' => $request,                // La solicitud de documentos
            ]
        );
    }

    /**
     * Crea el comentario de la solicitud
     *
     * @param Request $request La solicitud
     *
     * @param string  $token   token
     *
     * @return RedirectResponse                 Una redirección
     */
    public function createComment(Request $request, string $token): RedirectResponse
    {
        $request->validate(
            [
                'comment' => 'required',
            ]
        );
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        // Traemos la relacion entre el estado de la solicitud del documento
        $documentR = DocumentRequest::where('id', $signer->document_request_id)->first();

        // Creamos el comentario de la solicitud
        $comment = new WorkspaceComment;

        $comment->document_request_id = $documentR->id;
        $comment->signer_id = $signer->id;
        $comment->comment = $request->comment;
        $comment->save();

        // Creamos la notificación del comentario dejado en la solicitud
        Notification::create(
            [
                'user_id' => $documentR->user_id,
                // El usuario que solicito el documento
                'title' => Lang::get($signer->name . ' ha dejado un comentario al documento solicitado'),
                // el titulo del mensaje
                'message' => $comment->comment,
                // El motivo
                'url' => route('workspace.home', ['token' => $token]),
                //La ruta de la solicitud
                'type' => \App\Enums\NotificationTypeEnum::ATTENTION,
            ]
        );

        // Redirigir al WorkSpace
        return redirect()->back()
            ->with('message', Lang::get('El comentario se ha creado con éxito'));
    }

    /**
     * Mostrar la vista para realizar la verificación de datos
     *
     * @param string $token El token de acceso del usuario
     * @return String                   Una vista html
     */
    public function verificationForm(string $token): string
    {
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        // la verificacion de datos
        $verificationForm = $signer->verificationForm;

        // Si el firmante no debe realizar este tipo de validación se impide
        if ($signer->requestIsValidForVerificationForm()) {
            $signer->markAsInactive();
            abort(404);
        }

        // marca la verificacion como activa
        $signer->markAsActive();

        // Renderiza la vista de la página para realizar la validación
        $mav = new ModelAndView('workspace.verificationform.form');

        // Registra una visita del firmante a la vista de la página indicada
        if (!$signer->verificationFormIsDone()) {
            $this->registerVisit($signer, $mav);
        }

        return $mav->render(
            [
                'token' => $token,                      // El token de acceso
                'signer' => $signer,                     // El firmante
                'verificationForm' => $verificationForm,           // El formulario de datos
            ]
        );
    }

    /**
     * Guarda una verificacion de datos
     *
     * @param Request $request // la solicitud
     * @param string  $token   // el token del firmante
     * @return JsonResponse         // Un respuesta JSON
     */
    public function verificationSave(Request $request, string $token): JsonResponse
    {
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        // Si el firmante no debe realizar este tipo de validación se impide
        if ($signer->requestIsValidForVerificationForm()) {
            $signer->markAsInactive();
            abort(404);
        }

        // Obtenemos la ip y el agente de usuario
        $ip = request()->ip();
        $user_agent = $request->server('HTTP_USER_AGENT');

        // Obtenemos la posición del firmante datum WGS84
        $position = $request->input('position');

        // Actualizamos la visita realizada por el usuario
        $visit = request()->input('visit');
        $this->updateVisit($visit, $position);

        // Obtenemos la lista de inputs
        $data = collect(request()->input('data'));

        // Guardamos cada uno de los datos
        $data->each(
            function ($input) use ($ip, $user_agent, $position, $signer) {
                // Información adicional para la verificación del documento
                $input['signer_id'] = $signer->id;
                $input['ip'] = $ip;
                $input['user_agent'] = $user_agent;
                $input['latitude'] = $position['latitude'];
                $input['longitude'] = $position['longitude'];
                $input['device'] = $this->getDevice();

                // actualizar la verificacion en la BD
                $formdata = FormData::find($input['id']);

                // Comprobar si los textos recibidos son iguales
                // Si no lo son, se crea una copia del mismo
                if (!$formdata->textFieldAreTheSame($input['field_text'])) {
                    $formdata->saveFormDataBackup($input);
                }

                $formdata->update($input);
            }
        );

        // Se marca como realizada
        // y se pone inactivo
        // y se marca la verificacion como realizada para el usuario firmante
        $signer->process->done();
        $signer->markAsInactive();
        $signer->markVerificationFormDone();

        // Obtiene la solicitud de documentos
        $verificationForm = $signer->verificationForm;

        // Lanza los eventos relacionados con la realización de la solicitud de documentos
        event(new VerificationFormDoneEvent($verificationForm, $signer));

        return response()->json($verificationForm);
    }

    /**
     * Guarda el motivo del rechazo de la verificacion de datos
     *
     * @param Request $request los datos enviados
     * @param string  $token   El token del usuario firmante
     *
     * @return RedirectResponse                 Una redirección
     */
    public function cancelVerificationForm(Request $request, string $token): RedirectResponse
    {
        $request->validate(
            [
                'reason_cancel_verificationform_id' => 'required'
            ]
        );

        // Obtiene el usuario firmante por el token
        $signer = Signer::findByToken($token);

        // Se marca el firmante como inactivo sobre un proceso de verificacion de datos
        $signer->markAsInactive();

        // Si el id del motivo es mayor que 2
        if ($request->reason_cancel_verificationform_id > ReasonCancel::TECHNICAL_PROBLEMS) {
            // Modificamos el workspace_statu_id a cancelado
            $signer->process->update(['workspace_statu_id' => EnumsWorkspaceStatu::CANCELADO]);
        }

        // Actualizamos el campo reason_cancel_request_id en la del proceso
        $signer->process->update(['reason_cancel_request_id' => $request->reason_cancel_verificationform_id]);
        $signer->reason_cancel_request_id = $request->reason_cancel_verificationform_id;

        // Lanza los eventos relacionados con el rechazo de la verificacion de datos
        event(new VerificationFormCancelEvent($signer->verificationForm, $signer));

        // Redirigir al WorkSpace
        return redirect()->back()->with('message', Lang::get('La verificación se ha rechazado con éxito'));
    }

    /**
     * Descargar un pdf relacionado al proceso de verificacion de datos realizado por el
     * usuarios firmante
     *
     * @param string $token El token de acceso
     * @return Response                     La respuesta html
     */
    public function verificationCertificate(string $token): Response
    {
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        // Obtener un informe de participación en el proceso de verificacion
        $pdf = PDF::loadView(
            'workspace.pdf.verificationform.certificate',
            [
                'signer' => $signer,
                'verificationForm' => $signer->verificationForm
            ]
        );

        // Descarga el informe
        return $pdf->download("certificate-{$signer->verificationForm->id}.pdf");
    }

    /**
     * Verificar si se puede realizar o no la verificacion de datos
     *
     * @param string $token El token de acceso
     * @return JsonResponse             Una respuesta json
     */
    public function verificationIsDone(string $token): JsonResponse
    {
        // Obtiene el firmante por el token
        $signer = Signer::findByToken($token);

        return response()->json(
            [
                'isDone' => $signer->requestIsValidForVerificationForm() ? true : false
            ]
        );
    }

    /**
     * Muestra la vista donde se abandona el workspace
     *
     * @return string                           Una vista
     */
    public function exitWorkspace(): string
    {
        // Carga una vista
        $mav = new ModelAndView('workspace.exit-workspace');

        return $mav->render();
    }

    /**
     * Guarda un comentario a un proceso especifico
     *
     * @return JsonResponse         Una respuesta json
     */
    public function saveComment(): JsonResponse
    {
        request()->validate(
            [
                'token' => 'required',     // El token de acceso
                'comment' => 'required',     // El comentario
            ]
        );

        // es un proceso independiente de un documento
        if (request()->input('process')) {
            // el request
            $process = (object)[
                'id' => request()->input('id'),
                'comment' => request()->input('comment'),
                'name' => request()->input('process')
            ];

            // guardar el comentario segun sea el proceso
            $this->saveCommentInSeparateProcess($process);
            // Es un proceso dentro un documento hacia un usuario "firmante"
        } else {
            // el request
            $process = (object)[
                'token' => request()->input('token'),
                'comment' => request()->input('comment'),
                'validationType' => request()->input('validationType')
            ];

            // el firmante
            $signer = Signer::findByToken($process->token);

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
        }

        // la respuesta json
        return response()->json(['res' => 1]);
    }

    /**
     * Guardar un comentario segun el proceso independiente que se este solicitando
     * => verificacion de datos, solicitud de documentos
     *
     * @param object $process                   Los datos que contienen el id, el nombre del proceso
     *                                          Y el comenatario a guardar
     * @return void
     */
    public function saveCommentInSeparateProcess(object $process): void
    {
        switch ($process->name) {
                // una certificacion de datos
            case config('validations.independent-validations.dataCertification'):
                $verificationForm = VerificationForm::findOrFail($process->id);
                $verificationForm->saveComment($process->comment);
                break;

                // Una solicitud de documentos
            case config('validations.independent-validations.documentRequest'):
                $documentRequest = DocumentRequest::findOrFail($process->id);
                $documentRequest->saveComment($process->comment);
                break;

            default:
                break;
        }
    }

    /**
     * Carga la vista para la descarga del conjunto de archivos cuyo token se proporciona
     *
     * @param string $token El token de acceso al conjunto de archivos
     *                      Puede ser un token genérico, para cualquier usuario
     *                      o un token específico para un usuario concreto con
     *                      el cual se ha realizado la compartición
     *
     * @return string       Una vista
     */
    public function fileSetShare(string $token): string
    {
        // Obtenemos el conjunto de archivos a descargar por su token
        $fileSharing = FileSharing::findByToken($token);

        // Determinar el nombre para el recurso compartido
        /** @var File $firstFile */
        $firstFile = $fileSharing->files->first();
        $filesCount = $fileSharing->files->count();

        // No tiene archivos esta compartición, abortar
        if (!$firstFile) {
            abort(Response::HTTP_NOT_FOUND);
        }

        //
        $sharing = (object)[
            'token' => $fileSharing->token,
            'title' => $fileSharing->title,
            'description' => $fileSharing->description,
            'size' => 0,
            'user' => (object)[
                'image' => $firstFile->user->image,
                'name' => $firstFile->user->name,
                'lastname' => $firstFile->user->lastname,
                'email' => $firstFile->user->email,
                'phone' => $firstFile->user->phone,
                'dial_code' => $firstFile->user->dial_code,
                'position' => $firstFile->user->position,
                'company' => $firstFile->user->company,
            ]
        ];

        if ((1 === $filesCount) && !$firstFile->is_folder) {
            // Si es un fichero regular, el tipo y el tamaño del compartido es el del fichero
            $sharing->name = $firstFile->name;
            $sharing->type = $firstFile->type;
            $sharing->size = $firstFile->size;
        } else {
            $files = collect();

            foreach ($fileSharing->files as $file) {
                if ($file->is_folder) {
                    // Si es una carpeta, obtener los ficheros que contiene y agregarlos al listado respetando
                    // la estructura original
                    $innerFiles = FileUtils::getInnerFiles($file);

                    foreach ($innerFiles as $item) {
                        $files->add($item);

                        // Ir calculando el tamaño real del compartido
                        $sharing->size += $item->size;
                    }
                } else {
                    $files->add($file);

                    // Ir calculando el tamaño real del compartido
                    $sharing->size += $file->size;
                }
            }

            $sharing->name = $firstFile->name . ' (' . $files->count() . ' ficheros)';
            $sharing->type = 'application/zip';
        }

        // Obtiene el contacto o destinatario por el token
        // o bien null si se ha utilizado el token genérico de acceso al conjunto de archivos compartidos
        $contact = FileSharingContact::findByToken($token);

        // Carga la vista para compartir un archivo
        $mav = new ModelAndView('landing.share');

        return $mav->render(
            [
                'sharing' => $sharing,
                'file' => $fileSharing,
                // El conjunto de archivos compartidos
                'contact' => $contact,
                // El contacto
                'token' => $token,
                // El token utilizado que puede ser el genérico o un token para un contacto dado
                'files' => $files ?? [$firstFile],
            ]
        );
    }

    /**
     * Registra una visita a la descarga de la compartición
     *
     * @param Request $request
     * @param string  $token El token de acceso al conjunto de archivos Puede ser un token genérico, para cualquier
     *                       usuario o un token específico para un usuario concreto con el cual se ha realizado la
     *                       compartición
     * @return JsonResponse
     */
    public function fileSetShareLog(Request $request, string $token): JsonResponse
    {
        // Obtenemos el conjunto de archivos a descargar por su token
        $fileSharing = FileSharing::findByToken($token);

        // Actualizar la fecha de última actualización, pues es el indicador de última visita
        $fileSharing->updated_at = now();
        $fileSharing->save();

        // Obtiene el contacto o destinatario por el token
        // o bien null si se ha utilizado el token genérico de acceso al conjunto de archivos compartidos
        $contact = FileSharingContact::findByToken($token);

        // Anota una visita a la descarga
        $fileSharing->histories()->create(
            [
                'user_id' => $fileSharing->user_id,
                'file_sharing_contact_id' => $contact->id ?? null,          // El id del contacto o null
                'ip' => $request->ip(),                                    // La dirección IP
                'user_agent' => $request->server('HTTP_USER_AGENT'),  // El agente de usuario
                'starts_at' => now(),
                'latitude' => $request->position['latitude'] ?? null,
                'longitude' => $request->position['longitude'] ?? null,
            ]
        );

        return response()->json(['Registrada nueva visita al compartido']);
    }

    /**
     * Carga la vista para la descarga del conjunto de documentos
     *
     * @param string $token     El token de acceso
     * @return string           Una vista
     */
    public function documentShare(string $token): string
    {
        // Obtenemos el conjunto de documentos
        $documentSharing = DocumentSharing::findByToken($token);

        // Obtiene el contacto o destinatario por el token
        // o bien null si se ha utilizado el token genérico de acceso al conjunto de archivos compartidos
        $contact = DocumentSharingContact::findByToken($token);

        // la data a guardar como visita
        $dataSharing =  [
            // el id del usuario creador
            'user_id'                   => $documentSharing->document ? $documentSharing->document->user->id : null,
            'file_sharing_contact_id'   => $contact->id ?? null,                        // El contacto
            'ip'                        => request()->ip(),                             // La dirección IP
            'user_agent'                => request()->server('HTTP_USER_AGENT'),        // El agente de usuario
            'starts_at'                 => now()                                        // la fecha de la visita
        ];

        // Anota una visita a la descarga
        $documentSharing->histories()->create($dataSharing);

        // Carga la vista para compartir un documento
        $mav = new ModelAndView('landing.sharing.document');

        return $mav->render([

            // toda la comparticion
            'sharing' => $documentSharing->getDataSharingInWorkspace(),

            // El documento a compartir
            'documentSharing' => $documentSharing,

            // El contacto
            'contact' => $contact,

            // El token utilizado que puede ser el genérico o un token para un contacto dado
            'token' => $token
        ]);
    }

    /**
     * Dscarga el conjunto de documentos aportados en un proceso de firma
     *
     * @param string $token                             El token de acceso
     * @return * @return ZipStream|String               Los documentos comprimidos en zip o una vista de error
     */
    public function downloadDocumentShare(string $token)
    {
        // Obtenemos el conjunto de documentos
        $documentSharing = DocumentSharing::findByToken($token);

        // Obtiene el contacto o destinatario por el token
        // o bien null si se ha utilizado el token genérico de acceso al conjunto de documentos compartidos
        $contact = DocumentSharingContact::findByToken($token);

        // la data a guardar como descarga
        $dataSharing =  [
            // el id del usuario creador
            'user_id'                   => $documentSharing->document ? $documentSharing->document->user->id : null,
            'file_sharing_contact_id'   => $contact->id ?? null,                        // El contacto
            'ip'                        => request()->ip(),                             // La dirección IP
            'user_agent'                => request()->server('HTTP_USER_AGENT'),        // El agente de usuario
            'downloaded_at'             => now()                                        // la fecha de descarga
        ];

        // Anota una visita a la descarga una visita a la descarga
        // Dejando constancia del momento en que se inicia la misma
        $documentSharing->histories()->create($dataSharing);

        // Genera la descarga del archivo zip o devuelve una vista de error
        return $this->createDocumentZip($documentSharing->document, true);
    }

    /**
     * Obtener un zip con los documentos aportados en un proceso de firma
     *
     * @param Bool $noSigners               Identifica si la comparticion es por "firmantes" o "contactos"
     * @param Document $document            El documento
     * @return ZipStream|String             Los documentos comprimidos en zip o una vista de error
     */
    public function createDocumentZip(Document $document, bool $noSigners)
    {
        // El nombre del archivo zip que se va a descargar y que tiene el mismo nombre que el archivo firmado
        $zipFile    = implode('.', [pathinfo($document->name, PATHINFO_FILENAME), 'zip']);

        // Crea un nuevo archivo zip
        $zip = Zip::create($zipFile);

        // Comprueba si el archivo firmado existe
        if (Storage::disk(env('APP_STORAGE'))->exists($document->signed_path)) {
            // El nombre del archivo pdf firmado
            $signedFile = implode('.', [pathinfo($document->name, PATHINFO_FILENAME), 'pdf']);

            // Añade el archivo firmado al zip
            $zip->add(AppStorage::path($document->signed_path), $signedFile);
        } else {
            if ($document->onlyCanBeSigned()) {
                // info("El archivo firmado no existe... re-generandolo");
                $this->signDocument($document);
            }
        }

        // Añade el archivo original
        $zip->add(AppStorage::path($document->original_path), "original/{$document->name}");

        // Añade los archivos de audio si los hay
        $audioFolder    = Str::lower(config('validations.audio.folder'));

        foreach ($document->signers as $signer) {
            foreach ($signer->audios as $audio) {
                $signerFolder = trim("{$signer->name} {$signer->lastname} {$signer->email}");
                $zip->add(AppStorage::path("{$audioFolder}/{$audio->path}"), "audios/{$signerFolder}/{$audio->path}");
            }
        }

        // Añade los archivos de video si los hay
        $videoFolder    = Str::lower(config('validations.video.folder'));

        foreach ($document->signers as $signer) {
            foreach ($signer->videos as $video) {
                $signerFolder = trim("{$signer->name} {$signer->lastname} {$signer->email}");
                $zip->add(AppStorage::path("{$videoFolder}/{$video->path}"), "videos/{$signerFolder}/{$video->path}");
            }
        }

        // Añade los archivos de captura de pantalla si los hay
        $captureFolder    = Str::lower(config('validations.capture.folder'));

        foreach ($document->signers as $signer) {
            foreach ($signer->captures as $capture) {
                $signerFolder = trim("{$signer->name} {$signer->lastname} {$signer->email}");
                $zip->add(AppStorage::path(
                    "{$captureFolder}/{$capture->path}"
                ), "captures/{$signerFolder}/{$capture->path}");
            }
        }

        // Añade los documentos identificativos si los hay
        $passportFolder    = Str::lower(config('validations.identification-document.folder'));

        foreach ($document->signers as $signer) {
            $signerFolder = trim("{$signer->name} {$signer->lastname} {$signer->email}");
            foreach ($signer->passports as $passport) {
                // La imagen frontal del usuario
                if ($passport->user_image) {
                    $zip->add(
                        AppStorage::path("{$passportFolder}/{$passport->user_image}"),
                        "documents/{$signerFolder}/user-{$passport->user_image}"
                    );
                }
                // El anverso del documento
                if ($passport->front_path) {
                    $zip->add(
                        AppStorage::path("{$passportFolder}/{$passport->front_path}"),
                        "documents/{$signerFolder}/front-{$passport->front_path}"
                    );
                }
                // El reverso del documento
                if ($passport->back_path) {
                    $zip->add(
                        AppStorage::path("{$passportFolder}/{$passport->back_path}"),
                        "documents/{$signerFolder}/back-{$passport->back_path}"
                    );
                }
            }
        }

        // Genera el certificado
        $certificate = PDF::loadView('dashboard.documents.pdf.certificate', [
            'document'      => $document,
            'noSigners'     => $noSigners
        ]);

        // // Lo añade al archivo zip
        $zip->addRaw($certificate->download()->getOriginalContent(), "certificate-{$document->guid}.pdf");

        // Crea un archivo de texto plano que contiene los hash de los archivos original y firmado
        $date = (new \DateTime)->format('Y-m-d H:i');       // El momento actual
        $app  = config('app.name');                         // El nombre de la aplicación
        $url  = config('app.url');                          // La url de la aplicación

        $zip->addRaw(
            "
                 -----------------------------------------------------------------------
                 BEGIN
                 -----------------------------------------------------------------------
                 File : {$document->name}
                 Date : {$document->updated_at->format('d-m-Y H:i')}
                 -----------------------------------------------------------------------
                 Original
                 -----------------------------------------------------------------------
                 md5 : {$document->original_md5}
                 sha1: {$document->original_sha1}
                 -----------------------------------------------------------------------
                 Signed
                 -----------------------------------------------------------------------
                 md5 : {$document->signed_md5}
                 sha1: {$document->signed_sha1}
                 -----------------------------------------------------------------------
                 END
                 -----------------------------------------------------------------------
                 Verified by {$app} cryptographic module on {$date}
                 {$url}
             ",
            "verification-hash.txt"
        );

        return $zip;
    }

    /**
     * Obtiene el documento firmado
     *
     * @param Document $document El documento
     *
     * @return string El nombre del documento firmado
     *                o una vista de error
     * @throws DocumentTooBigException          El documento es demasiado grande
     */
    public function signDocument(Document $document): string
    {
        // Marcamos que el documento está siendo procesado en ese momento
        // ya que esta acción puede demorar tiempo
        $document->isBeingProcessed();

        // Descompone las páginas del documento en imágenes individuales
        $document->images = $this->getDocumentImages($document);

        // Obtenemos cada una de las firmas del documento
        foreach ($document->signs as $sign) {
            // Si hay firma efectuada se añade al documento
            if ($sign->sign) {
                $this->addSignToDocument($sign, $document);
            }
        }

        // Obtenemos cada una de los sellos del documento
        foreach ($document->stamps as $stamp) {
            $this->addStampToDocument($stamp, $document);
        }

        // Obtenemos cada una de las cajas de texto del documento
        foreach ($document->boxs as $box) {
            // Si hay caja completada se añade al documento
            if ($box->text) {
                $this->addTextToDocument($box, $document);
            }
        }

        // Creamos el documento firmado a partir de las imágenes independientes
        $documentSigned = $this->createSignedDocument($document);

        // Eliminar las imágenes utilizadas en el procesamiento
        $document->deleteImagesUsesInProcess();

        // Marcamos que el documento ha sido procesado
        $document->hasBeenProcessed();

        return $documentSigned;
    }

    /**
     * Dscarga los datos de facturacion de un usuario que ha compartido su informacion
     * con otro usuario externo
     *
     * @param string $token                 El token de la facturacion
     * @return Response                     Una documento pdf
     */
    public function downloadBillingData(string $token) : Response
    {
         // Obtiene el firmante por el token
        $company = CompanySharing::findByToken($token);
        $language = config('app.locale');
        $countries = [];

        if (!$company) {
            return redirect()->back()->with('error', Lang::get('No se ha encontrado el archivo solicitado'));
        }

        try {
            /**
             * @see https://github.com/umpirsky/Transliterator
             */
            $countries = Countries::getList($language == 'cn' ? 'zh' : $language);
        } catch (\RuntimeException $e) {
            // Si falla se devuelven los paises en el idioma nativo de la app
            $countries = Countries::getList('es');
        }

         // Obtener un informe de participación en el proceso de validación
        $pdf = PDF::loadView('workspace.pdf.billingdata.user-billing-data', [
            'company'   => $company,
            'countries' => $countries
        ]);

        // nombre a asignar al pdf
        $namePDF = 'certificate-'.date('dmYhis');

         // Descarga el informe
         return $pdf->download("{$namePDF}.pdf");
    }
}
