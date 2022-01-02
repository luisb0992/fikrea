<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingTest extends TestCase
{
    /**
     * Comprueba que la página de landing está activa
     *
     * @return void
     */
    /** @test */
    public function testLandingPageIsAlive():void
    {
        $response = $this->followingRedirects()->get('/');

        $response->assertStatus(200);
    }
    
    /**
     * Comprueba que la página de registro del usuario está activa
     *
     * @return void
     */
    /** @test */
    public function testRegisterPagesIsAlive():void
    {
        $response = $this->followingRedirects()->get(route('dashboard.register'));
        
        $response->assertStatus(200);
    }
}
