<?php

namespace Tests\Unit\Fikrea;

use Tests\TestCase;

use Fikrea\Altiria as Sms;

class AltiriaTest extends TestCase
{
    /**
     * Test de configuración del servicio de envío de SMS de Altiria
     *
     * @return void
     */
    /** @test */
    public function config():void
    {
        $sms = new Sms;

        $this->assertEquals($sms->url, 'http://www.altiria.net/api/http');
    }

    /**
     * Comprueba que se puede obtener el número de créditos disponibles
     *
     * @return void
     */
    /** @test */
    public function credits():void
    {
        $sms = new Sms;

        $credits = $sms->getCredit();

        echo "CREDITO : $ $credits";

        $this->assertIsFloat($credits);

        // En testing el mensaje SMS real no es enviado
        // Pero si el método se ha ejecutado con éxito devuelve true
        // $response = $sms->send(config('company.contact.phone'), 'Mensaje de prueba');
        
        $response = $sms->send(
            config('company.mikel.phone'),
            'Mensaje de prueba desde AltiriaTest fikrea.com'
        );

        $this->assertTrue($response);
    }
}
