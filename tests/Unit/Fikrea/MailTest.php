<?php

namespace Tests\Unit\Fikrea;

use Tests\TestCase;

use Fikrea\Mail;

class MailTest extends TestCase
{
    /**
     * Comprueba que se puede enviar correo
     *
     * @return void
     */
    /** @test */
    public function send(): void
    {
        $mail = new Mail(
            [
                'to'        => 'usuario@fikrea.com',
                'subject'   => 'Mensaje de prueba',
                'message'   => 'Trabajar no es malo, lo malo es tener que trabajar',
            ]
        );
        
        $this->assertEquals('usuario@fikrea.com', $mail->to);
        $this->assertEquals('Mensaje de prueba', $mail->subject);
        $this->assertEquals('Trabajar no es malo, lo malo es tener que trabajar', $mail->message);

        try {
            $mail->send();
            $this->assertTrue(true);
        } catch (\Exception $e) {
            $this->assertTrue(false);
        }
    }
}
