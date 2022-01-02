<?php

namespace Tests\Unit\Fikrea;

use Tests\TestCase;

use Fikrea\Browser;

class BrowserTest extends TestCase
{
    /**
     * Obtiene el sistema operativo y el navagador a partir del agente de usuario
     *
     * @return void
     */
    /** @test */
    public function testBrowser(): void
    {
        // Ubuntu/Linux; Firefox 86
        $user_agent = 'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:86.0) Gecko/20100101 Firefox/86.0';

        $browser = new Browser($user_agent);

        $this->assertEquals('Linux', $browser->getOs());
        $this->assertEquals('Firefox 86.0', $browser->getBrowser());

        $user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.150 Safari/537.36 Edg/88.0.705.63';
        
        $browser = new Browser($user_agent);

        $this->assertEquals('Win10', $browser->getOs());
        $this->assertEquals('Edge 88.0', $browser->getBrowser());
        $this->assertEquals('Edge 88.0', $browser->get()->parent);
    }
}
