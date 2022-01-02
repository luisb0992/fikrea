<?php

namespace App\Http\Controllers;

use App\Models\FileLog;
use App\Models\FileSharing;
use App\Models\Guest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Throwable;

class FileSharingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\FileSharing $fileSharing
     * @return Response
     */
    public function show(FileSharing $fileSharing)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\FileSharing $fileSharing
     * @return Response
     */
    public function edit(FileSharing $fileSharing)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\FileSharing  $fileSharing
     */
    public function update(Request $request, FileSharing $fileSharing)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function destroy(int $id): RedirectResponse
    {
        //
        $sharing = FileSharing::findOrFail($id);

        $this->authorize('destroy', $sharing);

        DB::transaction(
            static function () use ($sharing) {
                foreach ($sharing->files as $file) {
                    // Registrar la acción para cada fichero en la selección
                    FileLog::create(
                        [
                            'file_id' => $file->id,
                            'action' => 'ELIMINAR COMPARTICIÓN',
                            'description' => sprintf(
                                'Eliminado de la compartición de TÍTULO: %s. DESCRIPCIÓN: %s.',
                                $sharing->title,
                                $sharing->description
                            ),
                        ]
                    );
                }

                // Eliminar el token enviado a cada usuario, en caso de que se hubiera creado
                DB::table('file_sharing_contacts')->where('file_sharing_id', $sharing->id)->delete();

                // Eliminar por completo el historial para esta compartición
                DB::table('file_sharing_histories')->where('file_sharing_id', $sharing->id)->delete();

                // Por último, eliminar la compartición
                $sharing->delete();
            }
        );

        // Redirigir a la vista anterior
        return redirect()->back()->with('message', Lang::get('La compartición se ha eliminado con éxito'));
    }

    /**
     * Registrar el acceso a los ficheros de una compartición
     *
     * @param int $id
     * @return JsonResponse
     */
    public function log(int $id): JsonResponse
    {
        // Obtiene el usuario
        $user = Auth::user() ?? Guest::user();

        $sharing = FileSharing::where('user_id', $user->id)->where('id', $id)->first();

        $route = route('workspace.set.share', ['token' => $sharing->token]);
        foreach ($sharing->files as $file) {
            // Registrar la acción para cada fichero en la selección
            FileLog::create(
                [
                    'file_id' => $file,
                    'action' => 'COPIAR URL',
                    'description' => sprintf(
                        'TÍTULO: %s. DESCRIPCIÓN: %s. ENLACE: <a href="%s">%s</a>',
                        $sharing->title,
                        $sharing->description,
                        $route,
                        $route
                    ),
                ]
            );
        }

        return response()->json($sharing);
    }
}
