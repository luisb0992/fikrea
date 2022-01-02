<?php

namespace Tests\Unit\System;

use Tests\TestCase;

use Analytics;
use Spatie\Analytics\Period;

class AnalyticsTest extends TestCase
{
    /**
     * Test de la API de Google Analytics
     *
     * @link https://github.com/spatie/laravel-analytics
     *
     * @return void
     */
    /** @test */
    public function analytics():void
    {
        // Visitantes y páginas vistas durante la última semana
        $visitors = Analytics::fetchTotalVisitorsAndPageViews(Period::days(7));

        $this->assertGreaterThan(0, $visitors->count());
    }
}
