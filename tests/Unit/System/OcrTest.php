<?php

namespace Tests\Unit\System;

use Tests\TestCase;
use Illuminate\Support\Facades\Storage;

use Fikrea\Ocr;

class OcrTest extends TestCase
{
    /**
     * Comprueba a leer el contenido de una imagen transformándola en texto
     *
     * @return void
     */
    /** @test */
    public function testOcr():void
    {
        // Reconocimiento óptico de un texto en inglés
        $file = Storage::disk('test')->path('text-ocr.jpg');
        
        $ocr  = new Ocr($file, 'en');
        $text = $ocr->run();

        $this->assertStringContainsString('I showed him an example', $text);

        // Reconocimiento óptico de un texto en español
        $file = Storage::disk('test')->path('text-ocr-spanish.png');

        $ocr  = new Ocr($file, 'es');
        $text = $ocr->run();

        $this->assertStringContainsString('Requerir inicio de sesión utilizando Autenticación de contraseña', $text);
    }
}
