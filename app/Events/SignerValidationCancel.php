<?php

/**
 * Evento SignerValidationDone
 *
 * Evento cuando un firmante de un documento ha realizado una validación
 *
 * @author Jonathan Sanchez <jonathanch1991@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Events;

use Illuminate\Queue\SerializesModels;

use App\Models\Validation;

class SignerValidationCancel
{
    use SerializesModels;

    /**
     * La validación rechazada
     *
     * @var Validation
     */
    public Validation $validation;

    /**
     * El constructor
     *
     * @param Validation $validation            La validación rechazada
     */
    public function __construct(Validation $validation)
    {
        $this->validation = $validation;
    }
}
