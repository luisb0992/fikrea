<?php

namespace Tests\Unit\Fikrea;

use Tests\TestCase;

use Fikrea\I18n;

class I18nTest extends TestCase
{
    /**
     * Comprueba que los literales de idioma son obtenidos y se pueden traducir
     *
     * @return void
     */
    /** @test */
    public function doTranslations(): void
    {
        $literals = I18n::getStringsToTraslate();

        $this->assertGreaterThan(0, count($literals));
    }
}
