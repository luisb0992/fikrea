<?php

namespace Tests\Unit\Fikrea;

use Tests\TestCase;

use Illuminate\Support\Facades\Storage;

use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\IOFactory;

class PhpWordTest extends TestCase
{
    /**
     * Comprueba si se puede leer un documento Word
     *
     * @return void
     */
    /** @test */
    public function canReadWordDocument():void
    {
        $documentPath = Storage::disk('test')->path('loremipsum.docx');

        $phpWordReader = \PhpOffice\PhpWord\IOFactory::createReader('Word2007');

        $this->assertTrue($phpWordReader->canRead($documentPath));
    }

    /**
     * Comprueba si se puede convertir en PDF un documento Word
     *
     * @return void
     */
    /** @test */
    public function convertToPdf():void
    {
        // Configuramos PhpWord
        Settings::setPdfRenderer(
            Settings::PDF_RENDERER_MPDF,
            base_path(config('documents.mpdf'))
        );
    
        $documentPath          = Storage::disk('test')->path('loremipsum.docx');
        $documentConvertedPath = Storage::disk('test')->path('loremipsum.pdf');

        // Carga el documento Word
        $phpWord   = IOFactory::load($documentPath, 'Word2007');

        // Crea el PDF utilizando MPDF
        $pdfWriter = IOFactory::createWriter($phpWord, 'PDF');
        $pdfWriter->save($documentConvertedPath);

        // Comrpueba que el archivo generado (PDF) existe
        $this->assertTrue(Storage::disk('test')->exists('loremipsum.pdf'));

        // Carga la información del archivo generado
        $fileinfo = new \SplFileInfo($documentConvertedPath);

        // Comprueba el tamaño del archivo PDF generado
        $this->assertEqualsWithDelta(17271, $fileinfo->getSize(), 10);

        // Borra el archivo PDF
        Storage::disk('test')->delete('loremipsum.pdf');
    }
}
