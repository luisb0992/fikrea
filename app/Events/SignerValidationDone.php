<?php

/**
 * Evento SignerValidationDone
 *
 * Evento cuando un firmante de un documento ha realizado una validación
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Events;

use Illuminate\Queue\SerializesModels;

use App\Models\Validation;

class SignerValidationDone
{
    use SerializesModels;

    /**
     * La validación realizada
     *
     * @var Validation
     */
    public Validation $validation;

    /**
     * El constructor
     *
     * @param Validation $validation            La validación realizada
     */
    public function __construct(Validation $validation)
    {
        $this->validation = $validation;
    }
}
