<?php

namespace Tests\Unit\System;

use Tests\TestCase;

class ImagickTest extends TestCase
{
    /**
     * Comprueba que Imagick está cargada en el sistema
     *
     * @return void
     */
    /** @test */
    public function testImagick():void
    {
        $this->assertEquals(extension_loaded('Imagick'), true);
    }
}
