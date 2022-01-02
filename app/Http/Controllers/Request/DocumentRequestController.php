<?php

/**
 * Controlador de las solicitudes de documentos
 *
 * Una solicitud de documentos es un proceso en el cual el usuario de Fikrea invita a una serie
 * de contactos a adjuntar uno o más documentos específicos que precisa. Estos usuarios son
 * designados como firmantes (signers) en este proceso
 *
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers\Request;

use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\SmsController;
use App\Models\MediaType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

/**
 * Modelos requeridos
 */
use App\Models\Guest;
use App\Models\Signer;
use App\Models\Document;
use App\Models\DocumentRequest;
use App\Models\DocumentRequestFile;
use App\Models\RequiredDocumentExample;
use App\Models\WorkspaceStatu;

/**
 * Enumeraciones requeridas
 */
use App\Enums\TimePeriod;
use App\Enums\ValidationType;
use App\Enums\DocumentSharingType;

/**
 * Fikrea
 */
use Fikrea\ModelAndView;
use Fikrea\AppStorage;

/**
 * Carbon
 */
use Carbon\Carbon;

/**
 * Creación de archivos Zip al vuelo
 *
 * @link https://github.com/stechstudio/laravel-zipstream
 */

use Zip;
use STS\ZipStream\ZipStream;

/**
 * Jobs para la creación del documento firmado
 */
use App\Jobs\SignDocument;

/**
 * PDF
 */
use PDF;

/**
 * DomPDF
 */
use Dompdf\Dompdf;

use App\Events\DocumentRequestUrlGeneratedEvent;

class DocumentRequestController extends Controller
{
    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Muestra la vista para crear o editar una solicitud de documentos
     *
     * @param int|null $id El id de al solicitud de documentos o
     *                     null para crear una solicitud de documentos nuevo
     *
     * @return string                           Una vista
     */
    public function edit(?int $id = null): string
    {
        // Si se está visualizando un documento ya existente
        if ($id) {
            // Obtenemos la solicitud de documentos
            $documentRequest = DocumentRequest::findOrFail($id);

            // Comprobamos si el usuario actual puede visualizarlo
            $this->authorize('edit', $documentRequest);
        }

        $mav = new ModelAndView('dashboard.requests.edit');

        // textos para pasarle a Vue js
        $validityTexts = [
            'day'        => Lang::get('día'),
            'month'      => Lang::get('mes'),
            'year'       => Lang::get('año'),
            'days'       => Lang::get('días'),
            'months'     => Lang::get('meses'),
            'years'      => Lang::get('años'),
        ];

        $documentTypes = MediaType::where('signable', 1)->get(['media_type', 'description'])->map(
            function (MediaType $mime) {
                $mime->description = $mime->description ?? $mime->media_type;

                return $mime;
            }
        )->pluck('description');

        return $mav->render(
            [
                // La solicitud de documentos si existe una solicitud previa
                'documentRequest'   => $documentRequest ?? null,
                // Los documentos requeridos que se proporcionan como ejemplo para la solicitud
                // en función del idioma actual
                'documentExamples'  => RequiredDocumentExample::get(app()->getLocale()),
                // Los tipos de archivos admisibles
                'documentTypes'     => $documentTypes,
                // Los tamaños máximos de archivos admisibles em kb
                'documentSizes'     => config('validations.request-document.sizes'),
                // Los textos para los tiempos de validez de un documento
                'validityTexts'     => $validityTexts,
            ]
        );
    }

