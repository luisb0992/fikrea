<?php

/**
 * DocumentController
 *
 * Controlador del gestor de documentos
 * Gestiona los documentos subidos, conversión y almacenamiento
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers\Signature;

use App\Models\MediaType;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;


use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controladores requeridos
 */

use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\Signature\Traits\DocumentProcess;

/**
 *  Modelos requeridos
 */

use App\Models\Document;
use App\Models\Signer;
use App\Models\Sign;
use App\Models\Audio;
use App\Models\Video;
use App\Models\Capture;
use App\Models\Passport;
use App\Models\Guest;
use App\Models\Validation;
use App\Models\Stamp;
use App\Models\FormTemplate;

/**
 * Traits
 */

use App\Http\Controllers\Traits\HasVisits;

/**
 * Enumeraciones
 */

use App\Enums\ValidationType;

/**
 * Fikrea
 */

use Fikrea\ModelAndView;
use Fikrea\AppStorage;
use Fikrea\FaceRecognition;
use Fikrea\Ocr;

/**
 * Fikrea
 */

use Fikrea\Uuid;

/**
 * Excepciones requeridas
 */

use Fikrea\Exception\DocumentTooBigException;
use Fikrea\Exception\OcrException;

/**
 * Eventos lanzados
 */

use App\Events\SignerValidationDone;

/**
 * Proceso de firma del documento
 */

use App\Jobs\SignDocument;
use App\Jobs\TextboxsInDocument;
use App\Models\FormData;

/**
 * Creación de archivos Zip al vuelo
 *
 * @link https://github.com/stechstudio/laravel-zipstream
 *
 * @example
 *
 * use Zip;
 *
 * Zip::create('package.zip')
 *     ->add('/path/to/some-file.pdf')
 *     ->add('/path/to/data.xlsx', 'export.xlsx')
 *     ->add('/path/to/log.txt', 'log/details.txt');
 */

use Zip;
use STS\ZipStream\ZipStream;

/**
 * PDF
 */

use PDF;

/**
 * DomPDF
 */

use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;

/**
 * Trait UserDeviceTrait
 */

use App\Http\Controllers\Traits\UserDeviceTrait;
use App\Models\DocumentSharing;
use App\Models\File;
use App\Models\Textbox;
use Illuminate\Support\Facades\File as FacadesFile;

class DocumentController extends Controller
{
    /**
     * Trait para el procesamiento de los documentos
     */
    use DocumentProcess;

    /**
     * Trait para el control de las visitas de los firmantes a las validaciones
     */
    use HasVisits;

    /**
     * Trait para la detección del dispositivo del usuario usado en la conección
     */
    use UserDeviceTrait;

    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Muestra la vista para crear on editar un documento de texto simple
     * Se trata de un documento no subido sino editado manualmente
     *
     * @param int|null $id El id del documento o
     *                     null para crear un documento nuevo
     *
     * @return string                           Una vista
     */
    public function edit(?int $id = null): string
    {
        // Si se está visualizando un documento ya existente
        if ($id) {
            // Obtenemos el documento
            $document = Document::findOrFail($id);

            // Comprobamos si el usuario actual puede visualizarlo
            $this->authorize('view', $document);
        }

        $mav = new ModelAndView('dashboard.edit-document');

        $ocrMimeTypes = json_encode(
            MediaType::where('media_types.can_apply_ocr', 1)->get('media_type')->pluck('media_type'),
            JSON_UNESCAPED_SLASHES
        );

        return $mav->render(
            [
                'document' => $document ?? null,
                'ocrMimeTypes' => $ocrMimeTypes,
            ]
        );
    }

    /**
     * Guarda un documento
     *
     * Si el documento ha sido creado manualmente, se puede cambiar su contenido,
     * además de su nombre y comentarios
     *
     * Para un archivo subido, no se puede alterar su contenido aunque si su nombre
     * y comentarios
     *
     * @return RedirectResponse                 Un redirección
     */
    public function save(): RedirectResponse
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        // Valida la entrada
        $data = request()->validate(
            [
                'id'        => 'integer|nullable',
                'name'      => 'string|required|max:255',
                'comment'   => 'string|nullable|max:65535',
                'content'   => 'string|nullable|max:65535',
            ]
        );

        //
        // Si el documento posee id asignado y ha sido subido al servidor
        //

        if ($data['id']) {
            // Obtenemos el documento
            $document = Document::findOrFail($data['id']);

            if ($document->hasBeenUploaded) {
                // Obtiene la extensión del archivo
                $extension = (new \SplFileInfo($document->name))->getExtension();

                // Fija el nuevo nombre y los comentarios
                $document->name     = "{$data['name']}.{$extension}";
                $document->comment  = $data['comment'];
                $document->workspace_statu_id = \App\Enums\WorkspaceStatu::PENDIENTE;

                // Guarda el archivo
                $document->save();

                // Envía la respuesta
                return redirect()->route('dashboard.document.list')
                    ->with('message', Lang::get('El documento se ha guardado con éxito'));
            }
        }

        //
        // Si es un documento nuevo o es un documento existente que fue creado manualmente y se está editando
        //

        // Crear un nuevo documento PDF con el contenido del documento
        $dompdf = new Dompdf;
        $dompdf->loadHtml($data['content']);

        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        // Si no existía el documento
        if (!$data['id']) {
            // El nombre del archivo PDF que se ha generado
            $filename = config('documents.folder.original') . '/' . Str::random(40) . '.pdf';

            // El nombre del archivo PDF que se generará en el proceso de firma
            $signed = Str::of($filename)->replace(
                config('documents.folder.original'),
                config('documents.folder.signed'),
            );
            // Guardar el documento PDF
            Storage::disk(env('APP_STORAGE'))->put($filename, $dompdf->output());

            // Crea un documento nuevo
            $user->documents()->create(
                [
                    'name'              => "{$data['name']}.pdf",                     // El nombre del documento
                    // con su extensión
                    'comment'           => $data['comment'],                          // Los comentarios
                    'content'           => $data['content'],                          // El contenido del documento
                    'guid'              => Uuid::create(),                            // El GUID del documento
                    'original_path'     => $filename,                                 // El archivo PDF
                    // Hash del archivo original
                    'original_md5'      => md5(Storage::disk(env('APP_STORAGE'))->get($filename)),
                    'original_sha1'     => sha1(Storage::disk(env('APP_STORAGE'))->get($filename)),

                    'converted_path'    => $filename,                                 // El mismo archivo PDF
                    'signed_path'       => $signed,                                   // La ruta del archivo firmado
                    // El tipo Mime
                    'type'              => Storage::disk(env('APP_STORAGE'))->mimetype($filename),
                    // El tamaño del archivo
                    'size'              => Storage::disk(env('APP_STORAGE'))->size($filename),
                ]
            );
        } else {
            // Guarda el documento existente
            $document = Document::findOrFail($data['id']);

            // Guardar el documento PDF
            // Sustituyendo el contenido anterior por el actual
            $filename = $document->original_path;

            Storage::disk(env('APP_STORAGE'))->put($filename, $dompdf->output());

            $document->name          = "{$data['name']}.pdf";
            $document->comment       = $data['comment'];
            $document->content       = $data['content'];
            $document->original_md5  = md5(Storage::disk(env('APP_STORAGE'))->get($filename));
            $document->original_sha1 = sha1(Storage::disk(env('APP_STORAGE'))->get($filename));

            $document->save();
        }

