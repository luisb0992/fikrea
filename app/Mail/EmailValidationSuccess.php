<?php

/**
 * Correo que se envía al autor de un documento confirmando que un usuario ha validado un documento
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

use App\Models\Validation;

class EmailValidationSuccess extends Mailable
{
    use Queueable, SerializesModels;
    
    /**
     * La validación efectuada
     *
     * @var Validation
     */
    public Validation $validation;

    /**
     * El constructor
     *
     * @param Validation $validation            La validación efectuada
     */
    public function __construct(Validation $validation)
    {
        $this->validation = $validation;
    }

    /**
     * Construye el mensaje
     *
     * @return self                             El propio objeto
     */
    public function build(): self
    {
        $this->subject(
            Lang::get(
                ':app le informa que :name :lastname <:email>
                ha completado una validación sobre un documento',
                [
                    'app'       => config('app.name'),
                    'name'      => $this->validation->signer->name,
                    'lastname'  => $this->validation->signer->lastname,
                    'email'     => $this->validation->signer->email,
                ]
            )
        );

        return $this->view(
            'dashboard.mail.confirm-validation-done',
            [
                'validation'      => $this->validation,
            ]
        );
    }
}
