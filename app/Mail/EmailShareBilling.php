<?php

/**
 * Correo de envío de los datos de facturacion
 *
 * @author Jonathan Sanchez <jsanchez1991@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

/**
 * Modelos Requeridos
 */

use App\Models\User;

class EmailShareBilling extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * El usuario
     *
     * @var User
     */
    public User $user;

    /**
     * los datos de facturacion
     *
     * @var CompanySharing
     */
    public $company;

    /**
     * El constructor
     *
     * @param User $user                            Los datos del usuario
     * @param $company                              Los datos de facturacion
     */
    public function __construct(User $user, $company)
    {
        $this->user        = $user;
        $this->company     = $company;
    }

    /**
     * Construye el mensaje
     *
     * @return self                             El propio objeto
     */
    public function build(): self
    {
        $this->subject(
            Lang::get(':app. El usuario :name :lastname :email ha compartido sus datos de facturacion con usted', [
                'app'           => config('app.name'),
                'name'          => $this->user->name,
                'lastname'      => $this->user->lastname,
                'email'         => $this->user->email,
            ])
        );

        return $this->view('dashboard.mail.share-billing', [
            'company'   => $this->company
        ]);
    }
}
