<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * Comprueba que la página de login está activa
     *
     * @return void
     */
    /** @test */
    public function logingPageIsAlive(): void
    {
        $response = $this->followingRedirects()->get(route('dashboard.login'));
        
        $response->assertStatus(200);
    }

    /**
     * Comprueba si el usuario usuario@fikrea.com puede hacer login
     *
     * @return void
     */
    /** @test */
    public function loginSuccess(): void
    {
        // Credenciales válidas para el usuario Fikrea
        $credential =
            [
                'email'    => 'usuario@fikrea.com',
                //'password' => 'Demo.1234'
                'password' => 'F1kre@*-+'
            ];

        // Se espera una redirección a la zona de usuario o dashboard
        $response = $this->post('login', $credential);

        $response->assertRedirect('/dashboard');
    }
}
