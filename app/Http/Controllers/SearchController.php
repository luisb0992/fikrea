<?php

/**
 * SearchController
 *
 * Controlador para la búsqueda de archivos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

/**
 * Modelos requeridos
 */

use App\Models\User;
use App\Models\Guest;

/**
 * Fikrea
 */

use Fikrea\ModelAndView;

class SearchController extends Controller
{
    use Traits\Statistical;

    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Muestra los resultados de una búsqueda de archivos y documentos
     *
     * @param string $query La cadena de búsqueda
     *
     * @return string                           Una vista
     */
    public function findDocument(string $query): string
    {
        // Obtenemos el usuario
        $user = Auth::user() ?? Guest::user();

        // Obtenemos los documentos para firmar cuyo nombre, comentario o contenido se ajustan a la búsqueda
        $documents = $user->documents()->where('name', 'like', "%{$query}%")
            ->orWhere('comment', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%")
            ->get();

        // Obtenemos los archivos subidos se ajustan a la búsqueda
        // Sin tener en cuenta en qué carpeta están contenidos.
        $files = $user->files(null, false)->where('name', 'like', "%{$query}%")
            ->get();

        // Muestra la vista con los reultados de la búsqueda
        $mav = new ModelAndView('dashboard.search.search-list');

        return $mav->render(
            [
                'documents' => $documents,                              // Documentos encontrados
                'files'     => $files,                                  // Archivos encontrados
                'query'     => $query,                                  // La cadena de consulta
                'results'   => $documents->count() + $files->count(),   // El número de resultados totales
            ]
        );
    }

    /**
     * Muestra los resultados de una búsqueda de usuarios
     *
     * @param string $query La cadena de búsqueda
     *
     * @return string                           Una vista
     */
    public function findUser(string $query): string
    {
        $users = User::where('name', 'like', "%{$query}%")
            ->orWhere('lastname', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orderBy('lastname');

        // Muestra la vista con los reultados de la búsqueda
        $mav = new ModelAndView('backend.search.search-list');

        // Añade las estadísticas básicas a la vista
        $this->appendStats($mav);

        return $mav->render(
            [
                'users'     => $users->paginate(10),                    // Los usuarios encontrados
                'query'     => $query,                                  // La cadena de consulta
                'results'   => $users->count(),                         // El número de resultados totales
            ]
        );
    }
}