    /**
     * Guarda una solicitud de documentos
     *
     * @param Request $request La solicitud
     *
     * @return JsonResponse                     Una respuesta JSON
     */
    public function save(Request $request): JsonResponse
    {
        $user = Auth::user() ?? Guest::user();
    
        // El id de la solicitud de documentos o null si es nueva
        $id      = request()->input('id');

        // El nombre y el comentario
        $name    = request()->input('name');
        $comment = request()->input('comment');

        // La lista de documentos requeridos
        $documents = request()->input('documents');

        if ($id) {
            // Obtiene la solicitud de documentos y la actualiza
            $documentRequest = DocumentRequest::findOrFail($id);

            $documentRequest->comment = $comment;
            $documentRequest->workspace_statu_id = \App\Enums\WorkspaceStatu::PENDIENTE;
            $documentRequest->save();
        } else {
            // Crea la solicitud de documentos
            $documentRequest = $user->documentRequests()->create(
                [
                    'name'      => $name,
                    'comment'   => $comment,
                ]
            );
        }

        // Añade los documentos requeridos a la solicitud
        foreach ($documents as &$document) {
            // Sólo se crea un documento si se le ha dado nombre
            if ($document['name']) {
                // Si se ha especificado un periodo de validez
                // Obtiene la fecha de expedición máxima requerida para el documento
                if ($document['validity']) {
                    if ($document['validity_unit'] == TimePeriod::DAY) {
                        $document['issued_to'] = Carbon::now()->subDays($document['validity']);
                    } elseif ($document['validity_unit'] == TimePeriod::MONTH) {
                        $document['issued_to'] = Carbon::now()->subMonths($document['validity']);
                    } elseif ($document['validity_unit'] == TimePeriod::YEAR) {
                        $document['issued_to'] = Carbon::now()->subYears($document['validity']);
                    }
                }
                
                $documentRequest->documents()->create($document);
            }
        }

        return response()->json($documentRequest);
    }

    /**
     * Muestra la vista para seleccionar los usuarios "firmantes" a los que se solicita los documentos
     *
     * @param int $id El id de la solicitud de documentación
     *
     * @return string                           Una vista
     */
    public function signers(int $id): string
    {
        // Obtenemos la solicitud de documentos
        $documentRequest = DocumentRequest::findOrFail($id);

        // Verifica si el usuario actual está autorizado para definir las personas "firmantes"
        $this->authorize('signers', $documentRequest);

        $mav = new ModelAndView('dashboard.config.request.select-signers');

        return $mav->render(
            [
                'document' => $documentRequest,
            ]
        );
    }

    /**
     * Obtiene los firmantes o personas para los que se han solicitado los documentos
     *
     * @param int $id El id de la solicitud de documentos
     *
     * @return JsonResponse                     Una respusta JSON
     */
    public function getSigners(int $id): JsonResponse
    {
        // Obtenemos el documento
        $documentRequest = DocumentRequest::findOrFail($id);

        // Verifica si el usuario actual está autorizado a obtener los firmante o personas
        // que deben enviar sus documentos
        $this->authorize('signers', $documentRequest);

        // Devolvemos los firmantes que no son el propio autor del documento
        $signers = $documentRequest->signers()->get();

        return response()->json($signers);
    }

    /**
     * Guarda las personas o "firmantes" que deben atender a la solicitud de documentos
     *
     * @param int $id El id de la solicitud de documento
     *
     * @return RedirectResponse                 Un respuesta JSON
     */
    public function saveSigners(int $id): JsonResponse
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();
        
        // Obtenemos el documento
        $documentRequest = DocumentRequest::findOrFail($id);

        // Verifica si el usuario actual está autorizado a configurar la firma del documento
        $this->authorize('signers', $documentRequest);

        // Obtenemos los usuarios "firmantes" que deben enviar los documentos solicitados
        $signers = request()->input('signers');
        
        // Obtenemos las direcciones de correo de los firmantes ya existentes
        $registerEmails = $documentRequest->signers->map(fn ($signer) => $signer->email)->toArray();

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
        foreach ($documentRequest->signers as $signer) {
            if (!in_array($signer->email, $sendedEmails)) {
                $signer->delete();
            }
        }

        // Añade sólo los firmantes nuevos, es decir, que no estaban en la lista de firmantes
        // Para cada firmante se genera un token de acceso
        foreach ($signers as &$signer) {
            $signer['token'] = Str::random(64);
        }

