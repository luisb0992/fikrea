<?php

/**
 * FileController
 *
 * Controlador para la subida de archivos
 *
 * @author    javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers;

/**
 * Controladores requeridos
 */

use App\Exceptions\FileIsNotAFolderException;
use App\Http\Controllers\Signature\Traits\DocumentConversion;
use App\Models\FileLog;
use App\Utils\FileUtils;
use Fikrea\GeoIp;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PDF;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Modelos requeridos
 */

use App\Models\Guest;
use App\Models\File;
use App\Models\FileSharing;
use App\Models\FileSharingContact;
use Exception;

/**
 * Librerías de Fikrea
 */

use Fikrea\ModelAndView;
use Fikrea\AppStorage;
use Fikrea\Uuid;

/**
 * Excepciones requeridas
 */

use Fikrea\Exception\DocumentNotValidException;

/**
 * Creación de archivos Zip al vuelo
 *
 * @link https://github.com/stechstudio/laravel-zipstream
 */

use Throwable;
use Zip;
use STS\ZipStream\ZipStream;

/**
 * PDF Merge for PHP
 */

use Jurosh\PDFMerge\PDFMerger;
use Fikrea\PdfInfo;
use Howtomakeaturn\PDFInfo\PDFInfo as HPDFInfo;

class FileController extends Controller
{
    /**
     * Trait para la conversión de documentos al formato común PDF
     */
    use DocumentConversion;

    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Carga la vista para la subida de un archivo
     *
     * @param Request $request La solicitud
     * @return string          Una vista
     */
    public function upload(Request $request): string
    {
        $id = $request->id;

        $validator = Validator::make(
            [
                'folder' => $id
            ],
            [
                'folder' => [
                    'nullable',
                    Rule::exists('files', 'id')->where('is_folder', true),
                ],
            ]
        );

        if ($validator->fails()) {
            abort(Response::HTTP_BAD_REQUEST);
        }

        // Obtiene el usuario actual
        $user = Auth::user() ?? Guest::user();

        // Obtiene la carpeta a la que se suben los documentos
        $folder = File::find($id);

        $mav = new ModelAndView('dashboard.files.upload-files');

        return $mav->render(['diskSpace' => $user->diskSpace, 'folder' => $folder]);
    }

    /**
     * Guarda el archivo subido
     * Opcionalmente puede indicarse un ID que se corresponda con el de la carpeta en la que se quiere
     * almacenar el archivo subido.
     *
     * @param Request $request La solicitud
     * @return JsonResponse
     */
    public function save(Request $request): JsonResponse
    {
        // Obtiene el usuario para la sesión actual
        $user = Auth::user() ?? Guest::user();

        // Lee el tamaño máximo de archivo permitido en la configuración
        $size = config('files.max.size');

        if (!$request->file->getSize() || !$request->file->getMimeType()) {
            return response()->json(Response::HTTP_BAD_REQUEST);
        }

        // Valida la solicitud
        $data = $request->validate(
            [
                'file' => "max:{$size}",
                'id' => [
                    'nullable',
                    Rule::exists('files')->where('user_id', $user->id)->where('is_folder', true),
                ],
                'locked' => 'nullable',
            ]
        );

        // Obtiene el archivo a guardar
        $file = $data['file'];
        $dataLocked = $data['locked'] ?? false;
        $locked = 'true' === $dataLocked;

        $parent = File::find(request()->get('id'));
        $basePath = $parent ? ($parent->full_path ?? []) + [$parent->id => $parent->name] : null;

        // Guarda el archivo
        $file = $user->files()->create(
            [
                // Nombre del archivo original
                'name' => $file->getClientOriginalName(),
                // Tamaño del archivo
                'size' => $file->getSize(),
                // Tipo Mime del archivo
                'type' => $file->getMimeType(),
                // md5 del archivo
                'md5' => md5(file_get_contents($file->getRealPath())),
                // La ruta del archivo
                'path' => Storage::disk(env('APP_STORAGE'))->putFile(config('files.folder'), $file),
                // El token del archivo
                'token' => Str::random(64),
                // La carpeta hacia la que se sube el archivo
                'parent_id' => $request->id ?? null,
                'full_path' => $basePath,
                'locked' => $locked,
            ]
        );

        return response()->json($file);
    }

    /**
     * Carga la vista para listar los archivos
     *
     * @param int|null $count La cantidad de elementos a mostrar
     * @return string                           Una vista
     */
    public function list(Request $request, int $count = null): string
    {
        $id = $request->id;

        $validator = Validator::make(
            [
                'folder' => $id,
            ],
            [
                'folder' => [
                    'nullable',
                    Rule::exists('files', 'id')->where('is_folder', true),
                ],
            ]
        );

        if ($validator->fails()) {
            abort(Response::HTTP_BAD_REQUEST);
        }

        $user = Auth::user() ?? Guest::user();
        $folder = $id ? File::find($id) : null;

        // La paginación es la configurada por defecto en caso de que no se solicite
        $paginate = $count ?? config('files.pagination');

        // El listado de archivos paginados según la configuración, indicando además la carpeta en la que se encuentra
        $files = $user->files($id)->paginate($paginate)->appends(['id' => $id]);

        // Obtener información adicional de las carpetas
        foreach ($files as $file) {
            $this->extraInfo($file);
        }

        $mav = new ModelAndView('dashboard.files.files-list');

        return $mav->render(
            [
                'files' => $files,              // La lista de archivos del usuario
                'diskSpace' => $user->disk_space,    // El espacio ocupado por archivos y documentos
                'count' => $paginate,           // La cantidad de elementos que se muestran
                'folder' => $folder,             // El fichero que identifica a la carpeta; nulo, si es la raíz
            ]
        );
    }

    /**
     * Carga la vista para listar los archivos bloqueados
     *
     * @param int|null $count La cantidad de elementos a mostrar
     * @return string                           Una vista
     */
    public function locked(Request $request): string
    {
        $user = Auth::user() ?? Guest::user();

        // El listado de archivos paginados según la configuración, indicando además la carpeta en la que se encuentra
        $files = $user->lockedFiles()->get();

        $mav = new ModelAndView('dashboard.files.locked');

        return $mav->render(
            [
                'files' => $files,              // La lista de archivos del usuario
                'diskSpace' => $user->disk_space,    // El espacio ocupado por archivos y documentos
            ]
        );
    }

    /**
     * Obtener información acerca de los ficheros indicados
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function info(Request $request): JsonResponse
    {
        $user = Auth::user() ?? Guest::user();

        $ids = $request->selected;

        $files = $user->files()->whereIn('id', $ids)->get();

        $result = [];
        foreach ($files as $file) {
            // Adicionar la información complementaria que se requiere cuando es una carpeta
            $this->extraInfo($file);

            // Generar un arreglo con los resultados (los campos precisan ir en el formato correcto
            // en el que se mostrarán)
            $result[] = (object)[
                'id' => $file->id,
                'name' => $file->name,
                'type' => view('dashboard.partials.file-icon', ['type' => $file->type])->render(),
                'size' => $this->humanReadableSize($file->size),
                'created_at_date' => $file->created_at->format('d-m-Y'),
                'created_at_time' => $file->created_at->format('H:i'),
                'updated_at' => $file->id,
                'parent_id' => $file->parent_id,
                'is_folder' => $file->is_folder,
                'notes' => $file->notes,
                'full_path' => implode('/', $file->full_path ?? [Lang::get('PRINCIPAL')]),
                'locked' => $file->locked,
                'extra_data' => $file->extra_data
            ];
        }

        return response()->json($result);
    }

    /**
     * Obtener información acerca del fichero indicado
     * @param int $id
     * @return JsonResponse
     */
    public function singleFileInfo(int $id): JsonResponse
    {
        $file = File::find($id);

        // FIXME: ¿Alguna forma de chequeo de acceso al archivo?

        if (!Storage::disk('project_root')->exists('public/preview/' . $file->path)) {
            Storage::disk('project_root')->copy('storage/app/public/' . $file->path, 'public/preview/' . $file->path);
        }

        // Registrar acción
        FileLog::create(
            [
                'file_id' => $file->id,
                'action' => 'PREVISUALIZAR',
            ]
        );

        return response()->json(
            [
                'id' => $file->id,
                'type' => $file->type,
                'preview_as' => Str::before($file->type, "/"),
                'name' => $file->name,
                'path' => '/preview/' . $file->path,
            ]
        );
    }

