<?php

/**
 * ScreenController
 *
 * Controlador para las grabaciones de pantalla de los usuarios
 *
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * Controladores requeridos
 */
use App\Http\Controllers\FileController;

/**
 * Fikrea
 */
use Fikrea\ModelAndView;

use App\Models\Screen;

class ScreenController extends Controller
{
    /**
     * Devuelve un array restructurado con el sistema de carpetas del usuario en fikrea
     * para pasarle al componente Treeselect
     * @see https://vue-treeselect.js.org/#basic-features
     *
     * @return array
     */
    private function getFileSystemTreeselect() : array
    {
        // Aquí debo enviarle la estructura del sistema de carpetas de
        // fikrea seleccionar destino de una grabación
        $fileSystemTreeselect = [];

        // Confecciono mi arreglo según el formato que necesito en vue js para el componente Treeselect
        // @see https://vue-treeselect.js.org/#basic-features
        foreach (auth()->user()->files as $file) {
            if ($file->is_folder === 1) {
                // Para Treeselect
                $fileSystemTreeselect[] = [
                    'id'        =>  $file->id,
                    'label'     =>  $file->name,
                    'children'  =>  FileController::getFoldersInFolderTreeselect($file),
                ];
            }
        }

        return $fileSystemTreeselect;
    }

    /**
     * Devuelve la vista para crear una grabación de pantalla
     *
     * @return void
     */
    public function editScreen() : string
    {
        // Vista para realizar la grabación
        $mav = new ModelAndView('dashboard.screens.edit.record-screen');

        return $mav->render([
            'fileSystemTreeselect'  => $this->getFileSystemTreeselect(),
        ]);
    }

    /**
     * Guarda una grabación de pantalla del usuario autenticado
     *
     * Al finalizar la grabación de un video se guarda este en una carpeta
     * temporal para luego moverse para su ubicación final
     *
     * @param Request $request          La solicitud
     * @return JsonResponse             La respuesta JSON
     */
    public function saveScreenRecord(Request $request) : JsonResponse
    {
        if ($request->ajax()) {
            // Creamos el screen con los datos que recibimos en la petición
            $screen = Screen::create($request->capture);
            $screen->user_id = Auth::user()->id;
            $screen->save();

            // chequeo si existe la carpeta temporal donde se guardarán estos archivos tmps
            if (!Storage::disk(env('APP_STORAGE'))->exists(config('screen.folder.temp'))) {
                Storage::disk(env('APP_STORAGE'))->makeDirectory(config('screen.folder.temp'));
            }

            // Copio el contenido del archivo para la carpeta creada
            Storage::disk(env('APP_STORAGE'))->put(
                config('screen.folder.temp') . '/' .$screen->filename,
                base64_decode(preg_replace('#data:[^;]+/[^;]+;base64,#', '', $request->capture['file']))
            );

            return response()->json([
                'token' => $screen->token,
            ]);
        }
    }