        // Relaciona los firmantes con la solicitud de documentos
        // y crea el proceso para cada firmante
        $documentRequest->signers()->createMany($signers)->each(
            function ($signer) {
                $signer->process()->create([]);
            }
        );

        // Volvemos a obtener la solicitud de documentos actualizada ya con la lista de firmantes
        $documentRequest = DocumentRequest::findOrFail($id);

        // Se registra una nueva compartición de la solicitud de documentos
        $documentRequest->sharings()->create(
            [
                'signers' => json_encode(
                    [
                        'signers' => $documentRequest->signers
                            ->filter(fn ($signer) => !$signer->creator)
                            ->map(fn ($signer) => $signer->id)
                    ]
                ),
                'type'  => DocumentSharingType::MANUAL,
            ]
        );

        // Notificar a los firmantes
        // Se envía un email/SMS a cada firmante con un enlace a su espacio de usuario
        $documentRequest->signers->filter(fn ($signer) => !$signer->creator)->each(
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
        EmailController::confirmDocumentRequestShared($documentRequest->user, $documentRequest);

        return response()->json(
            [
                'code'      => 1,
                'message'   => Lang::get('Firmantes guardados con éxito'),
            ]
        );
    }

    /**
     * Genera URL para la aportación de documentos, creando un signer falso
     * que accederá al workspace para completar la solicitud
     *
     * @param int $id El id de la solicitud de documento
     *
     * @return RedirectResponse                 Un respuesta JSON
     */
    public function generateUrl(int $id): JsonResponse
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();
        
        // Obtenemos el documento
        $documentRequest = DocumentRequest::findOrFail($id);

        // Verifica si el usuario actual está autorizado a configurar la firma del documento
        $this->authorize('signers', $documentRequest);

        // Debo crear un fake signer
        // Creo el fake signer o firmante falso
        $fakeSigner = $documentRequest->signers()->create([
            'token'     => Str::random(64),
            'name'      => config('request.user.name'),
            'lastname'  => config('request.user.lastname'),
            'email'     => config('request.user.email'),
        ]);

        // Creo el proceso para este firmante
        $fakeSigner->process()->create([]);

        // Volvemos a obtener la solicitud de documentos actualizada ya con la lista de firmantes
        $documentRequest = DocumentRequest::findOrFail($id);
        
        // Se registra una nueva compartición de la solicitud de documentos
        $documentRequest->sharings()->create(
            [
                'signers' => json_encode(
                    [
                        'signers' => $documentRequest->signers
                            ->filter(fn ($signer) => !$signer->creator)
                            ->map(fn ($signer) => $signer->id)
                    ]
                ),
                'type'    => DocumentSharingType::MANUAL,
            ]
        );

        // Devuelvo la ruta para acceder a la solicitud de documentos
        $route = route('workspace.home', ['token'=>$fakeSigner->token]);

