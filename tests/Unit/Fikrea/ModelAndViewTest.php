<?php

namespace Tests\Unit\Fikrea;

use Tests\TestCase;

use Fikrea\ModelAndView;

class ModelAndViewTest extends TestCase
{
    /**
     * Comprueba que una vista puede ser renderizada
     *
     * @return void
     */
    /** @test */
    public function render(): void
    {
        // Vista sin inyectar parámetros
        $mav = new ModelAndView('errors.404');

        $result= $mav->render();

        $this->assertStringContainsString('Se ha producido un error en la solicitud', $result);

        // Vista con inyección de parámetros
        $mav = new ModelAndView('errors.custom');

        $result= $mav->render(
            [
                'code'      => 1000,
                'title'     => 'Error en la solicitud',
                'message'   => 'Se ha producido un error en la solicitud'
            ]
        );

        $this->assertStringContainsString('Se ha producido un error en la solicitud', $result);
    }
}