    /**
     * Guarda una lista de grabaciones de pantalla del usuario autenticado
     *
     * Al finalizar el proceso de grabación de X videos se guardan todos
     * en el sistema de archivo, cada uno en la ubicación indicada en 'location'.
     * Si 'location' es null, se copia en la raíz del filesystem
     *
     * @param Request $request          La solicitud
     * @return JsonResponse             La respuesta JSON
     */
    public function saveScreenAllRecords(Request $request) : JsonResponse
    {
        if ($request->ajax()) {
            collect($request->captures)-> each(function ($capture) {
                // Verifico si está en la base de datos, y no ha sido salvado
                $screenSaved = Screen::where('token', $capture['token'])
                                     ->where('saved', false)
                                     ->first();
                // Si lo encuentro significa que está en la carpeta temporal sin procesar,
                // esperando este momento
                if ($screenSaved) {
                    if ($screenSaved->filename != $capture['filename']) {
                        // Actualizar el nombre al archivo temporal
                        Storage::disk(env('APP_STORAGE'))->move(
                            config('screen.folder.temp') . '/' . $screenSaved->filename,
                            config('screen.folder.temp') . '/' . $capture['filename'],
                        );
                        $screenSaved->filename = $capture['filename'];
                        $screenSaved->path = $capture['path'];
                    }
                    $screenSaved->save();
                } else {
                    // No se ha encontrado, porque es un nuevo archivo, que viene con su data en 'file'
                    
                    // Copio el contenido del archivo para la carpeta temporal
                    Storage::disk(env('APP_STORAGE'))->put(
                        config('screen.folder.temp') . '/' .$capture['filename'],
                        base64_decode(preg_replace('#data:[^;]+/[^;]+;base64,#', '', $capture['file']))
                    );

                    $screenSaved = Screen::create($capture);
                    $screenSaved->user_id = Auth::user()->id;
                    $screenSaved->save();
                }

                // En este punto tengo el registro del archivo
                // en la tabla actualizado y en carpeta tmp

                // Lo convierto en un archivo y lo marco como procesado en caso satisfactorio
                $fileId = $screenSaved->toFile();

                if ($fileId > 0) {
                    // Elimino el archivo temporal
                    Storage::disk(env('APP_STORAGE'))->delete(
                        config('screen.folder.temp') . '/' .$screenSaved->filename,
                    );
                    // marco como guardado
                    $screenSaved->saved = true;
                    $screenSaved->file_id = $fileId;
                    $screenSaved->save();
                }
            });

            return response()->json(['code'=>1]);
        }
    }

    /*
     * Devuelve vista con el listado de las capturas que ha realizado el usuario
     *
     * @return string
     */
    public function getScreens() : string
    {
        // Vista para listar las grabaciones
        $mav = new ModelAndView('dashboard.screens.list.screens-list');

        $screens = array();

        Auth::user()->screens->each(function ($screen) use (&$screens) {
            try {
                if ($screen->file) {
                    $screen['base64'] = base64_encode(Storage::disk(env('APP_STORAGE'))->get($screen->file->path));
                }
                array_push($screens, $screen);
            } catch (Exception $e) {
            }
        });

        return $mav->render([
            'screens'               => $screens,
            'fileSystemTreeselect'  => $this->getFileSystemTreeselect(),
        ]);
    }

    /*
     * Actualiza la información de una captura que ha realizado el usuario
     *
     * @return JsonResponse
     */
    public function updateScreen(Request $request, Screen $screen) : JsonResponse
    {
        if ($request->ajax()) {
            $newPath = config('files.folder') . '/' . $request->capture['filename'];

            if ($request->capture['filename'] != $screen) {
                // Actualizar el nombre del archivo
                Storage::disk(env('APP_STORAGE'))->move(
                    $screen->file->path,
                    $newPath
                );
                $screen->path = $request->capture['path'];
                $screen->file->path = $newPath;
                $screen->file->save();
            }

            $screen->filename = $request->capture['filename'];
            $screen->path = $request->capture['path'];
            $screen->save();

            $screen->file->update([
                'name' => $screen->filename,
                'parent_id' => $screen->path,
            ]);

            return response()->json(['code' => 1]);
        }
    }

    /*
     * Elimina una captura que ha realizado el usuario
     *
     * @return JsonResponse
     */
    public function destroyScreen(Request $request, Screen $screen) : JsonResponse
    {
        if ($request->ajax()) {
            try {
                // Eliminar el archivo que pertenece a la grabación
                Storage::disk(env('APP_STORAGE'))->delete($screen->file->path);

                // Elimino el archivo relacionado al screen
                $screen->file->delete();
        
                // Elimino el screen
                $screen->delete();

                return response()->json(['code' => 1]);
            } catch (Exception $e) {
                return response()->json([
                    'code' => -1,
                    'message' => Lang::get(
                        'No se ha podido completar esta operación en este momento, le rogamos lo intente más tarde.'
                    )
                ]);
            }
        }
    }
}
