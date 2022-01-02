<?php

namespace Tests\Unit\Fikrea;

use Tests\TestCase;

use Fikrea\Environment;

class EnvironmentTest extends TestCase
{
    /**
     * Test de identificación del entorno de ejecución de la aplicación
     *
     * @return void
     */
    /** @test */
    public function config():void
    {
        $this->assertEquals(Environment::get(), Environment::TEST);
    }
}