        return response()->json([
            'code' => 1, 'route' => $route
        ]);
    }

    /**
     * Finaliza el proceso de solicitud de documentos mediante URL
     * al lanzar evento de creación de notificación al usuario creador de la misma
     * con información relacionada con la solicitud
     *
     * @param Request $request   La petición
     * @param int     $id        El id de la solicitud de documento
     *
     * @return RedirectResponse                 Un respuesta JSON
     */
    public function saveDocumentRequestByUrl(Request $request, int $id): JsonResponse
    {
        if ($request->ajax()) {

            // Obtenemos el documento
            $documentRequest = DocumentRequest::findOrFail($id);

            // Desatamos evento para crear notifiación al usuario creador de la solicitud
            event(new DocumentRequestUrlGeneratedEvent(
                $documentRequest,
                $documentRequest->signers->first()
            ));

            return response()->json(['code' => 1]);
        }
    }

    /**
     * Muestra la vista con la lista de solicitudes de documentos
     *
     * @return string                           Una vista
     */
    public function list(): string
    {
        // Obtenemos el usuario actual
        $user = Auth::user() ?? Guest::user();

        // Obtenemos las solicitudes de documentos del usuario actual
        $documentRequests = DocumentRequest::findByUser($user);

        $mav = new ModelAndView('dashboard.requests.list.requests-list');

        return $mav->render(
            [
                // Documentos paginados según configuración
                'documents' => $documentRequests->paginate(config('documents.pagination')),
                // El espacio ocupado por archivos y documentos
                'diskSpace' => $user->diskSpace,
            ]
        );
    }

    /**
     * Muestra el estado actual de la solicitud de documentos
     *
     * @param int $id El id de la solicitud de documentos
     *
     * @return string                           Una vista
     */
    public function requestStatus(int $id): string
    {
        // Obtenemos la solicitud de documentos
        $documentRequest = DocumentRequest::findOrFail($id);

        $this->authorize('status', $documentRequest);

        $mav = new ModelAndView('dashboard.requests.status.status');

        return $mav->render(
            [
                'request' => $documentRequest,
            ]
        );
    }

    /**
     * Descarga un archivo que forma parte de una solicitud de documentos
     *
     * @param int $id El id del archivo que forma parte de la solicitud
     *
     * @return StreamedResponse                 Un stream con el archivo a descargar
     */
    public function downloadFile(int $id): StreamedResponse
    {
        // Obtenemos el archivo por su id
        $file = DocumentRequestFile::findOrFail($id);

        // Verifica si el usuario actual está autorizado para descargar el archivo
        $this->authorize('download', $file);
       
        return Storage::disk(env('APP_STORAGE'))->download($file->path, $file->name);
    }

    /**
     * Descarga todos los archivos que forman parte de una solicitud de documentos
     *
     * @param int $id El id de la solicitud de archivos
     *
     * @return ZipStream                        Un stream con el archivo ZIP a descargar
     */
    public function downloadFiles(int $id): ZipStream
    {
        // Obtenemos el archivo por su id
        $request = DocumentRequest::findOrFail($id);

        // Verifica si el usuario actual está autorizado para descargar el archivo
        $this->authorize('download', $request);
       
        // Crea el archivo Zip al vuelo
        $zip = Zip::create("{$request->name}.zip");

        $request->files->each(
            function ($file) use ($zip) {
                // Se crea una carpeta independiente para cada usuario "firmante"
                $zip->add(
                    AppStorage::path($file->path),
                    "{$file->signer->id} {$file->signer->email}/{$file->id}-{$file->name}"
                );
            }
        );

        return $zip;
    }

    /**
     * Muestra la vista con histórico en solicitud de documentos
     *
     * @param int $id El id de la solicitud de archivos
     *
     * @return string                           Una vista
     */
    public function historyDocumentRequest(int $id): string
    {
        // Obtenemos la solicitud de documentos
        $documentRequest = DocumentRequest::findOrFail($id);

        $mav = new ModelAndView('dashboard.requests.history.history');

        return $mav->render(
            [
                'request'   => $documentRequest,
            ]
        );
    }

    /**
     * Envía una solicitud de documentos a aquellos firmantes que aún no la han atendido
     *
     * @param int $id El id de la solicitud de documentos
     *
     * @return JsonResponse                     Un respuesta JSON
     */
    public function sendDocumentRequest(int $id): JsonResponse
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        // Obtenemos la solicitud de documentos
        $documentRequest = DocumentRequest::findOrFail($id);

        // Verifica si el usuario actual está autorizado a ver el estado de la solicitud
        $this->authorize('status', $documentRequest);

        // Obtenemos los usuarios únicos que aún no han completado la solicitud
        $signers = $documentRequest->signers->where('validated', false);

        // Notificar a los firmantes que no sean el creador/autor del documento
        // Se envía un email/SMS a cada firmante con un enlace a su espacio de usuario
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

        // Se registra una nueva compartición de Documento
        $documentRequest->sharings()->create(
            [
                'signers' => json_encode(
                    [
                        'signers' => $signers
                            ->filter(fn ($signer) => !$signer->creator)
                            ->map(fn ($signer) => $signer->id)
                    ]
                ),
                'type'    => DocumentSharingType::MANUAL,
            ]
        );

        return response()->json($signers);
    }

    /**
     * Muestra la vista dónde se seleccionan los documentos que debe aportar cada
     * firmante como validación del proceso de firma
     *
     * @param int $id El id del documento a firmar
     *
     * @return string      La vista
     */
    public function validationsDocumentRequest(int $id) : string
    {
        // Obtenemos el documento
        $document = Document::findOrFail($id);

        // Verifica si el usuario actual está autorizado a configurar el documento
        $this->authorize('config', $document);

        // Comprobar que el número de páginas del documento no es superior al permitido en la configuración
        if ($document->pages > config('documents.max.pages')) {
            // Carga la página de error
            $mav = new ModelAndView('errors.custom');

            return $mav->render(
                [
                    'code'      => 502,
                    'title'     => Lang::get('El archivo suministrado tiene demasiadas páginas'),
                    'message'   => Lang::get('El archivo no puede ser procesado por la aplicación'),
                ]
            );
        }

        // Se seleccionan como firmantes únicamente aquellos para los que se ha elegido
        // la validación mediante el procedimiento de Solicitud de Documentos
        $signers = $document->signers->filter(
            fn ($signer) => $signer->mustValidate($document, ValidationType::DOCUMENT_REQUEST_VERIFICATION)
        )->values();

        // Muesta la vista para la gestión de las Solicitudes de documentos para los firmantes
        $mav = new ModelAndView(
            'dashboard.requests.validations.config-requests-in-validation-document'
        );

        // textos para pasarle a Vue js
        $validityTexts = [
            'day'        => Lang::get('día'),
            'month'      => Lang::get('mes'),
            'year'       => Lang::get('año'),
            'days'       => Lang::get('días'),
            'months'     => Lang::get('meses'),
            'years'      => Lang::get('años'),
        ];

        $documentTypes = MediaType::where('signable', 1)->get(['media_type', 'description'])->map(
            function (MediaType $mime) {
                $mime->description = $mime->description ?? $mime->media_type;

                return $mime;
            }
        )->pluck('description');

        return $mav->render(
            [
                'document' => $document,
                'signers'  => $signers,
                'documentExamples'  => RequiredDocumentExample::get(app()->getLocale()),
                // Los tipos de archivos admisibles
                'documentTypes'     => $documentTypes,
                // Los tamaños máximos de archivos admisibles em kb
                'documentSizes'     => config('validations.request-document.sizes'),
                // Los textos para los tiempos de validez de un documento
                'validityTexts'     => $validityTexts,
            ]
        );
    }

    /**
     * Guarda las solicitudes de documentos creadas para los firmantes
     * durante el proceso de selección de validaciones de cada firmante
     *
     * @param Request $request La petición
     * @param int     $id      El id del documento a firmar
     *
     * @return JsonResponse Un respuesta JSON
     */
    public function saveValidationsDocumentRequest(
        Request $request,
        int $id
    ) : JsonResponse {
        if ($request->ajax()) {
            // Obtenemos el usuario autenticado
            $user = Auth::user() ?? Guest::user();

            // Se crea una solicitud de documentos por cada firmante
            // con los x documentos requeridos
            foreach ($request->signers as $key => $signer) {
                // comentario para la solicitud
                $comment = Lang::get(
                    'Solicitud de documento desde proceso de validación para firmante :firmante',
                    [
                        'firmante' => $signer["name"],
                    ]
                );
                
                // 1. Crea la solicitud
                $documentRequest = $user->documentRequests()->create(
                    [
                        'name'      => $comment,
                        'comment'   => $comment,
                    ]
                );
                
                // 2. Se registra una nueva compartición de la solicitud de documentos
                $documentRequest->sharings()->create(
                    [
                        'signers' => json_encode(['signers' => [ $signer["id"] ] ]),
                        // 0 = Envío automático, 1 = Envío manual
                        'type'    => DocumentSharingType::MANUAL,
                    ]
                );

                $objSigner = Signer::find($signer["id"]);
                $objSigner->document_request_id = $documentRequest->id;
                // Aqui si es el creador del documento no tiene token asignado por lo que se le crea
                // para que pueda atender a la solicitud de documento
                if (!$objSigner->token) {
                    $objSigner->token = Str::random(64);
                }
                $objSigner->save();

                // 3. Filtra los documentos requeridos del firmante
                $documents = array_filter(
                    $request->requests,
                    fn ($req) => $req["signer"]["id"] == $signer["id"]
                );

                // 4. Añade los documentos requeridos del firmante a la solicitud
                foreach (array_map(fn ($req) => $req['document'], $documents) as &$document) {
                    // Sólo se crea un documento si se le ha dado nombre
                    if ($document['name']) {
                        // Si se ha especificado un periodo de validez
                        // Obtiene la fecha de expedición máxima requerida para el documento
                        if ($document['validity']) {
                            if ($document['validity_unit'] == TimePeriod::DAY) {
                                $document['issued_to'] = Carbon::now()->subDays($document['validity']);
                            } elseif ($document['validity_unit'] == TimePeriod::MONTH) {
                                $document['issued_to'] = Carbon::now()->subMonths($document['validity']);
                            } elseif ($document['validity_unit'] == TimePeriod::YEAR) {
                                $document['issued_to'] = Carbon::now()->subYears($document['validity']);
                            }
                        }
                        
                        $documentRequest->documents()->create($document);
                    }
                }
            }

            // Obtenemos el documento
            $document = Document::findOrFail($id);

            // En caso de que se haya realizado una configuración de firma manuscrita anteriormente
            // o si fue validado con formulario de datos especificos
            // debemos realizar los pasos que se deben ejecutar al finalizar la misma
            if ($document->mustBeValidateByHandWrittenSignature() || $document->mustBeValidateByFormData()) {
                // Se marca el documento como enviado
                $document->send();


                // El documento puede contener firmas del propio autor/creador del documento que deben ser procesadas
                // Pone el cola la creación del documento firmado ya que es un proceso que puede demorar tiempo
                SignDocument::dispatch($document);

                // Se registra una compartición de Documento
                $document->sharings()->create(
                    [
                        'signers' => json_encode(
                            [
                                'signers' => $document->signers->map(fn ($signer) => $signer->id)
                            ]
                        ),
                        'type'    => DocumentSharingType::MANUAL,
                    ]
                );
            }

            // Notificar a los firmantes que no sean el creador/autor del documento
            // Se envía un email/SMS a cada firmante con un enlace a su espacio de usuario
            $document->signers->filter(fn ($signer) => !$signer->creator)->each(
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

            // Se envía un correo al creador/autor del documento confirmando que
            // ha compartido un documento
            EmailController::confirmDocumentShared($document->user, $document);

            return response()->json(['code'=>1]);
        } else {
            return response()->json(['code'=>-1]);
        }
    }

    /**
     * Obtiene el certificado en PDF del proceso de solicitud de documentos
     *
     * @param int $id El id de la solicitud
     *
     * @return Response                         Una respuesta HTTP
     */
    public function certificate(int $id)
    {
        // Obtenemos el documento
        $request = DocumentRequest::findOrFail($id);

        // Verifica si el usuario actual está autorizado para generar y descargar el certificado de validación
        $this->authorize('certificate', $request);

        //return view('dashboard.requests.pdf.certificate',['request'      => $request]);

        // Genera un archivo PDF, cargando la vista correspondiente
        $pdf = PDF::loadView(
            'dashboard.requests.pdf.certificate',
            [
                'request'      => $request,
            ]
        );

        // Genera la descarga del certificado
        return $pdf->download("certificate-request-{$request->id}.pdf");
    }
}
