<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LandingTest extends DuskTestCase
{
    /**
     * Comprueba la página de Landing
     *
     * @return void
     */
    /** @test */
    public function landingPage():void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/landing')
                    ->assertSee(config('app.name'));
        });
    }
}
