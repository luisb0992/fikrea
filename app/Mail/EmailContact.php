<?php

/**
 * Correo para el administrador con un contacto
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Lang;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Models\Contact;

class EmailContact extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * El Contacto
     *
     * @var object
     */
    public object $contact;

    /**
     * Envía un mensaje al administrador del sitio informándole de un contacto
     *
     * @param object $contact                   El contacto
     */
    public function __construct(object $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Construye el mensaje
     *
     * @return self                             El propio objeto
     */
    public function build():self
    {
        $this->subject(
            Lang::get(
                ':app le informa que un usuario desea contactar usted',
                [
                    'app' => config('app.name'),
                ]
            )
        );

        return $this->view('landing.mail.contact', [
            'contact' => $this->contact,
        ]);
    }
}
