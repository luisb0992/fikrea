<?php

/**
 * Controlador de Contacto con el Cliente
 *
 * El cliente se pone en contacto con el administrador del sitio a través de un formulario
 * que es el clásico formulario de contacto
 *
 * No confudir con el controlador que maneja los contactos de un usuario
 *
 * @author    javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Http\Controllers;

use Fikrea\ModelAndView;
use Illuminate\Http\JsonResponse;

class CustomerContactController extends Controller
{
    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Muestra la vista con el formulario de contacto
     *
     * @return string
     */
    public function show(): string
    {
        // Muestra la lista de contactos del usuario
        $mav = new ModelAndView('dashboard.customer-contact');

        return $mav->render();
    }

    /**
     * Guarda el mensaje de contacto del cliente
     *
     * El cliente se pone en contacto con el administrador del sitio a través de un formulario
     *
     * @return JsonResponse                     Una respuesta JSON
     */
    public function save(): JsonResponse
    {
        // Obtiene el contacto
        $contact = (object) request()->only('email', 'subject', 'content');

        // Envía un mensaje de correo con el contacto al administrador del sitio
        EmailController::sendContactEmail($contact);

        // Envía una respuesta JSON al cliente
        return response()->json(
            [
                'message'  => "Mail sended from {$contact->email}",
            ]
        );
    }
}
