<?php

namespace Fikrea;

/**
 * La Clase Mail
 *
 * Envía un simple correo electrónico
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 *
 * @example
 *
 * use Fikrea\Mail;
 *
 * $mail = new \Fikrea\Mail(
 *      [
 *          'to'        => 'usuario@fikrea.com',
 *          'subject'   => 'Mensaje de prueba',
 *          'message'   => 'Trabajar no es malo, lo malo es tener que trabajar',
 *      ]
 * );
 *
 * $mail->send();
 *
 */

use Illuminate\Support\Facades\Mail as Mailer;

class Mail extends AppObject
{
    /**
     * El destinatario
     *
     * @var string                              La dirección de correo del destinatario
     */
    protected string $to;

    /**
     * El asunto
     *
     * @var string                              El asunto
     */
    protected string $subject;

    /**
     * El contenido del mensaje
     *
     * @var string                              El contenido del mensaje
     */
    protected string $message;

    /**
     * El constructor
     *
     * @param array                             Una lista de valores
     *                                              to     : (string) El destinatario
     *                                              subject: (string) El asunto
     *                                              message: (string) El contenido
     *
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);
    }

    /**
     * Envía un sencillo mensaje de correo
     *
     * @return void
     */
    public function send(): void
    {
        Mailer::send([], [], function ($message) {
            $message->to($this->to)
                    ->subject($this->subject)
                    ->setBody($this->message);
        });
    }
}
