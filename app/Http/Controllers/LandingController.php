<?php

/**
 * LandingController
 *
 * Controlador de la página landing que incluye las acciones
 * para el acceso (login), registro
 *
 * @author    javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Utils\FileUtils;
use Carbon\Carbon;

/**
 * Modelos requeridos
 */

use App\Models\Plan;
use App\Models\User;
use App\Models\Document;
use App\Models\File;
use App\Models\FileSharing;
use App\Models\FileSharingContact;

/**
 * Fikrea
 */

use Fikrea\ModelAndView;

class LandingController extends Controller
{

    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Muestra la langing page
     *
     * @return string
     */
    public function index(): string
    {
        // Renderiza la vista de la página landing
        $mav = new ModelAndView('landing.home');

        // Se devuelven los planes sin incluir el plan creado para usuarios especiales
        // nombrado FIKREA, con 50 Gib de Capacidad de almacenamiento
        $plans = Plan::where('id', '<>', 3)->get();

        return $mav->render(
            [
                // Planes
                'plans' => $plans,

                // Número de documentos (para firma)
                // y archivos subidos
                'files' => File::all()->count() + Document::all()->count(),

                // Número de cuentas de usuario
                // Número total de cuentas incluyendo
                // las de los usuarios invitados
                'accounts' => User::all()->count(),

                // Número de usuarios registrados
                // Es decir, usuarios con cuenta verificada
                'registered' => User::where('email_verified_at', '!=', null)->count(),

                // Espacio ocupado por los archivos y documentos
                // en MB
                'size' => (File::all()->sum('size') + Document::all()->sum('size')) / pow(2, 20),
            ]
        );
    }
}