        // Envía la respuesta
        return redirect()->route('dashboard.document.list')
            ->with('message', Lang::get('El documento se ha guardado con éxito'));
    }

    /**
     * Obtiene el texto de un archivo subido utilizando OCR (Reconocimiento óptico de carácteres)
     *
     * @return JsonResponse                     Una respuesta HTTP con el texto que ha sido reconocido
     */
    public function ocr(): JsonResponse
    {
        // Obtiene los tipos mime admitidos por OCR
        $mimes = MediaType::where('can_apply_ocr', 1)->get('media_type')->pluck('media_type')->toArray();

        // Obtiene el tamaño máximo que se puede procesar por OCR en kilobytes
        $maxSize = intval(config('ocr.max.size') * 1024);

        // Valida el archivo subido en cuanto al tipo de archivo admitido y su tamaño
        request()->validate(
            [
                'file' => "required|mimeTypes:{$mimes}|max:{$maxSize}",
            ]
        );

        // Obtiene la ruta absoluta del archivo temporal subido
        $file = request()->file('file')->getRealPath();

        // Se efectúa el reconocimiento óptico del archivo en el idioma actual
        // Si no se ha instalado los datos de entrenamiento para ese idioma se usará el idioma por defecto
        $ocr = new Ocr($file, app()->getLocale());

        try {
            $text = $ocr->run();
            return response()->json(['text' => $text]);
        } catch (OcrException $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Obtiene, para su descarga, el documento dado en su formato original
     *
     * @param int $id El id del documento
     *
     * @return StreamedResponse                 Un stream con el documento a descargar
     */
    public function download(int $id): StreamedResponse
    {
        // Obtenemos el documento
        $document = Document::findOrFail($id);

        // Comprueba si el usuario actual puede descargar el documento
        $this->authorize('download', $document);

        return Storage::disk(env('APP_STORAGE'))->download($document->original_path, $document->name);
    }

    /**
     * Obtiene el documento en formato PDF
     *
     * El documento, con independencia de su formato original, ya fue convertido con anterioridad
     * Si se dispone del token de acceso del firmante al documento, este puede ser descargado
     *
     * @param int|string $token El id del documento o el token de acceso
     *
     * @return StreamedResponse                 Un stream
     */
    public function pdf($token): StreamedResponse
    {
        // Si el argumento es un valor numérico, se trata del id del documento a descargar
        if (is_numeric($token)) {
            // El id del documento es el argumento del método
            $id = $token;

            // Obtenemos el documento
            $document = Document::findOrFail($id);

            // Comprueba si el usuario actual puede descargar el documento
            $this->authorize('download', $document);
        } else {
            // En caso contrario, se intenta obtener por el token
            // En tal caso, se omite la autorización de seguridad, pues la seguridad es el propio token
            $document = Document::findByToken($token);
        }

        return Storage::disk(env('APP_STORAGE'))->response($document->converted_path);
    }

    /**
     * Muestra la vista con la lista de documentos existentes
     *
     * @return string                           Una vista
     */
    public function list(): string
    {
        // Obtenemos el usuario
        $user = Auth::user() ?? Guest::user();

        // El listado de documentos no eliminados
        $documents = $user->documents()
            ->where('purged', false);

        // Muestra la lista de documentos no eliminados del usuario
        $mav = new ModelAndView('dashboard.documents.document-list');

        return $mav->render(
            [
                // Documentos paginados según configuración
                'documents' => $documents->paginate(config('documents.pagination')),
                // Los documentos mostrados son seleccionables uno a uno en la tabla
                'selection' => true,
                // El espacio ocupado por archivos y documentos
                'diskSpace' => $user->diskSpace,
            ]
        );
    }

    /**
     * Muestra la vista con la lista de documentos enviados
     *
     * @return string                           Una vista
     */
    public function sent(): string
    {
        // Obtenemos el usuario
        $user = Auth::user() ?? Guest::user();

        // El listado de documentos enviados
        $documents = $user->documents()
            ->where('sent', true)
            ->where('purged', false);

        // Muestra la lista de documentos enviados
        $mav = new ModelAndView('dashboard.documents.document-list');

        return $mav->render(
            [
                // Documentos paginados según configuración
                'documents' => $documents->paginate(config('documents.pagination')),
                // El espacio ocupado por archivos y documentos
                'diskSpace' => $user->diskSpace,
            ]
        );
    }

    /**
     * Muestra la vista con la lista de documentos eliminados
     *
     * @return string                           Una vista
     */
    public function removed(): string
    {
        // Obtenemos el usuario
        $user = Auth::user() ?? Guest::user();

        // El listado de documentos eliminados
        $documents = $user->documents()
            ->where('purged', true);

        // Muestra la lista de documentos no eliminados del usuario
        $mav = new ModelAndView('dashboard.documents.document-list');

        return $mav->render(
            [
                // Documentos paginados según configuración
                'documents' => $documents->paginate(config('documents.pagination')),
                // Los documentos mostrados son seleccionables uno a uno en la tabla
                'selection' => true,
                // El espacio ocupado por archivos y documentos
                'diskSpace' => $user->diskSpace,
            ]
        );
    }

    /**
     * Elimina un documento
     * Lo envía a la papelera de reciclaje
     *
     * @return void
     */
    public function delete(): void
    {
        $documents = request()->input('documents');

        // Eliminamos cada uno de los documentos
        foreach ($documents as $documentId) {
            $document = Document::findOrFail($documentId);

            // Comprueba si el usuario actual puede eliminar
            $this->authorize('delete', $document);

            // Marcamos el documento como eliminado
            $document->purge();
        }
    }

    /**
     * Elimina uno o más documentos
     *
     * @return void
     */
    public function destroy(): void
    {
        $documents = request()->input('documents');

        // Eliminamos cada uno de los documentos
        foreach ($documents as $documentId) {
            $document = Document::findOrFail($documentId);

            // Comprueba si el usuario actual puede eliminar
            $this->authorize('delete', $document);

            // Elimina los archivos físicos original, convertido y el firmado
            Storage::disk(env('APP_STORAGE'))->delete(
                [
                    $document->original_path,       // El archivo original
                    $document->converted_path,      // El archivo convertido (PDF)
                    $document->signed_path,         // El archivo firmado
                ]
            );

            // Elimina el documento de forma definitiva de la base de de datos
            $document->remove();
        }
    }

    /**
     * Recupera un documento que fue eliminado con anterioridad
     *
     * @param int $id El id del documento a recuperar
     *
     * @return RedirectResponse                 Una redirección
     */
    public function recover(int $id): RedirectResponse
    {
        // Obtenemos el documento a recuperar
        $document = Document::findOrFail($id);

        // Comprueba si el usuario actual puede recuperar
        $this->authorize('recover', $document);

        // Se recupera el documento de la papelera
        $document->restore();

        // Redirigir a la lista de documentos
        return redirect()->back()
            ->with('message', Lang::get('El documento se ha recuperado con éxito'));
    }

    /**
     * Muestra la vista para seleccionar las personas firmantes del documento
     *
     * Debe definirse, como mínimo una persona firmante, cuyos datos pueden provenir
     * de la lista de contactos guardados (Mis Contactos) o no
     *
     * @param int $id El id del documento
     *
     * @return string                           Una vista
     */
    public function signers(int $id): string
    {
        // Obtenemos el documento
        $document = Document::findOrFail($id);

        // Verifica si el usuario actual está autorizado a configurar la firma del documento
        $this->authorize('config', $document);

        $mav = new ModelAndView('dashboard.config.select-signers');

        return $mav->render(
            [
                'document' => $document,
            ]
        );
    }

    /**
     * Obtiene los firmantes de un documento
     *
     * @param int $id El id del documento
     *
     * @return RedirectResponse                 Un redirección
     */
    public function getSigners(int $id): JsonResponse
    {
        // Obtenemos el documento
        $document = Document::findOrFail($id);

        // Verifica si el usuario actual está autorizado a configurar la firma del documento
        $this->authorize('config', $document);

        // Devolvemos los firmantes que no son el propio autor del documento
        $signers = $document->signers()->where('creator', '=', false)->get();

        return response()->json($signers);
    }

    /**
     * Guarda los firmantes de un documento
     *
     * @param Request $request La solicitud
     * @param int     $id      El id del documento
     *
     * @return RedirectResponse                 Un respuesta JSON
     */
    public function saveSigners(Request $request, int $id): JsonResponse
    {
        // Obtenemos el usuario actual
        $user = Auth::user() ?? Guest::user();

        // Obtenemos el documento
        $document = Document::findOrFail($id);

        // Verifica si el usuario actual está autorizado a configurar la firma del documento
        $this->authorize('config', $document);

        // Obtenemos los firmantes
        $signers = $request->input('signers');

        // Obtenemos las direcciones de correo de los firmantes ya existentes
        $registerEmails = $document->signers->map(fn ($signer) => $signer->email)->toArray();

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
        foreach ($document->signers as $signer) {
            if (!in_array($signer->email, $sendedEmails)) {
                $signer->delete();
            }
        }

        // Añade sólo los firmantes nuevos, es decir, que no estaban en la lista de firmantes
        // Para cada firmante se genera un token de acceso
        foreach ($signers as &$signer) {
            $signer['token'] = Str::random(64);
        }

        $signers = $document->signers()->createMany($signers);

        // Se crea el proceso para cada firmante
        foreach ($signers as $signer) {
            $signer->process()->create([]);
        }

        // Añade al creador como firmante si no estaba añadido previamente
        $document->addCreatorAsSigner($user);

        return response()->json(Lang::get('Firmantes guardados con éxito'));
    }

    /**
     * Muestra la vista de validaciones del documento
     *
     * @param int $id El id del documento
     *
     * @return string                           Una vista
     */
    public function validations(int $id): string
    {
        // Obtenemos el documento
        $document = Document::findOrFail($id);

        // Verifica si el usuario actual está autorizado a configurar la firma del documento
        $this->authorize('config', $document);

        $mav = new ModelAndView('dashboard.config.select-validations');

        return $mav->render(
            [
                'document'          => $document,   // el documento
                'validations'       => config('validations.document-validations'), // las validaciones permitidas
                'notAvailableMsj'   => \Lang::get('NO está disponible para el creador del proceso'),
                'notAvailableIcon'  => '<i class="fas fa-ban fa-2x text-danger"></i>',  // icono a renderizar
            ]
        );
    }

    /**
     * Guarda las validaciones del documento
     *
     * @param int $id El id del documento
     *
     * @return JsonResponse                         Una respuesta JSON
     */
    public function saveValidations(int $id): JsonResponse
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        // Obtenemos el documento
        $document = Document::findOrFail($id);

        // Verifica si el usuario actual está autorizado a configurar la firma del documento
        $this->authorize('config', $document);

        // Obtiene las validaciones seleccionadas
        $validations = array_filter(request()->input('validations'), fn ($validation) => $validation['selected']);

        // Elimina todas las validaciones anteriores
        $document->validations()->delete();

        // Guarda las validaciones de un documento
        // y creo el proceso correspondiente a cada validación
        $document->validations()->createMany($validations)->each(
            function ($validation) {
                $validation->process()->create([]); // El proceso correspondiente
            }
        );

        // Las validaciones de edición de documento
        $documentEditorValidations = array_filter(
            $validations,
            fn ($validation) => $validation['validation'] == ValidationType::TEXT_BOX_VERIFICATION
        );

        // Las validaciones de firma manuscrita
        $handWrittenSignatureValidations = array_filter(
            $validations,
            fn ($validation) => $validation['validation'] == ValidationType::HAND_WRITTEN_SIGNATURE
        );

        // Las validacion de formulario de datos especificos
        $formDataValidations = array_filter(
            $validations,
            fn ($validation) => $validation['validation'] == ValidationType::FORM_DATA_VERIFICATION
        );

        // Las validaciones de Solicitud de Documentos
        $documentsRequestValidations = array_filter(
            $validations,
            fn ($validation) => $validation['validation'] == ValidationType::DOCUMENT_REQUEST_VERIFICATION
        );

        //
        // Si no hay que realizar validaciones de editor de documentos, ni de firma manuscrita,
        // ni de certificación de datos ni de solicitud de documentos
        // se notifica a los firmantes y se marca el documento como enviado
        if (
            !$documentEditorValidations
            && !$handWrittenSignatureValidations
            && !$documentsRequestValidations
            && !$formDataValidations
        ) {
            // Notificar a los firmantes que no sean el creador/autor del documento
            // Se envía un email/SMS a cada firmante con un enlace a su espacio de usuario
            $document->signers->filter(fn ($signer) => !$signer->creator)->each(
                function ($signer) use ($user) {
                    if ($signer->email) {
                        // Si se ha proporcionado el correo del firmante se notifica por email
                        EmailController::sendWorkSpaceAccessEmail($user, $signer);
                    } elseif ($signer->phone) {
                        // Si no se ha proporcionado un correo, pero si su teléfono, se notifica por SMS
                        SMSController::sendWorkSpaceAccessSms($user, $signer);
                    }
                }
            );

            // Se marca el documento como enviado
            $document->send();

            // Se envía un correo al creador/autor del documento confirmando que
            // ha compartido un documento
            EmailController::confirmDocumentShared($document->user, $document);

            // Se registra una nueva compartición de Documento
            $document->sharings()->create(
                [
                    'signers' => json_encode(
                        [
                            'signers' => $document->signers
                                ->filter(fn ($signer) => !$signer->creator)
                                ->map(fn ($signer) => $signer->id)
                        ]
                    ),
                    'type'  => 1,
                ]
            );
        }

        // Si hay que realizar alguna de las validaciones anteriorres
        // se notificará tras finalizar con la config del ultimo proceso
        return response()->json(['code' => 1, 'validations' => $validations]);
    }

    /**
     * Muestra la página de configuración de la firma del documento
     *
     * @param int $id El id del documento a firmar
     *
     * @return string                           Una vista
     */
    public function prepare(int $id): string
    {
        // Obtenemos el documento
        $document = Document::findOrFail($id);

        // Verifica si el usuario actual está autorizado a configurar la firmar del documento
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

        // Muesta la vista para la firma del documento
        $mav = new ModelAndView('dashboard.documents.config.config-sign');

        // Se seleccionan como firmantes únicamente aquellos para los que se ha elegido
        // la validación mediante el procedimiento de firma manuscrita
        $signers = $document->signers->filter(
            fn ($signer) => $signer->mustValidate($document, ValidationType::HAND_WRITTEN_SIGNATURE)
        )->values();

        // Obtiene los sellos de la librería predeterminada que se ofrece a los usuarios
        // para el idioma correspondiente
        $stamps = Stamp::library(app()->getLocale());

        $stampMimeTypes = json_encode(
            MediaType::where('type', 'image')->get('media_type')->pluck('media_type'),
            JSON_UNESCAPED_SLASHES
        );

        return $mav->render(
            [
                'document' => $document,           // El documento
                'signers' => $signers,            // Los firmantes del documento
                'stamps' => $stamps,             // Una colección de sellos de la librería predeterminada
                'stampMimetypes' => $stampMimeTypes,
            ]
        );
    }


    /**
     * Muestra la página de configuración de las cajas de texto sobre el documento
     *
     * @param int $id El id del documento a firmar
     *
     * @return string                           Una vista
     */
    public function textBoxs(int $id): string
    {
        // Obtenemos el documento
        $document = Document::findOrFail($id);

        // Verifica si el usuario actual está autorizado a configurar la firmar del documento
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

        // Muesta la vista para la firma del documento
        $mav = new ModelAndView('dashboard.documents.config-texts.config-texts');

        // Se seleccionan como firmantes únicamente aquellos para los que se ha elegido
        // la validación mediante edición de cajas de texto
        $signers = $document->signers->filter(
            fn ($signer) => $signer->mustValidate($document, ValidationType::TEXT_BOX_VERIFICATION)
        )->values();

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
                'document'  => $document,           // El documento
                'signers'   => $signers,            // Los firmantes del documento
                'langs'     => $langs,              // Textos traducidos utilizados en vue js
            ]
        );
    }

    /**
     * Guarda la configuración de firma del documento
     *
     * El documento queda preparado para su firma por parte de los usuarios firmantes
     * que procederán a efectuar las firmas desde su espacio de trabajo o WorkSpace
     *
     * Se ha establecido un conjunto de puntos en el mismo donde los firmantes deben firmar
     * Se envía un correo/SMS a cada firmante
     *
     * @param Request $request La solicitud
     * @param int     $id      El id del documento a guardar
     *
     * @return JsonResponse                     Una respuesta JSON
     */
    public function saveConfigSignDocument(Request $request, int $id): JsonResponse
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        // Obtenemos el documento
        $document = Document::findOrFail($id);

        // Verifica si el usuario actual está autorizado a configurar la firma del documento
        /*
            // El documento debe ser del usuario y no haber sido ya enviado a firmar
            return $user && $user->id === $document->user_id && !$document->sent;
        */
        $this->authorize('config', $document);

        // Marca que el autor/creador del documento ha realizado la validación por firma manuscrita
        $validation = $document->validations
            ->where('validation', ValidationType::HAND_WRITTEN_SIGNATURE)
            ->filter(fn ($validation) => $validation->signer->creator == 1)
            ->first();
        if ($validation) {
            $validation->validated();
        }

        // Obtiene los ids (códigos identificadores únicos) de las firmas ya existentes
        $existSignCodes = $document->signs->map(fn ($sign) => $sign->code)->toArray();

        // Obtiene las firmas
        $signs = $request->input('signs');

        // Obtiene una lista con los ids de los firmantes del documento
        // para los que se exige firma manuscrita sobre el documento
        $signers = $document->validations->where('validation', ValidationType::HAND_WRITTEN_SIGNATURE)
            ->map(fn ($validation) => $validation->user)->toArray();
        sort($signers);

        // Obtiene los ids de las firmas que se han enviado
        $signCodes = array_map(fn ($sign) => $sign['id'], $signs);

        // Guarda las posiciones de cada una de las firmas del documento
        foreach ($signs as $sign) {
            // Se crean sólo las firmas que no existían previamente
            if (!in_array($sign['id'], $existSignCodes)) {
                $document->signs()->create(
                    [
                        'signer_id'     => $sign['signer']['id'],
                        'signer'        => $sign['signer']['name'],
                        'creator'       => $sign['signer']['creator'],
                        'page'          => $sign['page'],
                        'x'             => $sign['x'],
                        'y'             => $sign['y'],
                        'code'          => $sign['id'],
                        'sign'          => $sign['sign'],
                    ]
                );
            }

            // Si la firma ya existía y pertenece al creador/autor del documento se actualiza la firma
            if ($sign['code'] && $sign['signer']['creator']) {
                // Obtiene la firma guardada
                $signSaved = Sign::findByCode($sign['code']);

                // Actualiza la firma
                $signSaved->sign = $sign['sign'];

                // Consigna la fecha de firma
                $signSaved->signed     = $sign['sign'] != null;
                $signSaved->signDate   = $sign['sign'] ? new \DateTime : null;

                // Obtenemos la ip y el agente de usuario
                if ($sign['sign']) {
                    $signSaved->ip         = request()->ip();
                    $signSaved->user_agent = $request->server('HTTP_USER_AGENT');
                    $signSaved->device     = $this->getDevice();
                }

                $signSaved->save();
            }
        }

        // Las firmas que no se han enviado es que han sido quitadas del documento,
        // por lo que deben ser eliminadas
        foreach ($document->signs as &$sign) {
            if (!in_array($sign['code'], $signCodes)) {
                $sign->delete();
            }
        }

        // Obtiene los sellos que se han estampado sobre el documento
        $stamps = $request->input('stamps');

        // Guarda las posiciones de cada uno de los sellos que se han podido estampar sobre el documento
        foreach ($stamps as $stamp) {
            $document->stamps()->create(
                [
                    'stamp' => $stamp['stamp']['thumb'],
                    'page'  => $stamp['page'],
                    'x'     => $stamp['x'],
                    'y'     => $stamp['y'],
                ]
            );
        }

        /*
         * Si se ha seleccionado
         * verificación de datos (formulario) o solicitud de documentos
         * se debe configurar al terminar en ese orden...
         */
        if (
            $document->isRequestValidationConfigured()
            && !$document->mustBeValidateByFormData()
        ) {
            // Se marca el documento como enviado
            $document->send();

            // El documento puede contener firmas del propio autor/creador del documento que deben ser procesadas
            // Pone en cola la creación del documento firmado ya que es un proceso que puede demorar tiempo
            SignDocument::dispatch($document);

            // Se registra una compartición de Documento
            $document->sharings()->create(
                [
                    'signers' => json_encode(
                        [
                            'signers' => $document->signers->map(fn ($signer) => $signer->id)
                        ]
                    )
                ]
            );

            // Notificar a los firmantes que no sean el creador/autor del documento
            // Se envía un email/SMS a cada firmante con un enlace a su espacio de usuario
            $document->signers->filter(fn ($signer) => !$signer->creator)->each(
                function ($signer) use ($user) {
                    if ($signer->email) {
                        // Si se ha proporcionado el correo del firmante se notifica por email
                        EmailController::sendWorkSpaceAccessEmail($user, $signer);
                    } elseif ($signer->phone) {
                        // Si no se ha proporcionado un correo, pero si su teléfono, se notifica por SMS
                        SMSController::sendWorkSpaceAccessSms($user, $signer);
                    }
                }
            );

            // Se envía un correo al creador/autor del documento confirmando que
            // ha compartido un documento
            EmailController::confirmDocumentShared($document->user, $document);
        }

        // Envía la respuesta
        return response()->json(['code' => 1, 'document' => $document]);
    }

    /**
     * Guarda la configuración de cajas de textos del documento
     *
     * El documento queda preparado para el completamiento de las cajas de texto por
     * parte de los usuarios firmantes que procederán a completar las cajas desde
     * su espacio de trabajo o WorkSpace
     *
     * Se envía un correo/SMS a cada firmante
     *
     * @param Request $request La solicitud
     * @param int     $id      El id del documento a guardar
     *
     * @return JsonResponse                     Una respuesta JSON
     */
    public function saveConfigBoxsDocument(Request $request, int $id): JsonResponse
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        // Obtenemos el documento
        $document = Document::findOrFail($id);

        $this->authorize('config', $document);

        // Marca que el autor/creador del documento ha realizado la validación de editor de documento
        $validation = $document->validations
            ->where('validation', ValidationType::TEXT_BOX_VERIFICATION)
            ->filter(fn ($validation) => $validation->signer->creator == 1)
            ->first();
        if ($validation) {
            $validation->validated();
        }

        // Obtiene los ids (códigos identificadores únicos) de las cajas ya existentes
        $existBoxsCodes = $document->boxs->map(fn ($box) => $box->code)->toArray();

        // Obtiene las cajas de textos
        $textBoxs = $request->input('boxs');

        // Obtiene una lista con los ids de los firmantes del documento
        // para los que se exige caja de texto sobre el documento
        $signers = $document->validations->where('validation', ValidationType::TEXT_BOX_VERIFICATION)
            ->map(fn ($validation) => $validation->user)->toArray();
        sort($signers);

        // Obtiene los ids de las cajas que se han enviado
        $boxsCodes = array_map(fn ($box) => $box['id'], $textBoxs);

        // Guarda las posiciones de cada una de las cajas de texto del documento
        foreach ($textBoxs as $box) {
            // Se crean sólo las cajas que no existían previamente
            if (!in_array($box['id'], $existBoxsCodes)) {
                $document->boxs()->create(
                    [
                        'signer_id'     => $box['signer']['id'],
                        'signer'        => $box['signer']['name'],
                        'creator'       => $box['signer']['creator'],
                        'page'          => $box['page'],
                        'x'             => $box['x'],
                        'y'             => $box['y'],
                        'code'          => $box['id'],
                        'text'          => $box['text'],
                        'options'       => $box['options'],
                        'type'          => $box['type'],
                        'title'         => $box['title'],
                        'width'         => $box['width'],
                        //'height'        => $box['height'],
                        'shiftX'        => $box['shiftX'],
                        'shiftY'        => $box['shiftY'],
                        'fitMaxLength'  => $box['fitMaxLength'],
                        'rules'         => json_encode($box['rules']),
                    ]
                );
            }

            // Si la caja ya existía y pertenece al creador/autor del documento se actualiza el texto
            if ($box['code'] && $box['signer']['creator']) {
                // Obtiene la caja guardada
                $boxSaved = Textbox::findByCode($box['code']);

                // Actualiza la firma
                $boxSaved->text = $box['text'];

                // Consigna la fecha de firma
                $boxSaved->signed     = $box['text'] != null;
                $boxSaved->signDate   = $box['text'] ? new \DateTime : null;

                // Obtenemos la ip y el agente de usuario
                if ($box['text']) {
                    $boxSaved->ip         = request()->ip();
                    $boxSaved->user_agent = $request->server('HTTP_USER_AGENT');
                    $boxSaved->device     = $this->getDevice();
                }

                $boxSaved->save();
            }
        }

        // Las cajas que no se han enviado es que han sido quitadas del documento,
        // por lo que deben ser eliminadas
        foreach ($document->boxs as &$box) {
            if (!in_array($box['code'], $boxsCodes)) {
                $box->delete();
            }
        }

        // Este documento se debe enviar en caso de que no haya que configurar otro proceso como
        //      Firma manuscrita
        //      Certificacion de datos
        //      Solicitud de documentos
        if (
            $document->isRequestValidationConfigured()
            && !$document->mustBeValidateByFormData()
            && !$document->mustBeValidateByHandWrittenSignature()
        ) {
            // Se marca el documento como enviado
            $document->send();

            // El documento puede contener textos del propio autor/creador del documento que deben ser procesadas
            // Pone en cola la creación del documento con estos textos ya que es un proceso que puede demorar tiempo
            TextboxsInDocument::dispatch($document);

            // Se registra una compartición de Documento
            $document->sharings()->create(
                [
                    'signers' => json_encode(
                        [
                            'signers' => $document->signers->map(fn ($signer) => $signer->id)
                        ]
                    )
                ]
            );

            // Notificar a los firmantes que no sean el creador/autor del documento
            // Se envía un email/SMS a cada firmante con un enlace a su espacio de usuario
            $document->signers->filter(fn ($signer) => !$signer->creator)->each(
                function ($signer) use ($user) {
                    if ($signer->email) {
                        // Si se ha proporcionado el correo del firmante se notifica por email
                        EmailController::sendWorkSpaceAccessEmail($user, $signer);
                    } elseif ($signer->phone) {
                        // Si no se ha proporcionado un correo, pero si su teléfono, se notifica por SMS
                        SMSController::sendWorkSpaceAccessSms($user, $signer);
                    }
                }
            );

            // Se envía un correo al creador/autor del documento confirmando que
            // ha compartido un documento
            EmailController::confirmDocumentShared($document->user, $document);
        }

        // Envía la respuesta
        return response()->json(['code' => 1, 'document' => $document]);
    }

    /**
     * Obtiene la configuración de firma del documento
     *
     * Obtiene la lista de firmas que han sido establecidas en el documento
     *
     * Cada elemento representa la firma que debe realizar un firmante en una posición
     * determinada del documento
     *
     * @param string|int $token El id del documento o el token de
     *                          acceso del firmante al documento
     *
     * @return JsonResponse                     Una respuesta JSON con las firmas del documento
     */
    public function getConfigSignDocument($token): JsonResponse
    {
        // Si el argumento es un valor numérico, se trata del id del documento a descargar
        if (is_numeric($token)) {
            // El id del documento es el argumento del método
            $id = $token;

            // Obtenemos el documento
            $document = Document::findOrFail($id);

            // Verifica si el usuario actual está autorizado a configurar la firma del documento
            $this->authorize('config', $document);

            // Devuelve todas las firmas del documento
            return response()->json($document->signs);
        } else {
            // En caso contrario, se intenta obtener por el token
            // En tal caso, se omite la autorización de seguridad, pues la seguridad es el propio token
            $document = Document::findByToken($token);

            // Devuelve sólo las firmas del documento que debe firmar el usuario de token dado
            $signs = $document->signers->where('token', '=', $token)->first()->signs;

            return response()->json($signs);
        }
    }

    /**
     * Obtiene la configuración de las cajas de texto del documento
     *
     * Obtiene la lista de cajas de textos que han sido establecidas en el documento
     *
     * Cada elemento representa un texto que debe ser cumplimentado por un  firmante en una posición
     * determinada del documento
     *
     * @param string|int $token El id del documento o el token de
     *                          acceso del firmante al documento
     *
     * @return JsonResponse                     Una respuesta JSON con las cajas de texto del documento
     */
    public function getConfigBoxsDocument($token): JsonResponse
    {
        // Si el argumento es un valor numérico, se trata del id del documento a descargar
        if (is_numeric($token)) {
            // El id del documento es el argumento del método
            $id = $token;

            // Obtenemos el documento
            $document = Document::findOrFail($id);

            // Verifica si el usuario actual está autorizado a configurar la firma del documento
            $this->authorize('config', $document);

            // Devuelve todas las firmas del documento
            return response()->json($document->boxs);
        } else {
            // En caso contrario, se intenta obtener por el token
            // En tal caso, se omite la autorización de seguridad, pues la seguridad es el propio token
            $document = Document::findByToken($token);

            // Devuelve sólo las cajas de texto del documento que debe firmar el usuario de token dado
            $boxs = $document->signers->where('token', '=', $token)->first()->boxs;

            return response()->json($boxs);
        }
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
     * Obtiene el documento con cajas de texto
     *
     * SIN USAR AHORA MISMO
     * LOGICA UNIDA CON LAS FIRMAS EN 'signDocument'
     *
     * @param Document $document El documento
     *
     * @return string El nombre del documento con las cajas de texto
     *                o una vista de error
     * @throws DocumentTooBigException          El documento es demasiado grande
     */
    public function textboxsInDocument(Document $document): string
    {
        // Marcamos que el documento está siendo procesado en ese momento
        // ya que esta acción puede demorar tiempo
        $document->isBeingProcessed();

        // Descompone las páginas del documento en imágenes individuales
        $document->images = $this->getDocumentImages($document);

        // Obtenemos cada una de las cajas de texto del documento
        foreach ($document->boxs as $box) {
            // Si hay caja completada se añade al documento
            if ($box->text) {
                $this->addTextToDocument($box, $document);
            }
        }

        // Creamos el documento a partir de las imágenes independientes
        $documentSigned = $this->createSignedDocument($document);

        // Eliminar las imágenes utilizadas en el procesamiento
        $document->deleteImagesUsesInProcess();

        // Marcamos que el documento ha sido procesado
        $document->hasBeenProcessed();

        return $documentSigned;
    }

    /**
     * Obtiene el documento firmado, acompañado de los archivos que se han utilizado en la validación,
     * del cetificado generado y del archivo original (sin firmar)
     *
     * @param int $id El id del documento
     *
     * @return ZipStream|String                 Un stream con la descarga en zip
     *                                          o una vista de error
     */
    public function getSignedDocument(int $id)
    {
        // Obtenemos el documento
        $document = Document::findOrFail($id);

        // Chequeo que el archivo exista
        // Se puede dar el caso que cambie de almacenaminto y lo haya perdido
        if (!Storage::disk(env('APP_STORAGE'))->exists($document->signed_path)) {
            $mav = new ModelAndView('errors.custom');
            return $mav->render(
                [
                    'code'      => 510,
                    'title'     => Lang::get('El archivo que intenta descargar no se ha encontrado'),
                    'message'   => Lang::get('Puede que la configuración de su almacenamiento haya cambiado,
                                              por favor, confirme esta información e inténtelo más tarde'),
                ]
            );
        }

        $this->authorize('download', $document);

        // Si el archivo está siendo procesado
        if ($document->isInProcess()) {
            $mav = new ModelAndView('errors.custom');

            return $mav->render(
                [
                    'code'      => 510,
                    'title'     => Lang::get('El archivo está siendo procesado en este momento'),
                    'message'   => Lang::get('Espere a que el documento este preparado para su descarga'),
                ]
            );
        }

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
            info("El archivo firmado no existe... re-generandolo");
            $this->signDocument($document);
        }

        // Añade el archivo original
        $zip->add(
            AppStorage::path($document->original_path),
            "original/{$document->name}"
        );

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
                $zip->add(
                    AppStorage::path("{$captureFolder}/{$capture->path}"),
                    "captures/{$signerFolder}/{$capture->path}"
                );
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
        $certificate = PDF::loadView('dashboard.documents.pdf.certificate',
            [
                'document'      => $document,
            ]
        );

        // Lo añade al archivo zip
        $zip->addRaw(
            $certificate->download()->getOriginalContent(),
            "certificate-{$document->guid}.pdf"
        );

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

        // Genera la descarga del archivo zip
        return $zip;
    }

    /**
     * Obtiene una grabación de audio que valida un documento
     *
     * @param int $id El id de la grabación de audio
     *
     * @return StreamedResponse                 Un stream con la grabación de audio
     */
    public function getAudio(int $id): StreamedResponse
    {
        // Obtiene la grabación de audio
        $audio = Audio::findOrFail($id);

        $this->authorize('download', $audio);

        // Genera la descarga de la grabación de audio
        $audioFolder    = Str::lower(config('validations.audio.folder'));
        $audioExtension = config('validations.audio.file.extension');
        $audioPath      = "{$audioFolder}/{$audio->path}";

        // El nombre del archivo de audio en la descarga
        $appname  = config('app.name');
        $filename = "{$appname}-audio-{$audio->id}.{$audioExtension}";

        // Genera la descarga de la grabación de audio
        return Storage::disk(env('APP_STORAGE'))->download($audioPath, $filename);
    }

    /**
     * Obtiene una grabación de video que valida un documento
     *
     * @param int $id El id de la grabación de video
     *
     * @return StreamedResponse                 Un stream con la grabación de video
     */
    public function getVideo(int $id): StreamedResponse
    {
        // Obtiene la grabación de audio
        $video = Video::findOrFail($id);

        $this->authorize('download', $video);

        // Genera la descarga de la grabación de video
        $videoFolder    = config('validations.video.folder');
        $videoExtension = config('validations.video.file.extension');
        $videoPath      = "{$videoFolder}/{$video->path}";

        // El nombre del archivo de video en la descarga
        $appname  = Str::lower(config('app.name'));
        $filename = "{$appname}-video-{$video->id}.{$videoExtension}";

        // Genera la descarga de la grabación de video
        return Storage::disk(env('APP_STORAGE'))->download($videoPath, $filename);
    }

    /**
     * Obtiene una captura de pantalla que valida un documento
     *
     * @param int $id El id de la captura de pantalla
     *
     * @return StreamedResponse                 Un stream con la captura de pantalla
     */
    public function getScreen(int $id): StreamedResponse
    {
        // Obtiene la captura de pantalla
        $capture = Capture::findOrFail($id);

        $this->authorize('download', $capture);

        // Genera la descarga de la captura de pantalla
        $captureFolder    = config('validations.capture.folder');
        $captureExtension = config('validations.capture.file.extension');
        $capturePath      = "{$captureFolder}/{$capture->path}";

        // El nombre del archivo de video en la descarga
        $appname  = Str::lower(config('app.name'));
        $filename = "{$appname}-capture-{$capture->id}.{$captureExtension}";

        // Genera la descarga de la grabación de video
        return Storage::disk(env('APP_STORAGE'))->download($capturePath, $filename);
    }

    /**
     * Obtiene un documento identificativo como un pasaporte o documento nacional de identidad (ES)
     * que valida un documento
     *
     * @param int $id El id del documento
     *
     * @return ZipStream                        Un stream con el archivo Zip creado
     */
    public function getPassport(int $id): ZipStream
    {
        // Obtiene el documento identificativo
        $passport = Passport::findOrFail($id);

        $this->authorize('download', $passport);

        // Obtiene los nombres de los archivos que componen el documento identificativo, que son dos
        // El anverso y el reverso del mismo
        $identificationDocumentFolder    = config('validations.identification-document.folder');

        // Si hay captura de la imagen frontal del usuario
        if ($passport->user_image) {
            $userImagePath = "{$identificationDocumentFolder}/{$passport->user_image}";
        }

        // El anverso (front) y reverso (back) del documento identificativo
        $identificationDocumentFrontPath = "{$identificationDocumentFolder}/{$passport->front_path}";
        $identificationDocumentBackPath  = "{$identificationDocumentFolder}/{$passport->back_path}";

        // El nombre del archivo en la descarga, que es un zip que contiene los dos archivos
        $appname  = Str::lower(config('app.name'));
        $filename = "{$appname}-document-{$passport->id}.zip";

        // Crea un nuevo archivo zip
        $zip = Zip::create($filename);

        //
        // Añade los archivos al zip
        //

        // Si hay captura de la imagen frontal del usuario, la añadimos al archivo zip
        if ($passport->user_image) {
            $fileInfo = new \SplFileInfo($userImagePath);
            $zip->add(
                AppStorage::path($userImagePath),
                "user-image-capture.{$fileInfo->getExtension()}"
            );
        }

        // Incluye el anverso del documento
        $fileInfo = new \SplFileInfo($identificationDocumentFrontPath);

        $zip->add(
            AppStorage::path($identificationDocumentFrontPath),
            "document-front-image.{$fileInfo->getExtension()}"
        );

        // Incluye el reverso del documento
        $fileInfo = new \SplFileInfo($identificationDocumentBackPath);

        $zip->add(
            AppStorage::path($identificationDocumentBackPath),
            "document-back-image.{$fileInfo->getExtension()}"
        );

        // Genera la descarga del archivo zip
        return $zip;
    }

    /**
     * Muestra la vista con el estado de validación del documento
     *
     * Se muestra las validaciones realizadas y las pendientes de ser realizadas
     *
     * @param int $id El id del documento
     *
     * @return string                           Una vista
     */
    public function getValidationStatus(int $id): string
    {
        $mav = new ModelAndView('dashboard.documents.status.status');

        // Obtenemos el documento
        $document = Document::findOrFail($id);

        // Verifica si el usuario actual está autorizado a conocer el estado de validación del documento
        $this->authorize('status', $document);

        // Chequeo la actividad de sus firmantes
        foreach ($document->signers as $signer) {
            if (!$signer->hasPendingValidations() && $signer->active) {
                $signer->deactivate();
            }
        }

        return $mav->render(
            [
                'document'  => $document,
            ]
        );
    }

    /**
     * Envía una solicitud de firma de documento a aquellos firmantes que aún no han atendido
     * la solicitud de firma
     *
     * @param int $id El id del documento
     *
     * @return JsonResponse                     Un respuesta JSON
     */
    public function sendDocumentRequest(int $id): JsonResponse
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        // Obtenemos el documento
        $document = Document::findOrFail($id);

        // Verifica si el usuario actual está autorizado a ver el estado de validación de un documento
        $this->authorize('status', $document);

        // Obtenemos los usuarios únicos que aún no han realizado sus validaciones
        $signers = $document->validations
            ->where('validated', false)
            ->map(fn ($validation) => Signer::find($validation->user))
            ->unique();

        // Notificar a los firmantes que no sean el creador/autor del documento
        // Se envía un email/SMS a cada firmante con un enlace a su espacio de usuario
        $signers->filter(fn ($signer) => !$signer->creator)->each(
            function ($signer) use ($user) {
                if ($signer->email) {
                    // Si se ha proporcionado el correo del firmante se notifica por email
                    EmailController::sendWorkSpaceAccessEmail($user, $signer);
                } elseif ($signer->phone) {
                    // Si no se ha proporcionado un correo, pero si su teléfono, se notifica por SMS
                    SMSController::sendWorkSpaceAccessSms($user, $signer);
                }
            }
        );

        // Se registra una nueva compartición de Documento
        $document->sharings()->create(
            [
                'signers' => json_encode(
                    [
                        'signers' => $signers
                            ->filter(fn ($signer) => !$signer->creator)
                            ->map(fn ($signer) => $signer->id)
                    ]
                )
            ]
        );

        return response()->json($signers);
    }

    /**
     * Obtiene el certificado en PDF del proceso de validación del documento
     *
     * @param int $id El id del documento
     *
     * @return Response                         Una respuesta HTTP
     */
    public function certificate(int $id): Response
    {
        // Obtenemos el documento
        $document = Document::findOrFail($id);

        // Verifica si el usuario actual está autorizado para descargar el certificado de validación
        $this->authorize('certificate', $document);

        // Genera un archivo PDF, cargando la vista correspondiente
        $pdf = PDF::loadView(
            'dashboard.documents.pdf.certificate',
            [
                'document'      => $document,
            ]
        );

        // Genera la descarga del certificado
        return $pdf->download("certificate-{$document->guid}.pdf"); // stream para abrirse en el navegador
    }

    /**
     * Muestra la vista con el histórico de las visitas de los firmantes
     *
     * @param Document $document El documento
     *
     * @return string                           Una vista
     */
    public function getDocumentHistory(Document $document): string
    {
        // Verifica si el usuario actual está autorizado a visualizar el histórico de
        // firmantes del documento
        $this->authorize('history', $document);

        $mav = new ModelAndView('dashboard.documents.history.history');

        return $mav->render(
            [
                'document'          => $document,
            ]
        );
    }

    /**
     * Muestra la vista para que el autor/creador de un documento
     * realice una validación de audio
     *
     * @param int $id El id de la validación a audio a realizar
     *
     * @return string                           Una vista
     */
    public function audio(int $id): string
    {
        $validation = Validation::findOrFail($id);

        $this->authorize('validated', $validation->document);

        // Obtenemos el firmante que debe realizar la validación
        $signer = $validation->signer;

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
        $mav = new ModelAndView('dashboard.validations.audio');

        return $mav->render(
            [
                'signer'        => $signer,                                     // El firmante
                'visit'         => $signer->registerVisit(),                    // La visita
                'audioText'     => $audioText,      // El texto de referencia para la grabación
                'audioSample'   => $audioSample,    // El audio de ejemplo que sirve de guión a la grabación
            ]
        );
    }

    /**
     * Guarda la validación de audio de un autor/creador de documento
     *
     * @param int $id El id del usuario
     *
     * @return JsonResponse                     Una respuesta JSON
     */
    public function saveAudio(int $id): JsonResponse
    {
        // Obtiene el documento
        $document = Document::findOrFail($id);

        $this->authorize('validated', $document);

        $signer = $document->signers->filter(fn ($signer) => $signer->creator)->first();

        // Obtenemos la ip y el agente de usuario
        $ip         = request()->ip();
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
                $audio['user_id']       = $signer->document->user->id;
                $audio['signer_id']     = $signer->id;
                $audio['document_id']   = $signer->document->id;

                // Información adicional para la verificación del documento
                $audio['ip']            = $ip;
                $audio['user_agent']    = $user_agent;
                $audio['latitude']      = $position['latitude'];
                $audio['longitude']     = $position['longitude'];
                $audio['device']        = $this->getDevice();       // Dispositivo del firmante

                // Obtenemos el nombre y la ruta del archivo de audio en la carpeta de audios
                $audioFolder    = config('validations.audio.folder');
                $audioFile      = implode('.', [Str::random(64), config('validations.audio.file.extension')]);
                $audioPath      = "{$audioFolder}/{$audioFile}";

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
            ->validated();

        // Lanza los eventos relacionados con la realización de la validación
        event(new SignerValidationDone($validation));

        return response()->json($validation);
    }

    /**
     * Muestra la vista para que el autor/creador de un documento
     * realice una validación de video
     *
     * @param int $id El id de la validación a video a realizar
     *
     * @return string                           Una vista
     */
    public function video(int $id): string
    {
        $validation = Validation::findOrFail($id);

        $this->authorize('validated', $validation->document);

        // Obtenemos el firmante que debe realizar la validación
        $signer = $validation->signer;

        // Obtiene el texto que sirve de referencia para la locución que debe realizar el usuario
        // realizando las sustituciones oportunas
        $videoText = Str::of($signer->document->user->config->video->text)
            ->replace(':date', (new \DateTime)->format('d/m/Y'))
            ->replace(':name', $signer->name ?? ':name')
            ->replace(':lastname', $signer->lastname ?? ':lastname')
            ->replace(':doc', $signer->dni ?? ':doc');

        // Obtiene el ejemplo de video que puede utilizarse como referencia en esta validación
        $videoSample = $signer->document->user->config->video->sample;

        // Renderiza la vista de la página para realizar la grabación de video
        $mav = new ModelAndView('dashboard.validations.video');

        return $mav->render(
            [
                'signer'        => $signer,                                     // El firmante
                'visit'         => $signer->registerVisit(),                    // La visita
                'videoText'     => $videoText,      // El texto de referencia para la grabación
                'videoSample'   => $videoSample,    // El video de ejemplo que sirve de guión a la grabación
            ]
        );
    }

    /**
     * Guarda la validación de video de un autor/creador de documento
     *
     * @param int $id El id del usuario
     *
     * @return JsonResponse                     Una respuesta JSON
     */
    public function saveVideo(int $id): JsonResponse
    {
        // Obtiene el documento
        $document = Document::findOrFail($id);

        $this->authorize('validated', $document);

        $signer = $document->signers->filter(fn ($signer) => $signer->creator)->first();

        // Obtenemos la ip y el agente de usuario
        $ip         = request()->ip();
        $user_agent = request()->server('HTTP_USER_AGENT');

        // Obtenemos la posición del firmante datum WGS84
        $position = request()->input('position');

        // Actualizamos la visita realizada por el usuario
        $visit = request()->input('visit');
        $this->updateVisit($visit, $position);

        // Obtenemos la lista de grabaciones de video enviadas
        $videos = collect(request()->input('videos'));

        // Guardamos cada una de las grabaciones de video efectuadas
        $videos->each(
            function ($video) use ($signer, $ip, $user_agent, $position) {
                // Asocia la grabación de video al usuatio, al firmante y al documento
                $video['user_id']       = $signer->document->user->id;
                $video['signer_id']     = $signer->id;
                $video['document_id']   = $signer->document->id;

                // Información adicional para la verificación del documento
                $video['ip']            = $ip;
                $video['user_agent']    = $user_agent;
                $video['latitude']      = $position['latitude'];
                $video['longitude']     = $position['longitude'];
                $video['device']        = $this->getDevice();       // Dispositivo del firmante

                // Obtenemos el nombre y la ruta del archivo de video en la carpeta de videos
                $videoFolder    = config('validations.video.folder');
                $videoFile      = implode('.', [Str::random(64), config('validations.video.file.extension')]);
                $videoPath      = "{$videoFolder}/{$videoFile}";

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
            ->where('validation', '=', ValidationType::VIDEO_FILE_VERIFICATION)
            ->first()
            ->validated();

        // Lanza los eventos relacionados con la realización de la validación
        event(new SignerValidationDone($validation));

        return response()->json($validation);
    }

    /**
     * Muestra la vista para que el autor/creador de un documento
     * realice una validación mediante documento identificativo
     *
     * @param int $id El id de la validación mediante documento a realizar
     *
     * @return string                           Una vista
     */
    public function passport(int $id): string
    {
        $validation = Validation::findOrFail($id);

        $this->authorize('validated', $validation->document);

        // Obtenemos el firmante que debe realizar la validación
        $signer = $validation->signer;

        // Renderiza la vista de la página para realizar la validación mediante documento identificativo
        $mav = new ModelAndView('dashboard.validations.passport');

        return $mav->render(
            [
                'signer'        => $signer,                                     // El firmante
                'visit'         => $signer->registerVisit(),                    // La visita
                // Si se deben aplicar técnicas de reconocimiento
                // facial o no
                'useFacialRecognition'  =>
                $signer->document->user->config->identificationDocument->useFacialRecognition,
            ]
        );
    }

    /**
     * Guarda la validación por documento identificativo del autor/creador de documento
     *
     * @param int $id El id del usuario
     *
     * @return JsonResponse                     Una respuesta JSON
     */
    public function savePassport(int $id): JsonResponse
    {
        // Obtiene el documento
        $document = Document::findOrFail($id);

        $this->authorize('validated', $document);

        $signer = $document->signers->filter(fn ($signer) => $signer->creator)->first();

        // Obtenemos la ip y el agente de usuario
        $ip         = request()->ip();
        $user_agent = request()->server('HTTP_USER_AGENT');

        // Obtenemos la posición del firmante datum WGS84
        $position = request()->input('position');

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

            $imageInfo          = (object) getimagesize($userImage);
            $userImageExtension = explode('/', $imageInfo->mime)[1];
            $userImageFile      = implode('.', [Str::random(64), $userImageExtension]);
            $userImagePath      = "{$identificationDocumentsFolder}/{$userImageFile}";

            // Obtenemos los datos del archivo de imagen
            $userFileDecoded  = base64_decode(preg_replace('#data:[^;]+/[^;]+;base64,#', '', $userImage));

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
                $passport['user_id']       = $signer->document->user->id;
                $passport['signer_id']     = $signer->id;
                $passport['document_id']   = $signer->document->id;

                // Información adicional para la verificación del documento
                $passport['ip']            = $ip;
                $passport['user_agent']    = $user_agent;
                $passport['latitude']      = $position['latitude'];
                $passport['longitude']     = $position['longitude'];
                $passport['device']        = $this->getDevice();

                // Obtenemos la ruta de los archivos de documentos identificativos
                $identificationDocumentsFolder = config('validations.identification-document.folder');

                //
                // Para la imagen del anverso del documento
                //
                $imageInfo         = (object) getimagesize($passport['front']);
                $frontExtension    = explode('/', $imageInfo->mime)[1];
                $frontFile         = implode('.', [Str::random(64), $frontExtension]);
                $frontPath         = "{$identificationDocumentsFolder}/{$frontFile}";

                // Obtenemos los datos del archivo de imagen
                $frontFileDecoded   = base64_decode(preg_replace('#data:[^;]+/[^;]+;base64,#', '', $passport['front']));

                // Guarda el archivo de imagen
                Storage::disk(env('APP_STORAGE'))->put($frontPath, $frontFileDecoded);

                $passport['front_path'] = $frontFile;

                //
                // Para la imagen del reverso del documento
                //
                $imageInfo         = (object) getimagesize($passport['back']);
                $backExtension     = explode('/', $imageInfo->mime)[1];
                $backFile          = implode('.', [Str::random(64), $backExtension]);
                $backPath          = "{$identificationDocumentsFolder}/{$backFile}";

                // Obtenemos los datos del archivo de imagen
                $backFileDecoded  = base64_decode(preg_replace('#data:[^;]+/[^;]+;base64,#', '', $passport['back']));

                // Guarda el archivo de imagen
                Storage::disk(env('APP_STORAGE'))->put($backPath, $backFileDecoded);

                // Completa los datos del documento identificativo
                // con las rutas físicas de los archivos relativas a la carpeta de almacenamiento
                // de los documentos identificativos
                $passport['front_path'] = $frontFile;
                $passport['back_path']  = $backFile;

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
            ->validated();

        // Lanza los eventos relacionados con la realización de la validación
        event(new SignerValidationDone($validation));

        return response()->json($validation);
    }

    /**
     * Guarda un sello para ser estampado sobre un documento
     *
     * @return JsonResponse                     Una respuesta JSON
     */
    public function saveStamp(): JsonResponse
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        // Obtiene los datos del sello estampado
        $stampData = request()->input('stamp');

        // Obtiene la información de la imagen en miniatura del sello
        $stampInfo  = getimagesize($stampData['thumb']);

        $stampData['width']     = $stampInfo[0];
        $stampData['height']    = $stampInfo[1];
        $stampData['type']      = $stampInfo['mime'];

        // Guarda el sello
        $stamp = $user->stamps()->create($stampData);

        return response()->json($stamp);
    }

    /**
     * Elimina un sello para ser estampado sobre un documento
     *
     * @param int $id El id del elemento a eliminar
     *
     * @return JsonResponse                     Una respuesta JSON
     */
    public function deleteStamp(int $id): JsonResponse
    {
        // Obtiene el sello
        $stamp = Stamp::findOrFail($id);

        // Comprueba si el usuario actual puede eliminar el sello
        $this->authorize('delete', $stamp);

        // Elimina el sello
        $stamp->delete();

        return response()->json($stamp);
    }

    /**
     * Devuelve vista para aportar los documentos requeridos por parte del creador de un documento
     *
     * @param int $id El id de la validación
     *
     * @return string
     */
    public function request($id): string
    {
        $validation = Validation::findOrFail($id);

        $this->authorize('validated', $validation->document);

        // Obtenemos el firmante que debe realizar la validación
        $signer = $validation->signer;

        // Obtiene la solicitud de documentos
        $request = $signer->request();

        $token = $signer->token;

        // Si no hay solicitud de documentos para
        // el firmante o ya ha sido realizada termina
        if (!$request || $request->done()) {
            abort(404);
        }

        // Carga la vista para aportar la docucmentación requerida
        $mav = new ModelAndView('dashboard.requests.creator.request');

        // Aquí debo enviarle una estructura del sistema de archivos de fikrea para
        // cargar archivo desde la nube
        // Carpeta con archivos dentro
        // $fileSystem = [ ... ]

        $fileSystemTreeselect = [];

        // Confecciono mi arreglo según el formato que necesito en vue js para el componente Treeselect
        // @see https://vue-treeselect.js.org/#basic-features
        foreach (auth()->user()->files as $file) {
            if ($file->is_folder === 1) {
                // Para Treeselect
                $fileSystemTreeselect[] = [
                    'id'        =>  $file->id,
                    'label'     =>  $file->name,
                    'children'  =>  FileController::getFilesInFolderTreeselect($file)
                ];
            } else {
                // Para Treeselect
                $fileSystemTreeselect[] = [
                    'id'        =>  $file->id,
                    'label'     =>  $file->name,
                ];
            }
        }

        return $mav->render(
            [
                'token'     => $token,                  // El token de acceso
                'signer'    => $signer,                 // El firmante
                'request'   => $request,                // La solicitud de documentos
                'visit'     => $signer->registerVisit(), // La visita
                'validation' => $validation,             // La validacion del firmantes
                'fileSystemTreeselect' => $fileSystemTreeselect, // Sistema de archivos ordenado para Treeselect
                'fileSystem' => auth()->user()->files(null, false)->get(),          // Sistema de archivos ordenado
            ]
        );
    }

    /**
     * Muestra la vista donde se crea/selecciona el formulario de datos especificos
     *
     * @param integer $id                   El id del documento
     *
     * @return String                       Una vista html
     */
    public function formData(int $id): String
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        // Obtiene el documento
        $document = Document::findOrFail($id);

        // Verifica si el usuario actual está autorizado a ver el formulario de datos especificos
        $this->authorize('formData', $document);

        // Usuarios o "firmantes" solicitados para este documento y que cumplen con la validacion de datos
        // en este caso, que cumpla con la validacion de formulario de datos especifico
        $signers = $document->signersComplyWithValidation(ValidationType::FORM_DATA_VERIFICATION);

        // obtener Todas las plantillas de formularios
        $formTemplates = FormTemplate::all();

        // obtener plantillas del sistema
        $appFormTemplates = $formTemplates->whereNull('user_id')->groupBy('template_number');

        // obtener plantillas del creadas por el usuario
        $userFormTemplates = $formTemplates->where('user_id', $user->id)->groupBy('template_number');

        $validations = [];

        if ($document->mustBeValidateByDocumentRequest()) {
            $validations[]['DOCUMENT_REQUEST_VERIFICATION'] = ValidationType::DOCUMENT_REQUEST_VERIFICATION;
        }

        // renderizar una vista
        $mav = new ModelAndView('dashboard.validations.form-data');

        return $mav->render(
            [
                'document'          => $document,                                                   // el documento
                'signers'           => $signers,                                                    // el documento
                'appFormTemplates'  => $appFormTemplates,                                           // plantillas de formulario del sistema
                'userFormTemplates' => $userFormTemplates,                                          // plantillas de formulario del usuario
                'validations'       => $validations,                                                // Validaciones
                'characterTypes'    => config('validations.form-validations.character-types')       // tipos de caracteres para validacion del formulario
            ]
        );
    }

    /**
     * Guarda los formularios de datos asignados a lso firmantes
     *
     * @param int     $id                           El id del documento
     *
     * @return JsonResponse                         Una respuesta JSON
     */
    public function saveFormDataValidation(int $id): JsonResponse
    {
        // Obtiene el documento
        $document = Document::findOrFail($id);

        // Verifica si el usuario actual está autorizado a vmodificar el formulario de datos
        $this->authorize('formData', $document);

        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        // Elimina todos los formularios añadidos anteriormente
        $document->formdata()->delete();

        // Array de con la data recibida
        $request = request()->all();

        // Agregar usuario y documento al array
        foreach ($request['formDataValidate'] as $key => $value) {
            $request['formDataValidate'][$key]['user_id'] = $user->id;
        }

        // array con los datos agrupados para los formularios
        $groupFormData = $document->groupFormDataToBeSaved($request);

        // Validar si presiono guardar y continuar o solo presiono coninuar
        // Si presiono guardar y continuar se guarda en ambas tablas (FomTemplate y FomrData)
        // los campos correspondientes para cada uno
        DB::transaction(function () use ($groupFormData, $request, $user, $document) {

            foreach ($groupFormData as $clearFormData) {
                if ($request['saveAndContinue'] == 'true') {
                    $clearFormData = FormTemplate::getClearFormDataWithTemplateNumber($clearFormData, $user->id);

                    FormTemplate::saveMultipleFormTemplate($clearFormData);
                } else {
                    $clearFormData = FormData::getClearFormDataWithTemplateNumber($clearFormData, $user->id);
                }

                //  Almacenar el formulario de dato para el firmante
                $document->formdata()->createMany($clearFormData);
            }

            // Si no hay que realizar validaciones de solicitud de documentos,
            // se notifica a los firmantes y se marca el documento como enviado
            if (!$document->mustBeValidateByDocumentRequest()) {
                $this->sendNotificationOfDocumentToSigners($document, $user);
            }
        });

        return response()->json($document);
    }

    /**
     * Prepara un documento para ser compartido
     *
     * @return string                       Una vista
     */
    public function shareDocument(): string
    {
        $documents = collect();
        $dataSharing = request()->input('dataSharing');

        // es un id
        if (is_numeric($dataSharing)) {
            $document = Document::findOrFail($dataSharing);
            $documents->push($document);

            // Verifica si el usuario actual está autorizado ver el documento
            $this->authorize('view', $document);

            // es un array de datos
        } else {
            $ids = explode(',', $dataSharing);

            $documentsIn = Document::whereIn('id', $ids)->get();
            $documentsIn->each(fn ($document) => $documents->push($document));
        }

        // renderizar una vista
        $mav = new ModelAndView('dashboard.documents.shared.document-share');

        return $mav->render(['documents' => $documents]);
    }

    /**
     * Guarda y comparte los documentos a usuarios seleccioandos
     *
     * @param Request $request              Los datos recibidos
     * @return JsonResponse                 Una respuesta json
     */
    public function saveShareDocument(Request $request): JsonResponse
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        // Obtiene los documentos a compartir
        $documentsIds = array_map(fn ($document) => $document['id'], $request->input('documents'));

        // Obtiene los contactos con los cuales se realiza el proceso de compartición
        $contacts = $request->users ?? null;

        // Si no se han proporcionado documentos o usuarios
        if (!$documentsIds || !$contacts) {
            abort(404);
        }

        // verificar si existe el id del contacto, si existe en un contacto previamente guardado
        // sino es un contacto nuevo y se guardara el email
        $filterContacts = array_filter($contacts, fn ($contact) => isset($contact['email']));
        $jsonContacts = $filterContacts ? json_encode(['contacts' => array_map(fn ($contact) => $contact['email'], $contacts)])
            : json_encode(['contacts' => []]);

        // Este token se usa para que cualquier persona pueda acceder al archivo
        // token, titulo y descripcion
        $dataSharing = [
            'type'          => 1,
            'title'         => $request->title,
            'description'   => $request->description,
            'token'         => $request->get('token', Str::random(64)),
            'signers'       => $jsonContacts
        ];

        foreach ($documentsIds as $id) {

            // el documento
            $document = Document::findOrFail($id);

            // Se registra una nueva compartición de Documento
            $documentSharing = $document->sharings()->create($dataSharing);

            // Se envía un email/SMS a cada firmante con un enlace a su espacio de usuario
            foreach ($contacts as $contact) {

                // Asigna un token personalizado a cada contacto
                $contact['token'] = Str::random(64);

                // Crea el contacto para la compartición
                $documentSharingContact = $documentSharing->contacts()->create($contact);

                if ($contact['email']) {

                    // Si se ha proporcionado el correo del firmante se notifica por email
                    EmailController::sendDocumentSharingEmail($documentSharing, $documentSharingContact);
                } elseif ($contact['phone']) {

                    // Si no se ha proporcionado un correo, pero si su teléfono, se notifica por SMS
                    SmsController::sendDocumentSharingSms($user, $documentSharingContact);
                }
            }
        }

        return response()->json($dataSharing);
    }

    /**
     * Muestra un listado con las comparticiones de documentos del usuario
     *
     * @return string                           Una vista
     */
    public function listShareDocument(): string
    {
        // Obtiene el usuario actual
        $user = Auth::user() ?? Guest::user();

        // los documentos compartidos del usuario
        $userDocumentSharing = $user->documentSharing();

        // Obtener los documentos compartidos y filtrar por el id del documento compartido (DocumentSharing)
        $documentSharingIds = $userDocumentSharing->isNotEmpty() ? $userDocumentSharing->pluck(['id']) : null;

        // Obtener todos los documentos compartidos con su respectiva info, o null
        $documentSharings = $documentSharingIds ? DocumentSharing::whereIn('id', $documentSharingIds) : null;

        // Si se encuentran resultados se pagina, sino devuelve un array vacio
        $documentSharings = $documentSharings ? $documentSharings->paginate(config('files.pagination')) : [];

        // renderizar una vista
        $mav = new ModelAndView('dashboard.documents.shared.document-sharing-list');

        return $mav->render([
            'documentSharings' => $documentSharings,
        ]);
    }

    /**
     * Muestra un historico de descargas de las comparticiones del documento
     *
     * @param int     $id                       El id del documento compartido
     * @return string                           Una vista
     */
    public function historyShareDocument(int $id): string
    {
        // Obtiene la compartición de archivos
        $documentSharing = DocumentSharing::findOrFail($id);

        $this->authorize('history', $documentSharing->document);

        $mav = new ModelAndView('dashboard.documents.shared.document-sharing-history');

        return $mav->render([
            'documentSharing' => $documentSharing,
        ]);
    }

    /**
     * Guarda un documento original - firmado a la lista de archivos del usuario
     *
     * @return JsonResponse         Una respuesta json
     */
    public function copyToFiles(): JsonResponse
    {
        // Obtiene el usuario para la sesión actual
        $user = Auth::user() ?? Guest::user();

        // los datos recibidos en la peticion
        $dataDocument = collect(request()->dataDocument);

        // algun aviso de error en el proceso
        $info = false;

        // identificar si se han guardado todos los documentos o
        // solo los originales mediante un boolean (omitiendo los firmados)
        $infoSigned = false;

        // documentos no copiados
        $documentNotCopy = collect();

        // el path donde se deben copiar los documentos
        $copyPath = config('files.folder');

        foreach ($dataDocument as $data) {

            // el documento encontrado
            $doc = Document::findOrFail($data['idDocument']);

            // si no existe el documento seleccioando
            if (!$doc) {
                $info = true;
                return response()->json(['info' => Lang::get('El documento seleccionado no se ha encontrado')]);
            }

            // si no posee type (mimetype)
            if (!$doc->type) {
                $info = true;
                return response()->json(['info' => Lang::get('El documento posee errores y no puede ser guardado')]);
            }

            // si no puede ser firmado
            if (!$doc->onlyCanBeSigned()) {
                $infoSigned = true;
            }

            // si el tamaño maximo permitido es sobrepasado
            if (floatval($doc->size) > floatval(config('files.max.size'))) {
                $info = true;
                return response()->json(['info' => Lang::get('El documento seleccionado supera el tamaño máximo permitido')]);
            }

            // si es un documento firmable
            // se genera el documento firmado
            if ($doc->onlyCanBeSigned()) {
                // Comprueba si el archivo firmado existe, sino lo genera
                if (!Storage::disk(env('APP_STORAGE'))->exists($doc->signed_path)) {
                    $this->signDocument($doc);
                }
            }

            // si alguno de los dos documentos no existen
            // mientras pueda ser firmado
            if ($doc->onlyCanBeSigned()) {
                if (!$doc->originalDocumentAndSignedDocumentExist()) {
                    $info = true;
                    return response()->json(['info' => Lang::get('Uno de los documentos no existe o está siendo procesado, intente más tarde')]);
                }

            // Sino se verifica que solo el original exista
            }else{
                if (!$doc->originalDocumentExists()) {
                    $info = true;
                    return response()->json(['info' => Lang::get('El documento original no existe, no se puede guardar')]);
                }
            }

            // el nombre del documento original
            $originalDocumentName = $doc->originaldocumentname . '-' . $doc->name;

            // El nombre del documento pdf firmado
            $signedDocumentName = $doc->signeddocumentname . '-' . implode('.', [pathinfo($doc->name, PATHINFO_FILENAME), 'pdf']);

            // path al cual sera copiado los documentos
            $originalPath = $copyPath . '/' . $originalDocumentName;
            $signedPath = $copyPath . '/' . $signedDocumentName;

            // la carpeta donde se guardara dentro de files
            $parent = File::find($data['parentId']);
            $basePath = $parent ? ($parent->full_path ?? []) + [$parent->id => $parent->name] : null;

            // si no ocurrio algun aviso en el proceso
            if (!$info) {

                $copySigned = null;     // la copia del documento firmado

                // copiar los archivos en files
                $copyOriginal = Storage::disk(env('APP_STORAGE'))->copy($doc->original_path, $originalPath);

                // si en un documento firmable se copia el documento firmado
                // sino se ignora y solo se copia el original
                if($doc->onlyCanBeSigned()) {
                    $copySigned = Storage::disk(env('APP_STORAGE'))->copy($doc->signed_path, $signedPath);
                }

                // guardar el documento original
                if ($copyOriginal) {
                    $user->files()->create([
                        'name'      => $originalDocumentName,
                        'size'      => $doc->size,
                        'type'      => $doc->type,
                        'md5'       => md5($doc->original_path),
                        'path'      => $originalPath,
                        'token'     => Str::random(64),
                        'parent_id' => $data['parentId'],
                        'full_path' => $basePath,
                        'locked'    => false,
                    ]);
                }

                // guardar el documento firmado
                if ($copySigned) {
                    $user->files()->create([
                        'name'      => $signedDocumentName,
                        'size'      => $doc->size,
                        'type'      => 'application/pdf',
                        'md5'       => md5($doc->signed_path),
                        'path'      => $signedPath,
                        'token'     => Str::random(64),
                        'parent_id' => $data['parentId'],
                        'full_path' => $basePath,
                        'locked'    => false,
                    ]);
                }

                // si fue copiado algunos de los documentos
                if ($copyOriginal || $copySigned) {
                    // marca el documento como copiado
                    $doc->markAsCopied();

                // Sino fue copiado
                } else {
                    $documentNotCopy->push([$doc->name]);
                }
            }
        }

        // sino ocurrio algun aviso en el proceso
        // o algun documento no pudo ser copiado
        // o ningun documento pudo ser guardado se dan avisos
        if ($documentNotCopy->count()) {
            return response()->json([
                'failedProcess' => Lang::get('Algunos documentos no pudieron ser guardados, visite MIS ARCHIVOS para ver los documentos guardados'),
                'infoFailedProcess' => Lang::get('A continuación se muestra una lista de los documentos NO GUARDADOS'),
                'documentNoCopy' => $documentNotCopy
            ]);
        } else {
            if (!$info) {
                if (!$infoSigned) {
                    return response()->json([
                        'successProcess' => $dataDocument->count() > 1 ?
                            Lang::get('Los documentos originales y firmados han sido guardados en MIS ARCHIVOS') :
                            Lang::get('El documento original y el documento firmado han sido guardados en MIS ARCHIVOS')
                    ]);
                }else {
                    return response()->json([
                        'successProcess' => $dataDocument->count() > 1 ?
                            Lang::get('Los documentos originales han sido guardados en MIS ARCHIVOS') :
                            Lang::get('El documento original ha sido guardado en MIS ARCHIVOS')
                    ]);
                }
            }
        }
    }
}
