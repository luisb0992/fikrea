<?php

namespace Tests\Unit\Models;

use Tests\TestCase;

use App\Models\User;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    /** @test */
    public function testUser():void
    {
        // Crea un usuario (mock)
        $user = User::factory()->make();

        // Cambia su nombre
        $user->name = 'Jonh Daytona';
    
        // Comprueba que el nombre del usuario ha cambiado
        $this->assertEquals($user->name, 'Jonh Daytona');
    }
}
