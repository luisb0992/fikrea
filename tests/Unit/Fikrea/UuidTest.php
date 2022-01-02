<?php

namespace Tests\Unit\Fikrea;

use Tests\TestCase;

use Fikrea\Uuid;

class UuidTest extends TestCase
{
    /**
     * Creación de un identificador global único o GUID
     *
     * @return void
     */
    /** @test */
    public function create():void
    {
        $guid = Uuid::create();

        $this->assertMatchesRegularExpression(
            '/[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}/',
            $guid
        );
    }
}
