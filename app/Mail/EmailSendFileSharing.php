<?php

/**
 * Correo de envío de la dirección de descarga de un conjunto de archivos compartidos
 *
 * @author javieru <javi@gestoy.com>
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
use App\Models\FileSharing;
use App\Models\FileSharingContact;

class EmailSendFileSharing extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * El usuario
     *
     * @var User
     */
    public User $user;

    /**
     * La compartición de archivos
     *
     * @var string
     */
    public FileSharing $fileSharing;

    /**
     * La dirección de correo del destinatario
     *
     * @var string
     */
    public FileSharingContact $contact;

    /**
     * El constructor
     *
     * @param User $user                            El usuario
     * @param FileSharing $fileharing               La compartición de archivos
     * @param FileSharingContact $contact           El contacto con el que se comparte
     */
    public function __construct(User $user, FileSharing $fileSharing, FileSharingContact $contact)
    {
        $this->user        = $user;
        $this->fileSharing = $fileSharing;
        $this->contact     = $contact;
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
                ':app. El usuario :name :lastname :email ha compartido unos archivos con usted',
                [
                    'app'           => config('app.name'),
                    'name'          => $this->user->name,
                    'lastname'      => $this->user->lastname,
                    'email'         => $this->user->email,
                ]
            )
        );

        return $this->view(
            'dashboard.mail.file-sharing',
            [
                'fileSharing'   => $this->fileSharing,
                'contact'       => $this->contact,
            ]
        );
    }
}
