<?php

namespace App\Events;

use App\Models\Signer;
use App\Models\VerificationForm;
use Illuminate\Queue\SerializesModels;

class VerificationFormCancelEvent
{
    use SerializesModels;

    /**
     * La verificación de datos
     *
     * @var VerificationForm
     */
    public VerificationForm $verificationForm;

    /**
     * El usuario que responde a la verificación de datos
     *
     * @var Signer
     */
    public Signer $signer;

    /**
     * El constructor
     *
     * @param VerificationForm $verificationForm            La verificación de datos
     * @param Signer           $signer                      El usuario "firmante" que
     *                                                      responde a la verificación de datos
     */
    public function __construct(VerificationForm $verificationForm, Signer $signer)
    {
        $this->verificationForm = $verificationForm;
        $this->signer           = $signer;
    }
}
