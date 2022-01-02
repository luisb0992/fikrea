<?php

namespace Tests\Unit\Fikrea;

use Tests\TestCase;

use Fikrea\Mobile;

class MobileTest extends TestCase
{
    /**
     * Test para verificar si la conexión actual se está realizado desde un dispositivo móvil o tablet o no
     *
     * @return void
     */
    /** @test */
    public function testMobile(): void
    {
        $this->assertTrue(Mobile::isNotMobile());
    }
}
