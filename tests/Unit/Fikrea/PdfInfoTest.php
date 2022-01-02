<?php

namespace Tests\Unit\Fikrea;

use Tests\TestCase;

use Illuminate\Support\Facades\Storage;

use Fikrea\PdfInfo;

class PdfInfoTest extends TestCase
{
    /**
     * Obtiene el número de páginas de un documento PDF
     *
     * @return void
     */
    /** @test */
    public function numberOfPagesunit(): void
    {
        $path = Storage::disk('test')->path('example-5-pages.pdf');

        // COmprobar si el documento PDF posee cinco páginas
        $pdfInfo = new PdfInfo($path);

        $this->assertEquals(5, $pdfInfo->pages());
    }
}