    /**
     * Listado de archivos seleccionados para ejecutar una acción de selección múltiple
     *
     * @param Request $request
     * @return string
     */
    public function selected(Request $request): string
    {
        $user = Auth::user() ?? Guest::user();

        $mav = new ModelAndView('dashboard.files.selected');

        return $mav->render(
            [
                'diskSpace' => $user->disk_space,    // El espacio ocupado por archivos y documentos
            ]
        );
    }

    /**
     * Obtiene, para su descarga, el archivo dado
     *
     * @param int|string $id |$token El id o el token del archivo a descargar
     *
     * @return StreamedResponse|ZipStream
     * @throws AuthorizationException
     */
    public function download($id)
    {
        if (is_numeric($id)) {
            // Obtenemos el archivo por su id
            $file = File::findOrFail($id);

            // Verifica si el usuario actual está autorizado para descargar el archivo
            $this->authorize('download', $file);
        } else {
            // Obtenemos el archivo por su token
            $file = File::findByToken($id);
        }

        // Registrar la acción
        $route = route('file.download', ['id' => $id]);
        FileLog::create(
            [
                'file_id' => $id,
                'action' => 'DESCARGAR',
                'description' => sprintf(
                    'ENLACE: <a href="%s">%s</a>',
                    $route,
                    $route
                ),
            ]
        );
        // Si se está descargando una carpeta, hacerlo como un comprimido recreando toda la estructura de ficheros.
        if ($file->is_folder) {
            $zip = Zip::create("{$file->name}.zip");

            $files = FileUtils::getInnerFiles($file);

            foreach ($files as $item) {
                $zip->add(AppStorage::path($item->real_path), $item->full_name);
            }

            return $zip;
        }

        // Un fichero regular, descargarlo directamente.
        return Storage::disk(env('APP_STORAGE'))->download($file->path, $file->name);
    }

    /**
     * Descarga de múltiples archivos seleccionados, comprimidos como ZIP
     *
     * @param Request $request La solicitud
     * @return StreamedResponse   El comprimido con los archivos
     */
    public function downloadMultiple(Request $request): StreamedResponse
    {
        $zip = Zip::create("download-multiple.zip");

        // Obtiene la lista de archivos a descargar
        /** @var Collection $files */
        $files = File::find($request->input('files'));

        $selection = $files->pluck('name')->toArray();
        $files->each(
            function ($file) use ($zip, $selection) {
                // Registrar la acción para cada fichero en la selección
                FileLog::create(
                    [
                        'file_id' => $file->id,
                        'action' => 'DESCARGAR (MÚLTIPLE)',
                        'description' => sprintf('SELECCIÓN: %s', implode(', ', $selection)),
                    ]
                );

                $path = implode('/', $file->full_path ?? []);

                if ($file->is_folder) {
                    // Si es una carpeta, obtener los ficheros que contiene y agregarlos al
                    // comprimido respetando la estructura original
                    $innerFiles = FileUtils::getInnerFiles($file, $path . '/' . $file->name);

                    foreach ($innerFiles as $item) {
                        $zip->add(AppStorage::path($item->real_path), $item->full_name);
                    }
                } else {
                    // Si es un archivo, adicionar al comprimido
                    $zip->add(AppStorage::path($file->path), ($path ?? '.') . '/' . $file->name);
                }
            }
        );

        return $zip->response();
    }

    /**
     * Obtiene, para su descarga, el conjunto de archivos compartidos dado
     * comprimidos en formato ZIP
     *
     * @param int|string $id |$token El id o el token del conjunto a descargar
     *
     * @return ZipStream                        Un stream con el archivo a descargar
     * @throws AuthorizationException
     */
    public function downloadSet($id): ZipStream
    {
        if (is_numeric($id)) {
            // Obtenemos el archivo por su id
            $fileSharing = FileSharing::findOrFail($id);

            // Verifica si el usuario actual está autorizado para descargar el archivo
            $this->authorize('download', $fileSharing);
        } else {
            // Obtenemos el archivo por su token
            $fileSharing = FileSharing::findByToken($id);

            // Obtiene el contacto o destinatario por el token
            // o bien null si se ha utilizado el token genérico de acceso al conjunto de archivos compartidos
            $contact = FileSharingContact::findByToken($id);

            // Anota una visita a la descarga una visita a la descarga
            // Dejando constancia del momento en que se inicia la misma
            $fileSharing->histories()->create(
                [
                    'user_id' => $fileSharing->user_id,
                    'file_sharing_contact_id' => $contact->id ?? null,                      // El contacto
                    'ip' => request()->ip(),                           // La dirección IP
                    'user_agent' => request()->server('HTTP_USER_AGENT'),      // El agente de usuario
                    'starts_at' => null,
                    'downloaded_at' => now(),
                ]
            );
        }

        // Crea el archivo Zip al vuelo
        $zip = Zip::create("{$fileSharing->title}.zip");

        $fileSharing->files->each(
            function (File $file) use ($zip) {
                $path = implode('/', $file->full_path ?? []);

                if ($file->is_folder) {
                    $files = FileUtils::getInnerFiles($file, $path . '/' . $file->name);

                    foreach ($files as $item) {
                        $zip->add(AppStorage::path($item->real_path), $item->full_name);
                    }
                } else {
                    $zip->add(AppStorage::path($file->path), ($path ?? '.') . '/' . $file->name);
                }
            }
        );

        return $zip;
    }

    /**
     * Elimina un archivo
     *
     * @param int $id El id del archivo a eliminar
     *
     * @return RedirectResponse                 Una redirección
     * @throws FileIsNotAFolderException
     * @throws AuthorizationException
     */
    public function delete(int $id): RedirectResponse
    {
        // Obtenemos el archivo a eliminar
        $file = File::findOrFail($id);

        // Verifica si el usuario actual está autorizado a la eliminación del archivo
        $this->authorize('delete', $file);

        if ($file->is_folder) {
            // Recuperar todos los archivos contenidos en esta carpeta, descendiendo todos los niveles
            $files = $this->getNestedFiles($file);

            // Retornar todos las rutas e identificadores de ficheros a eliminar
            $paths = $files->pluck('path')->toArray();
            $ids = $files->pluck('id')->toArray();
        } else {
            // Retornar la ruta e identificador del único fichero a eliminar
            $paths = [$file->path];
            $ids = [$file->id];
        }

        // Eliminar los archivos físicamente y de la base de datos de archivos subidos
        Storage::disk(env('APP_STORAGE'))->delete($paths);
        DB::table('files')->whereIn('id', $ids)->delete();

        // Elimina aquellas comparticiones de archivos que no tiene archivos porque han sido ya eliminados
        $this->removeFileSharingsWithoutFiles();

        // Desbloquear archivos, si se liberó espacio suficiente
        FileUtils::unlockFiles();

        // Redirigir a la lista de archivos
        return redirect()->back()->with('message', Lang::get('El archivo se ha eliminado con éxito'));
    }

    /**
     * Elimina multiples archivos
     *
     * @return void
     */
    public function deleteMultiple(): JsonResponse
    {
        // Obtiene la lista de archivos a eliminar
        $files = File::find(request()->input('files'));

        $files->each(
            function ($file) {
                // Verifica si el usuario actual está autorizado a la eliminación del archivo
                $this->authorize('delete', $file);

                // Elimina el archivo físicamente y de la base de datos de archivos subidos
                Storage::disk(env('APP_STORAGE'))->delete($file->path);
                $file->delete();
            }
        );

        // Elimina aquellas comparticiones de archivos que no tiene archivos porque han sido ya eliminados
        $this->removeFileSharingsWithoutFiles();

        // Desbloquear archivos, si se liberó espacio suficiente
        FileUtils::unlockFiles();

        return response()->json($files);
    }

    /**
     * Dentro de una compartición de archivos, puede haber archivos que han sido eliminados por el usuario
     * Elimina las comparticiones para las cuales todos los archivos que las contiene han sido eliminados
     *
     * Este método se ejecuta tars la eliminación de archivos
     *
     * @return void
     */
    protected function removeFileSharingsWithoutFiles(): void
    {
        // Obtenemos el usuario
        $user = Auth::user() ?? Guest::user();

        // Dentro de una compartición de archivos, puede haber archivos que han sido eliminados por el usuario
        // Elimina las comparticiones para las cuales todos los archivos que las contiene han sido eliminados
        $user->fileSharings->filter(fn($fileSharing) => $fileSharing->numFiles == 0)->each(
            fn($fileSharing) => $fileSharing->delete()
        );
    }

    /**
     * Lleva un archivo al proceso de firma
     *
     * @param int $id El id del archivo
     *
     * @return RedirectResponse|String          Una redirección
     *                                          o una vista mostrando un error
     */
    public function sign(int $id)
    {
        // Obtiene el usuario actual
        $user = Auth::user() ?? Guest::user();

        // Obtenemos el archivo
        $file = File::findOrFail($id);

        // Verifica si el usuario actual está autorizado para generar la firma del archivo
        $this->authorize('sign', $file);

        // Convierte el archivo en un array para su procesamiento
        $file = $file->toArray();

        // Copia el archivo desde su carpeta a la carpeta de documentos originales
        $file['original_path'] = Str::replaceFirst(
            config('files.folder'),
            config('documents.folder.original'),
            $file['path']
        );

        // Obtiene la extensión del archivo original
        $fileInfo = new \SplFileInfo($file['original_path']);
        $fileExt = $fileInfo->getExtension();

        // Obtiene la ruta del archivo que se generará en el proceso de firma manuscrita
        // si se diese el caso que el archivo deba ser firmado
        $file['signed_path'] = Str::of($file['original_path'])->replace(
            config('documents.folder.original'),
            config('documents.folder.signed'),
        )
            // Reemplazar la extensión del archivo original por la extensión pdf
            ->replace(
                ".{$fileExt}",
                ".pdf",
            );

        if (!Storage::disk(env('APP_STORAGE'))->exists($file['original_path'])) {
            // Se copia el archivo si, previamente, no existía
            Storage::disk(env('APP_STORAGE'))->copy($file['path'], $file['original_path']);
        }

        // Realiza la conversión del archivo si es preciso
        try {
            $this->convert($file);
        } catch (DocumentNotValidException $e) {
            // Carga la página de error
            $mav = new ModelAndView('errors.custom');

            return $mav->render(
                [
                    'code' => 501,
                    'title' => Lang::get('El archivo suministrado no es válido'),
                    'message' => Lang::get('El tipo de archivo no puede ser procesado por la aplicación'),
                ]
            );
        }

        // Asigna un identificador global único al documento que se va a generar
        // Este identificador se utiliza en el proceso de certificación
        $file['guid'] = Uuid::create();

        // Añade el hash md5 y sha-1 al archivo
        // Demora un tiempo para archivos grandes
        $file['original_md5'] = md5(Storage::disk(env('APP_STORAGE'))->get($file['original_path']));
        $file['original_sha1'] = sha1(Storage::disk(env('APP_STORAGE'))->get($file['original_path']));

        // Crea un nuevo documento (firmable)
        $document = $user->documents()->create($file);
        $document->process()->create([]);   // Se crea el proceso relacionado con el documento

        // Registrar la acción para cada fichero en la selección
        FileLog::create(
            [
                'file_id' => $id,
                'action' => 'FIRMAR',
            ]
        );

        // Redirigir a la firma del documento
        return redirect()->route('dashboard.document.signers', ['id' => $document->id]);
    }

    /**
     * Guarda un conjunto de archivos seleccionados para ser compartidos
     * con uno o más usuarios
     *
     * @return string  Una vista
     */
    public function saveFileSet()
    {
        //
        // Obtiene la colección de archivos que van a ser compartidos
        // Los ids de los archivos seleccionados vienen separados por comas
        // Ej: 83,73,82,71,70
        //
        // El valor 'files' sólo contiene los valores marcados en la última página
        //          'selected' contiene todos los archivos seleccionados
        //
        $selected = File::find(explode(',', request()->input('selected')));

        $files = new Collection();
        foreach ($selected as $item) {
            if ($item->is_folder) {
                // Si es una carpeta, obtener todos los ficheros que contiene
                $inner = FileUtils::getInnerFiles($item, implode('/', $item->full_path ?? []) ?? '.');

                if ($inner) {
                    // Y agregarlos al resultado
                    $files->push(...$inner);
                }
            } else {
                // Si es un fichero regular, agregar normalmente al resultado
                $files->push($item);
            }
        }
        // Carga la vista que muestra el conjunto de archivos seleccionado
        return View::make('dashboard.file-sharing.file-share', ['files' => $files]);
    }


    /**
     * Muestra una vista dónde se listan los archivos seleccionados para ser firmados
     * mediante la opción de firma en la selección múltiple de archvivos
     *
     * @param Request $request La data de la solicitud
     *
     * @return string                       Una vista
     */
    public function showFilesToSignMultiple(Request $request): string
    {
        //
        // Obtiene la colección de archivos que van a ser compartidos
        // Los ids de los archivos seleccionados vienen separados por comas
        // Ej: 83,73,82,71,70
        //
        // El valor 'files' sólo contiene los valores marcados en la última página
        //          'selected' contiene todos los archivos seleccionados
        //
        $selected = File::find(explode(',', $request->input('selected')));

        $files = new Collection();
        foreach ($selected as $item) {
            if ($item->is_folder) {
                // Si es una carpeta, obtener todos los ficheros que contiene
                $inner = FileUtils::getInnerFiles($item, implode('/', $item->full_path ?? []) ?? '.');
                if ($inner) {
                    // Y agregarlos al resultado
                    $files->push(...$inner);
                }
            } else {
                // Si es un fichero regular, agregar normalmente al resultado
                $files->add($item);
            }
        }

        // Adiciono el atributo signable a cada fichero segun extensión del mismo
        foreach ($files as $file) {
            $file["signable"] = $file->canBeSigned();
        }

        $folders = $this->getFoldersStructure();

        // Determinar las carpetas hacia las que no es posible mover los ficheros seleccionados
        $excluded = [];
        foreach ($files as /** @var File $file */ $file) {
            if ($file->is_folder) {
                // No se puede mover una carpeta hacia el interior de ella misma
                $excluded[$file->id] = $file->id;

                // Tampoco hacia la carpeta padre de si misma
                $excluded[$file->parent_id] = $file->parent_id;

                // Tampoco hacia una subcarpeta en su estructura
                foreach ($this->getNestedFolders($file) as /** @var File $subfolder */ $subfolder) {
                    $excluded[$subfolder->id] = $subfolder->id;
                }
            }
        }

        // Carga la vista que muestra el conjunto de archivos seleccionado
        $mav = new ModelAndView('dashboard.files-multiple.multiple-sign');

        return $mav->render(
            [
                'files' => $files,
                'folders' => $folders,
                'excluded' => $excluded,
            ]
        );
    }

    /**
     * Recibe varios archivos firmables para ser convertidos en un documento pdf
     * mediante la unión de todos los archivos convertidos
     *
     * @param Request $request La data de la solicitud
     *
     * @return JsonResponse                       Una respuesta Json
     */
    public function saveFilesToSignMultiple(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            // Obtiene el usuario actual
            $user = Auth::user() ?? Guest::user();

            $files = File::find($request->dataFiles);
            $generatedFiles = array();

            foreach ($files as $file) {
                // Verifica si el usuario actual está autorizado para generar la firma del archivo
                $this->authorize('sign', $file);

                // Convierte el archivo en un array para su procesamiento
                $file = $file->toArray();

                // Copia el archivo desde su carpeta a la carpeta de documentos originales
                $file['original_path'] = Str::replaceFirst(
                    config('files.folder'),
                    config('documents.folder.original'),
                    $file['path']
                );

                if (!Storage::disk(env('APP_STORAGE'))->exists($file['original_path'])) {
                    // Se copia el archivo si, previamente, no existía
                    Storage::disk(env('APP_STORAGE'))->copy($file['path'], $file['original_path']);
                }

                // Realiza la conversión del archivo si es preciso
                try {
                    $this->convert($file, false);   // Que no se eliminen los temporales generados
                } catch (Exception $e) {
                    info("Error convirtiendo archivo para firma múltiple");
                    info($e->getMessage());
                    continue;
                }

                $generatedFiles[] = $file;
            };

            /*
             * Tengo las urls de los nuevos documentos pdfs generados, en la clave
             * 'converted_path'
             * Si alm es AWS se deben traer al local para poder hacer el merge
             */
            $merger = new PDFMerger;

            $name = '';         // El nombre del pdf unido

            foreach ($generatedFiles as $file) {
                // Si el almacenamiento es S3 se copia el archivo a la carpeta pública local
                if (AppStorage::isS3() && !Storage::disk('public')->exists($file['converted_path'])) {
                    Storage::disk('public')->put(
                        $file['converted_path'],
                        Storage::disk('s3')->get($file['converted_path'])
                    );
                }

                $path = Storage::disk('public')->path($file['converted_path']);
                // Obtenemos la info del archivo para saber orientación
                $pdf = new HPDFInfo($path);
                $sizes = explode(' ', $pdf->pageSize);
                if (intval($sizes[0]) > intval($sizes[2])) {
                    $merger->addPDF($path, 'all', 'horizontal');
                } else {
                    $merger->addPDF($path, 'all', 'vertical');
                }

                $name .= $file['id'];
            }

            $outFolder = config('documents.folder.converted');      // La carpeta para los archivo convertidos

            // Nombre temporal del archivo que se genera
            // En el próximo paso debe establecerse el nombre que el usuario quiera
            $name = $name . '_' . date('His') . '.pdf';

            // Path donde se guarda el nuevo pdf, generado con la unión de los X pdfs
            $relativePath = $outFolder . '/' . $name;
            $fullPath = Storage::disk('public')->path($relativePath);

            try {
                // Se unen los pdf, en un nuevo fichero pdf
                if ($merger->merge('file', $fullPath)) {
                    // Elimino ahora los pdfs generados antes del merge
                    foreach ($generatedFiles as $file) {
                        if (Storage::disk('public')->exists($file['original_path'])) {
                            unlink(Storage::disk('public')->path($file['original_path']));
                        }
                        if (Storage::disk('public')->exists($file['converted_path'])) {
                            unlink(Storage::disk('public')->path($file['converted_path']));
                        }
                    }

                    $original_path = join('/', [config('files.folder'), $name]);

                    $pdf = new PdfInfo($fullPath);
                    $size = $pdf->size();

                    $md5 = md5(Storage::disk('public')->get($relativePath));

                    // Creo el archivo como si lo hubiese subido
                    $file = $user->files()->create(
                        [
                            'name' => $name,
                            // El nombre original del archivo
                            'size' => $size,
                            // El tamaño del archivo en bytes
                            'path' => $original_path,
                            // La ruta del archivo
                            'md5' => $md5,
                            // El hash md5 del archivo
                            'type' => 'application/pdf',
                            // El tipo mime del archivo
                            'token' => Str::random(64),
                            // El token del archivo
                            'parent_id' => null,
                            // ID de la carpeta que contiene el archivo; nulo, si está en la raíz.
                            'is_folder' => false,
                            // Verdadero si el archivo es una carpeta; falso, en caso contrario.
                        ]
                    );

                    // Ahora generamos el documento a partir de este archivo
                    $doc = [];
                    $doc["name"] = $name;
                    $doc["size"] = $size;
                    $doc["path"] = "";
                    $doc["sent"] = 0;
                    $doc["original_path"] = $relativePath;

                    // Añade el hash md5 y sha-1 al archivo
                    // Demora un tiempo para archivos grandes
                    $doc['original_md5'] = $md5;
                    $doc['original_sha1'] = sha1(Storage::disk('public')->get($relativePath));

                    $doc["token"] = Str::random(64);    // El token para el documento que se ha generado
                    $doc["type"] = "application/pdf";   // Tipo Mime del documento
                    $doc["converted_path"] = $relativePath;
                    $doc["converted_size"] = $size;
                    $doc["pages"] = $pdf->pages();

                    // Asigna un identificador global único al documento que se va a generar
                    // Este identificador se utiliza en el proceso de certificación
                    $doc['guid'] = Uuid::create();

                    // Obtiene la ruta del archivo que se generará en el proceso de firma manuscrita
                    $doc['signed_path'] = Str::of($doc['original_path'])->replace(
                        config('documents.folder.converted'),
                        config('documents.folder.signed'),
                    );

                    // Si se usa almacenamiento S3, se copia el archivo resultante de la conversión en el bucket S3
                    // y se elimina del almacenamiento local
                    if (AppStorage::isS3()) {
                        // Se copia el archivo al almacenamiento S3
                        Storage::disk('s3')->put(
                            $relativePath,
                            Storage::disk('public')->get($relativePath),
                        );

                        Storage::disk('public')->delete($relativePath);
                    }

                    // Copiamos el archivo generado para la carpeta de los archivos originales
                    if (!Storage::disk(env('APP_STORAGE'))->exists($original_path)) {
                        Storage::disk(env('APP_STORAGE'))->copy(join('/', [$outFolder, $name]), $original_path);
                    }

                    // Crea un nuevo documento (firmable)
                    $document = $user->documents()->create($doc);
                    $document->process()->create([]);

                    return response()->json(["code" => 1, 'document' => $document, 'file' => $file]);
                } else {
                    unset($merger);
                    return response()->json(["code" => -1]);
                }
            } catch (Exception $e) {
                info("Error uniendo todos los archivos pdf generados");
                info($e->getMessage());
                return response()->json(["code" => -1, 'error' => $e->getMessage()]);
            }
        }
        return response()->json(["code" => -1]);
    }

    /**
     * Guarda nombre y location de un archivo
     *
     * @param Request $request La solicitud
     * @return JsonResponse                     Una respuesta JSON
     */
    public function saveFilesInfo(Request $request, $id): JsonResponse
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        $file = File::findOrFail($id);

        // Chequeo si tengo permisos para realizar esta operación de actualización sobre el archivo
        $this->authorize('update', $file);

        // Actualizamos el nombre del documento que generamos con el nombre inicial del archivo
        $document = \App\Models\Document::where('name', $file->name)->get()->last();
        if ($document) {
            $document->name = $request->name . '.pdf';
            $document->save();
        }

        // Actualizamos el nombre del archivo
        $file->name = $request->name . '.pdf';
        if ($request->location) {
            $parent = File::find($request->location);
            $file->full_path = ($parent->full_path ?? []) + [$parent->id => $parent->name];
            $file->parent_id = $request->location;
        }

        $file->save();

        return response()->json(['code' => 1, 'file' => $file]);
    }

    /**
     * Guarda la compartición de archivos
     *
     * @param Request $request La solicitud
     * @return JsonResponse                     Una respuesta JSON
     */
    public function saveFileSharing(Request $request): JsonResponse
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        $hasContacts = true !== (boolean)$request->no_contacts;

        // Obtiene los archivos compartidos
        $files = array_map(fn($file) => $file['id'], $request->input('files'));

        // Obtiene los contactos con los cuales se realiza el proceso de compartición
        $contacts = $request->users ?? null;

        // Si no se han proporcionado archivos o usuarios
        if (!$files || ($hasContacts && !$contacts)) {
            abort(404);
        }

        // Obtiene un token único de acceso al conjunto de archivos compartidos
        // Este token se usa para que cualquier persona pueda acceder al archivo
        $token = $request->get('token', Str::random(64));
        $title = $request->title;
        $description = $request->description;

        // Crea una nueva compartición de arhivos (FileSharing)
        $fileSharing = $user->fileSharings()->create(
            [
                'token' => $token,                      // Crea un token de acceso a la compartición
                'files' => json_encode($files),         // La lista de archivos compartidos
                'title' => $title,
                'description' => $description,
            ]
        );

        $route = route('workspace.set.share', ['token' => $token]);
        foreach ($files as $file) {
            // Registrar la acción para cada fichero en la selección
            FileLog::create(
                [
                    'file_id' => $file,
                    'action' => $hasContacts ? 'COMPARTIR POR FIKREA' : 'COPIAR URL',
                    'description' => sprintf(
                        'TÍTULO: %s. DESCRIPCIÓN: %s. ENLACE: <a href="%s">%s</a>',
                        $title,
                        $description,
                        $route,
                        $route
                    ),
                ]
            );
        }

        if ($hasContacts) {
            // A cada contacto se le asigna un token distinto
            foreach ($contacts as &$contact) {
                // Asigna un token personalizado a cada contacto
                $contact['token'] = Str::random(64);
                // Crea el contacto para la compartición
                $fileSharingContact = $fileSharing->contacts()->create($contact);

                // Envía un mensaje de correo o SMS a cada destinatario
                if ($contact['email']) {
                    // Si se ha proporcionado el correo del destinatario se notifica por email
                    EmailController::sendFileSharingEmail($fileSharing, $fileSharingContact);
                } elseif ($contact['phone']) {
                    // Si no se ha proporcionado un correo, pero si su teléfono, se notifica por SMS
                    SmsController::sendFileSharingSms($user, $fileSharingContact);
                }
            }
        }

        return response()->json($fileSharing);
    }

    /**
     * Muestra un listado con las comparticiones de archivos realizadas por el usuario
     *
     * @return string                           Una vista
     */
    public function sharing(): string
    {
        $mav = new ModelAndView('dashboard.file-sharing.index');

        return $mav->render();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function sharingDatatable(Request $request): JsonResponse
    {
        // Obtiene el usuario actual
        $user = Auth::user() ?? Guest::user();

        $builder = DB::table('file_sharings')->where('user_id', $user->id);

        // ¡¡¡No tiene opciones de búsqueda!!!!
        $totalCount = $filteredCount = $builder->count();

        $resultRaw = $builder->orderBy('file_sharings.created_at', 'desc')->offset($request->start)->limit(
            $request->length
        )->get();

        $actionButtons = <<<EOT
<div class="btn-group-vertical" role="group" aria-label="">
    <div class="btn-group" role="group" aria-label="">
        <a href="%s" class="btn btn-action-edit disabled" data-toggle="tooltip" data-placement="top" 
            data-original-title="%s">
            <i class="fas fa-edit"></i>
        </a>
        <a href="%s" class="btn btn-action-download" data-toggle="tooltip" data-placement="top" 
            data-original-title="%s">
            <i class="fas fa-file-download"></i>
        </a>
        <a href="%s" class="btn btn-action-details" data-toggle="tooltip" data-placement="top" data-original-title="%s">
            <i class="fas fa-eye"></i>
        </a>
    </div>
    <div class="btn-group mt-1" role="group" aria-label="">
        <a href="%s" class="btn btn-action-copy-url" data-toggle="tooltip" data-placement="top" 
            data-original-title="%s" data-id="%d">
            <i class="fas fa-copy"></i>
        </a>
        <a href="%s" class="btn btn-action-share disabled" data-toggle="tooltip" data-placement="top" 
            data-original-title="%s">
            <i class="fas fa-share-alt"></i>
        </a>
        <form action="%s" method="post" data-toggle="tooltip" data-placement="top" data-original-title="%s">
            %s
            <input type="hidden" name="_method" value="DELETE">
            <button class="btn btn-action-remove"><i class="fas fa-trash"></i></button> 
        </form>
    </div>
</div>
<input type="hidden" id="token-%d" value="%s">
<input type="hidden" id="title-%d" value="%s">
<input type="hidden" id="description-%d" value="%s">
EOT;

        // Normalizar el resultado a mostrar
        foreach ($resultRaw as $item) {
            $ids = json_decode($item->files, true);

            $files = collect();
            $size = 0;
            foreach (File::find($ids) as $file) {
                if ($file->is_folder) {
                    // Si es una carpeta, obtener los ficheros que contiene y agregarlos al listado
                    // respetando la estructura original
                    $innerFiles = FileUtils::getInnerFiles($file);

                    foreach ($innerFiles as $innerFile) {
                        $files->add(
                            (object)[
                                'name' => $innerFile->name,
                                'parent_id' => $innerFile->parent_id,
                                'full_path' => implode('/', $innerFile->full_path ?? []),
                                'size' => $this->humanReadableSize($innerFile->size),
                            ]
                        );

                        // Ir calculando el tamaño real del compartido
                        $size += $innerFile->size;
                    }
                } else {
                    $files->add(
                        (object)[
                            'name' => $file->name,
                            'parent_id' => $file->parent_id,
                            'full_path' => implode('/', $file->full_path ?? []),
                            'size' => $this->humanReadableSize($file->size),
                        ]
                    );

                    // Ir calculando el tamaño real del compartido
                    $size += $file->size;
                }
            }

            $result[] = (object)[
                'title' => $item->title,
                'description' => $item->description,
                'count' => count($files),
                'size' => $this->humanReadableSize($size),
                'file_list' => $files->toArray(),
                'recipient_list' => FileSharingContact::where(
                    'file_sharing_contacts.file_sharing_id',
                    $item->id
                )->get()->toArray(),
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'action' => sprintf(
                    $actionButtons,
                    'javascript:void(0)', // route(),
                    Lang::get('Editar'),
                    route('file.set.download', ['id' => $item->id]),
                    Lang::get('Descargar'),
                    route('dashboard.files.sharing-history', ['id' => $item->id]),
                    Lang::get('Histórico de descargas'),
                    'javascript:void(0)', // route(),
                    Lang::get('Copiar URL'),
                    $item->id,
                    'javascript:void(0)', // route(),
                    Lang::get('Compartir'),
                    route('dashboard.file-sharing.destroy', ['id' => $item->id]),
                    Lang::get('Eliminar'),
                    csrf_field(),
                    $item->id,
                    $item->token,
                    $item->id,
                    $item->title,
                    $item->id,
                    $item->description
                ),
            ];
        }

        return response()->json(
            [
                'draw' => $request->draw,
                'recordsTotal' => $totalCount,
                'recordsFiltered' => $filteredCount,
                'data' => $result ?? [],
            ]
        );
    }

    /**
     * Muestra un listado con histórico de las visitas y descargas al conjunto de archivo
     *
     * @param int $id El id de la compartición de archivos
     *
     * @return string                           Una vista
     * @throws AuthorizationException
     */
    public function sharingHistory(int $id): string
    {
        // Obtiene la compartición de archivos
        $fileSharing = FileSharing::findOrFail($id);

        $this->authorize('history', $fileSharing);

        $mav = new ModelAndView('dashboard.file-sharing.history');

        return $mav->render(
            [
                'fileSharing' => $fileSharing,
            ]
        );
    }

    /**
     * @param Request $request
     * @param         $id
     * @return JsonResponse
     */
    public function sharingHistoryDatatable(Request $request, $id): JsonResponse
    {
        $builder = DB::table('file_sharing_histories')->leftJoin(
            'file_sharing_contacts',
            'file_sharing_histories.file_sharing_contact_id',
            '=',
            'file_sharing_contacts.id'
        )->where('file_sharing_histories.file_sharing_id', $id);

        // ¡¡¡No tiene opciones de búsqueda!!!!
        $totalCount = $filteredCount = $builder->count();

        $result = $builder->orderBy('file_sharing_histories.created_at')->offset($request->start)->limit(
            $request->length
        )->get();

        $result = $result->map(
            function ($item) {
                if ($item->starts_at) {
                    $item->action = Lang::get('Acceso');
                } elseif ($item->downloaded_at) {
                    $item->action = Lang::get('Descarga');
                    $item->starts_at = $item->downloaded_at;
                } else {
                    $item->action = '';
                }

                // $item->contact = FileSharingContact::where('file_sharing_id', $item->file_sharing_id)->first();

                $IPLocation = new GeoIp($item->ip);

                $item->anonymous_text = Lang::get('Anónimo');
                $item->location = $IPLocation->toArray();
                $item->no_location_text = Lang::get('No se ha obtenido');

                return $item;
            }
        );

        return response()->json(
            [
                'draw' => $request->draw,
                'recordsTotal' => $totalCount,
                'recordsFiltered' => $filteredCount,
                'data' => $result,
            ]
        );
    }

    /**
     * Muestra la interfaz de creación de una carpeta
     *
     * @return string                           Una vista
     * @throws FileIsNotAFolderException
     */
    public function createFolder(): string
    {
        $folders = $this->getFoldersStructure();

        // Si en la solicitud se indicó un ID, este se corresponde con la carpeta padre donde se crea la nueva carpeta
        $folder = (object)[
            'parent_id' => request()->id ?? null,
        ];

        $mav = new ModelAndView('dashboard.files.edit-folder');

        return $mav->render(
            [
                'folders' => $folders,
                'file' => $folder,
            ]
        );
    }

    /**
     * Crea una carpeta en la base de datos
     *
     * @param Request $request La solicitud
     * @return RedirectResponse
     * @throws \Exception
     */
    public function storeFolder(Request $request): RedirectResponse
    {
        // Obtiene el usuario para la sesión actual
        $user = Auth::user() ?? Guest::user();

        // Valida la solicitud
        $name = $request->name;
        $folder = $request->parent_id;
        $notes = $request->notes;

        $validator = Validator::make(
            [
                'parent_id' => $folder,
                'name' => $name,
            ],
            [
                'parent_id' => [
                    'nullable',
                    Rule::exists('files', 'id')->where('user_id', $user->id)->where('is_folder', true),
                ],
                'name' => [
                    'required',
                    Rule::unique('files')->where('user_id', $user->id)->where('parent_id', $folder),
                ],
            ],
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $parent = File::find($folder);

        $file = $user->files()->create(
            [
                'name' => $name,
                'parent_id' => $folder,
                'is_folder' => true,
                'type' => 'application/folder',
                'token' => Str::random(64),
                'notes' => $notes,
                'full_path' => $parent ? ($parent->full_path ?? []) + [$parent->id => $parent->name] : null,
            ]
        );

        return response()->redirectToRoute('dashboard.file.list', ['id' => $file->id]);
    }

    /**
     *  Mostrar formulario para edición de carpeta
     *
     * @param int $id El identificador de la carpeta a editar
     * @return string   La vista
     * @throws FileIsNotAFolderException
     */
    public function editFolder(int $id): string
    {
        $file = File::find($id);

        if (!$file || !$file->is_folder) {
            abort(Response::HTTP_BAD_REQUEST);
        }

        $folders = $this->getFoldersStructure();

        $mav = new ModelAndView('dashboard.files.edit-folder');

        return $mav->render(
            [
                'folders' => $folders,
                'file' => $file,
            ]
        );
    }

    /**
     * Muestra la interfaz para editar un fichero
     *
     * @param int $id El identificador del fichero
     * @return string       La vista
     */
    public function edit(int $id): string
    {
        $file = File::find($id);

        $mav = new ModelAndView('dashboard.files.edit');

        return $mav->render(
            [
                'file' => $file,
            ]
        );
    }

    /**
     * Actualiza los datos de un fichero
     *
     * @param Request $request La solicitud
     * @param int     $id      El identificador del fichero
     * @return RedirectResponse
     * @throws Throwable
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        // Obtiene el usuario para la sesión actual
        $user = Auth::user() ?? Guest::user();

        // Localizar el fichero indicado
        $file = File::find($id);

        // Valida la solicitud
        $name = $request->name;
        $notes = $request->notes ?? null;

        $validator = Validator::make(
            [
                'name' => $name,
                'notes' => $notes,
            ],
            [
                'name' => [
                    'required',
                    Rule::unique('files')->where('user_id', $user->id)->where('parent_id', $file->parent_id)->ignore(
                        $id
                    ),
                ],
                'notes' => 'nullable',
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // Registrar la acción para cada fichero en la selección
        FileLog::create(
            [
                'file_id' => $file->id,
                'action' => 'EDITAR',
                'description' => 'Nombre anterior: ' . $file->name,
            ]
        );

        DB::transaction(
            static function () use ($file, $name, $notes) {
                // Si es una carpeta y cambió de nombre, se debe de actualizar la ruta en todos los ficheros que se
                // que formen parte de su estructura
                if ($file->is_folder && ($file->name !== $name)) {
                    // Obtener todos los ficheros que tienen a esta carpeta en su estructura
                    $inPath = File::whereJsonContains('full_path', [$file->id => $file->name])->get();

                    foreach ($inPath as $item) {
                        $currentPath = $item->full_path;

                        // Actualizar al nuevo nombre de la carpeta
                        $currentPath[$file->id] = $name;

                        // Actualizar la estructura del fichero para reflejar el nuevo nombre de la carpeta
                        $item->full_path = $currentPath;

                        $item->save();
                    }
                }

                // Por último, cambiar el nombre de la carpeta
                $file->update(['name' => $name, 'notes' => $notes]);
            }
        );

        return response()->redirectToRoute('dashboard.file.list');
    }

    /**
     * Muestra la interfaz para mover una selección múltiple hacia una carpeta
     *
     * @param Request $request La solicitud
     * @return string          La vista
     * @throws FileIsNotAFolderException
     */
    public function multipleMove(Request $request): string
    {
        /** @var Collection $files */
        $files = File::find(explode(',', $request->selected));

        $folders = $this->getFoldersStructure();

        // Determinar las carpetas hacia las que no es posible mover los ficheros seleccionados
        $excluded = [];
        foreach ($files as /** @var File $file */ $file) {
            if ($file->is_folder) {
                // No se puede mover una carpeta hacia el interior de ella misma
                $excluded[$file->id] = $file->id;

                // Tampoco hacia la carpeta padre de si misma
                $excluded[$file->parent_id] = $file->parent_id;

                // Tampoco hacia una subcarpeta en su estructura
                foreach ($this->getNestedFolders($file) as /** @var File $subfolder */ $subfolder) {
                    $excluded[$subfolder->id] = $subfolder->id;
                }
            }
        }

        // Carga la vista que muestra el conjunto de archivos seleccionado
        $mav = new ModelAndView('dashboard.files.multiple-move');

        return $mav->render(
            [
                'files' => $files,
                'folders' => $folders,
                'excluded' => $excluded,
            ]
        );
    }

    /**
     * Almacena en base de datos la actualización de mover una selección de varios ficheros hacia una carpeta
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function multipleDoMove(Request $request): RedirectResponse
    {
        // Obtiene el usuario para la sesión actual
        $user = Auth::user() ?? Guest::user();

        // Valida la solicitud
        $folder = $request->parent_id;
        $files = $request->file;
        $name = $request->folder_name;
        $notes = $request->folder_notes;

        $validator = Validator::make(
            [
                'parent_id' => $folder,
                'files' => $files,
                'name' => $name,
            ],
            [
                'parent_id' => [
                    'nullable',
                    Rule::exists('files', 'id')->where('user_id', $user->id)->where('is_folder', true),
                ],
                'files' => [
                    'required',
                ],
                'name' => [
                    'nullable',
                    Rule::unique('files')->where('user_id', $user->id)->where('parent_id', $folder),
                ],
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        // Los ID deben de ser enteros, no cadenas de texto
        $files = collect($files)->map(
            function ($item) {
                return (int)$item;
            }
        )->toArray();

        $folderId = $this->doMoveFiles($files, $folder, $name, $notes);

        return response()->redirectToRoute('dashboard.file.list', ['id' => $folderId]);
    }

    /**
     * Mostrar interfaz para mover un fichero hacia una carpeta
     *
     * @param $id       El identificador del fichero
     * @return string   La vista
     * @throws FileIsNotAFolderException
     */
    public function move($id): string
    {
        $file = File::find($id);

        $folders = $this->getFoldersStructure();

        $mav = new ModelAndView('dashboard.files.move');

        return $mav->render(
            [
                'folders' => $folders,
                'file' => $file,
            ]
        );
    }

    /**
     * Almacenar en base de datos el movimiento de un fichero hacia una carpeta
     *
     * @param Request $request La solicitud
     * @param int     $id      El identificador del fichero
     * @return RedirectResponse
     * @throws Throwable
     */
    public function doMove(Request $request, int $id): RedirectResponse
    {
        // Obtiene el usuario para la sesión actual
        $user = Auth::user() ?? Guest::user();

        // Valida la solicitud
        $folder = $request->parent_id;
        $name = $request->folder_name;
        $notes = $request->folder_notes;

        $validator = Validator::make(
            [
                'parent_id' => $folder,
                'name' => $name,
            ],
            [
                'parent_id' => [
                    'nullable',
                    Rule::exists('files', 'id')->where('user_id', $user->id)->where('is_folder', true),
                ],
                'name' => [
                    'nullable',
                    Rule::unique('files')->where('user_id', $user->id)->where('parent_id', $folder),
                ],
            ]
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        $folderId = $this->doMoveFiles([$id], $folder, $name, $notes);

        return response()->redirectToRoute('dashboard.file.list', ['id' => $folderId]);
    }

    /**
     * Retorna el historial de uso de un archivo
     *
     * @param $id
     * @return string
     * @throws AuthorizationException
     */
    public function history($id)
    {
        // Obtiene el usuario actual
        $user = Auth::user() ?? Guest::user();

        // Obtenemos el archivo
        $file = File::findOrFail($id);

        // Verifica si el usuario actual está autorizado para visualizar el historial del archivo
        $this->authorize('history', $file);

        $mav = new ModelAndView('dashboard.files.history');

        return $mav->render(
            [
                'file' => $file,
            ]
        );
    }

    /**
     * @param Request $request
     * @param         $id
     * @return JsonResponse
     */
    public function historyDatatable(Request $request, $id): JsonResponse
    {
        $builder = DB::table('file_logs')->where('file_logs.file_id', $id);

        // ¡¡¡No tiene opciones de búsqueda!!!!
        $totalCount = $filteredCount = $builder->count();

        $result = $builder->orderBy('file_logs.created_at')->offset($request->start)->limit($request->length)->get();

        return response()->json(
            [
                'draw' => $request->draw,
                'recordsTotal' => $totalCount,
                'recordsFiltered' => $filteredCount,
                'data' => $result,
            ]
        );
    }

    /**
     * Retorna todos los ficheros y carpetas contenidos en una carpeta, descendiendo de manera recursiva todos los
     * posibles niveles
     *
     * @param File            $file        La carpeta a partir de la cual se comienza la búsqueda
     * @param int             $level       El nivel por el que se navega (parámetro uso interno de recursión)
     * @param Collection|null $nestedFiles Los ficheros anidados (parámetro uso interno de recursión)
     * @return Collection       La colección de ficheros anidados
     * @throws FileIsNotAFolderException
     */
    private function getNestedFiles(File $file, int $level = 0, Collection $nestedFiles = null): Collection
    {
        // El nivel 0 es la carpeta que se indicó borrar
        if (0 === $level) {
            // Crear la colección inicializada con el primer fichero(carpeta) que se indicó borrar
            // No es necesario hacerlo en los niveles subsiguientes porque todos los ficheros(carpetas) en el árbol
            // descendente será incluido en la colección de ficheros a borrar
            $nestedFiles = collect([$file]);
        }

        foreach ($file->files as /** @var File */ $nestedFile) {
            // Incluir este fichero en la colección
            $nestedFiles->add($nestedFile);

            // Si es una carpeta, hacer llamada recursiva para incluir a continuación todos los
            // ficheros/carpetas que contiene
            if ($nestedFile->is_folder) {
                $this->getNestedFiles($nestedFile, $level + 1, $nestedFiles);
            }
        }

        return $nestedFiles;
    }

    /**
     * Retorna todas las carpetas contenidas en una carpeta, descendiendo de manera recursiva todos los posibles niveles
     *
     * @param File            $folder        La carpeta a partir de la cual se inicia la búsqueda
     * @param int             $level         El nivel por el que se navega (parámetro uso interno de recursión)
     * @param Collection|null $nestedFolders Los ficheros anidados (parámetro uso interno de recursión)
     * @return Collection                    La colección de carpetas anidados
     * @throws FileIsNotAFolderException
     */
    private function getNestedFolders(File $folder, int $level = 0, Collection $nestedFolders = null): Collection
    {
        // El nivel 0 es la carpeta que se indicó borrar
        if (0 === $level) {
            // Crear la colección inicializada con el primer fichero(carpeta) que se indicó borrar
            // No es necesario hacerlo en los niveles subsiguientes porque todos los ficheros(carpetas) en el árbol
            // descendente será incluido en la colección de ficheros a borrar
            $nestedFolders = collect([$folder]);
        }

        foreach ($folder->files()->where('is_folder', true)->get() as /** @var File */ $nestedFolder) {
            // Incluir este fichero en la colección
            $nestedFolders->add($nestedFolder);

            // Si es una carpeta, hacer llamada recursiva para incluir a continuación todos los
            // ficheros/carpetas que contiene
            $this->getNestedFolders($nestedFolder, $level + 1, $nestedFolders);
        }

        return $nestedFolders;
    }

    /**
     * Retornar la estructura de carpetas creada por el usuario
     *
     * @return array        La estructura de carpetas
     * @throws FileIsNotAFolderException
     */
    private function getFoldersStructure(): array
    {
        // Obtiene el usuario para la sesión actual
        $user = Auth::user() ?? Guest::user();

        // Retornar las carpetas en el orden indicado, para mostrar una estructura de árbol en el componente SELECT
        $folders = [];
        $rootFolders = File::where('user_id', $user->id)->where('is_folder', true)->whereNull('parent_id')->orderBy(
            'name'
        )->get();
        foreach ($rootFolders as $rootFolder) {
            $nestedFolders = $this->getNestedFolders($rootFolder)->map->only(['id', 'name', 'parent_id', 'full_path'])
                ->toArray();

            foreach ($nestedFolders as $item) {
                $folders[] = (object)$item;
            }
        }

        return $folders;
    }

    /**
     * Obtener información adicional de carpetas: tamaño, cantidad de ficheros que contiene (a todos los niveles),
     * cantidad de carpetas que contiene (a todos los niveles), y el último fichero actualizado.
     *
     * @param File $folder El identificador de la carpeta
     * @return array       El tamaño, la cantidad de ficheros, la cantidad de carpetas, y el último fichero actualizado
     */
    private function getExtraInfoOfFolder(File $folder): array
    {
        $size = 0;
        $filesCount = 0;
        $foldersCount = 0;
        /** @var File $lastUpdated */
        $lastUpdated = null;

        foreach ($folder->files as $file) {
            if ($file->is_folder) {
                [$innerSize, $innerFiles, $innerFolders, $innerUpdated] = $this->getExtraInfoOfFolder($file);

                $foldersCount++;

                $size += $innerSize;
                $filesCount += $innerFiles;
                $foldersCount += $innerFolders;

                if ($innerUpdated && $innerUpdated->updated_at->isAfter($lastUpdated->updated_at ?? null)) {
                    $lastUpdated = $innerUpdated;
                }
            } else {
                $size += $file->size;
                $filesCount++;
                if ($file->updated_at->isAfter($lastUpdated->updated_at ?? null)) {
                    $lastUpdated = $file;
                }
            }
        }

        return [$size, $filesCount, $foldersCount, $lastUpdated];
    }

    /**
     * Obtener los archivos de una carpeta especifica
     *
     * @param File $folder El identificador de la carpeta
     * @return array       Listado de archivos
     */
    public static function getFilesInFolder(File $file): array
    {
        $files = [];

        foreach ($file->files as $file) {
            if ($file->is_folder) {
                $files[] = FileController::getFilesInFolder($file);
            } else {
                $files[] = $file;
            }
        }

        return $files;
    }

    /**
     * Obtener los archivos de una carpeta especifica
     * formateado para el uso del componente Treeselect de vue js
     * @see https://vue-treeselect.js.org/#basic-features
     *
     * @param File $folder El identificador de la carpeta
     * @return array       Listado de archivos
     */
    public static function getFilesInFolderTreeselect(File $file): array
    {
        $files = [];

        foreach ($file->files as $file) {
            if ($file->is_folder) {
                $files[] = [
                    'id' => $file->id,
                    'label' => $file->name,
                    'children' => FileController::getFilesInFolderTreeselect($file)
                ];
            } else {
                $files[] = [
                    'id' => $file->id,
                    'label' => $file->name
                ];
            }
        }

        return $files;
    }

    /**
     * Obtener las carpetas dentro de una carpeta especifica
     * formateado para el uso del componente Treeselect de vue js
     * @see https://vue-treeselect.js.org/#basic-features
     *
     * @param File $folder El identificador de la carpeta
     * @return array       Listado de carpetas
     */
    public static function getFoldersInFolderTreeselect(File $file)
    {
        $folders = [];

        foreach ($file->files as $file) {
            if ($file->is_folder) {
                $folders[] = [
                    'id' => $file->id,
                    'label' => $file->name,
                    'children' => FileController::getFilesInFolderTreeselect($file)
                ];
            }
        }

        return $folders;
    }

    /**
     * Obtiene el contenido de un archivo en base 64
     *
     * @param File $file El archivo
     * @return string      El contenido del archivo
     */
    public function getFileBase64Content(File $file): string
    {
        return base64_encode(Storage::disk(env('APP_STORAGE'))->get($file->path));
    }

    /**
     * Obtiene el certificado en PDF del histórico de descargas
     *
     * @param int $id El id de la solicitud
     */
    public function certificate(int $id)
    {
        // Obtenemos el documento
        $sharing = FileSharing::findOrFail($id);

        // Verifica si el usuario actual está autorizado para generar y descargar el certificado de validación
        $this->authorize('certificate', $sharing);

        // Genera un archivo PDF, cargando la vista correspondiente
        $pdf = PDF::loadView('dashboard.file-sharing.pdf.certificate', ['sharing' => $sharing]);

        // Genera la descarga del certificado
        return $pdf->download("sharing-{$sharing->id}.pdf");
    }

    /**
     * Mover ficheros hacia el interior de una carpeta
     *
     * @param array       $files  ID de los ficheros a mover al interior de la carpeta
     * @param int|null    $folder ID de la carpeta hacia la que se mueven los ficheros
     * @param string|null $name   Nombre de la nueva carpeta a crear
     * @param string|null $notes  Notas de la nueva carpeta a crear
     * @return int|null
     * @throws Throwable
     */
    private function doMoveFiles(array $files, int $folder = null, string $name = null, string $notes = null): ?int
    {
        // Obtiene el usuario para la sesión actual
        $user = Auth::user() ?? Guest::user();

        // Localizar la carpeta escogida; o establecer a nulo, si se mueve a la raíz (carpeta PRINCIPAL)
        if (null !== $folder) {
            $parent = File::find($folder);
        } else {
            $parent = null;
        }

        // Crear la nueva carpeta, si es necesario
        if (null !== $name) {
            $newFolder = $user->files()->create(
                [
                    'name' => $name,
                    'parent_id' => $folder,
                    'is_folder' => true,
                    'type' => 'application/folder',
                    'token' => Str::random(64),
                    'notes' => $notes,
                    'full_path' => $parent ? ($parent->full_path ?? []) + [$parent->id => $parent->name] : null,
                ]
            );
        } else {
            $newFolder = null;
        }

        // Se mueve a la nueva carpeta creada; si no, a la escogida
        $parent = $newFolder ?? $parent;

        // Determinar la ruta hacia la que se moverán los ficheros
        $basePath = $parent ? ($parent->full_path ?? []) + [$parent->id => $parent->name] : [];

        // Garantizar que se actualice toda la estructura
        DB::transaction(
            function () use ($files, $parent, $basePath) {
                foreach ($files as $id) {
                    $file = File::find($id);

                    $currentPath = ($file->full_path ?? []);

                    // Si se está moviendo una carpeta, hay que actualizar la ruta de todos los
                    // ficheros y carpetas contenidas en la misma
                    if ($file->is_folder) {
                        $children = File::whereJsonContains(
                            'full_path',
                            $currentPath + [$file->id => $file->name]
                        )->get();

                        foreach ($children as $child) {
                            $newPath = $basePath + array_diff($child->full_path ?? [], $currentPath);

                            $child->update(['full_path' => $newPath ?? null]);

                            // Registrar la acción
                            FileLog::create(
                                [
                                    'file_id' => $child->id,
                                    'action' => 'MOVER',
                                    'description' => 'Carpeta anterior: ' . implode(
                                            '/',
                                            $file->full_path ?? ['PRINCIPAL']
                                        ),
                                ]
                            );
                        }
                    }

                    // Registrar la acción
                    FileLog::create(
                        [
                            'file_id' => $id,
                            'action' => 'MOVER',
                            'description' => 'Carpeta anterior: ' . implode('/', $file->full_path ?? ['PRINCIPAL']),
                        ]
                    );
                }

                File::whereIn('id', $files)->update(
                    [
                        'parent_id' => $parent->id ?? null,
                        'full_path' => ([] !== $basePath) ? $basePath : null,
                    ]
                );
            }
        );

        return $parent->id ?? null;
    }

    /**
     * Obtener la información complementaria que se necesita para las carpetas
     *
     * @param $file
     */
    private function extraInfo(&$file): void
    {
        // Complementar información de carpetas
        if ($file->is_folder) {
            [
                $file->size,
                $file->files_count,
                $file->folders_count,
                $file->last_updated,
            ] = $this->getExtraInfoOfFolder($file);

            // Construir el texto a mostrar en la interfaz
            $file->extra_data = $file->notes ? 'NOTA: ' . $file->notes . '<br>' : '';
            $file->extra_data .= !($file->folders_count || $file->files_count) ? Str::upper(
                Lang::get('Carpeta vacía')
            ) : Lang::get(
                ':folders_count subcarpetas, :files_count ficheros<br> Última actualización: :last_updated_name',
                [
                    'folders_count' => $file->folders_count,
                    'files_count' => $file->files_count,
                    'last_updated_name' => $file->last_updated->name ?? '',
                ]
            );
        }
    }

    /**
     * Retorna el tamaño dado en bytes en un formato más legible
     *
     * @param int $size Tamaño en bytes
     * @return string Cadena de texto con el tamaño en formato legible
     */
    private function humanReadableSize(int $size): string
    {
        switch ((int)(log($size) / log(2) / 10)) {
            case 0:
            case 1:
                return number_format($size / 1024, 1, ',', '') . ' kB';
            case 2:
                return number_format($size / (1024 * 1024), 1, ',', '') . ' MB';
            case 3:
                return number_format($size / (1024 * 1024 * 1024), 1, ',', '') . ' GB';
        }
    }
}
