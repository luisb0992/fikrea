<?php

namespace Tests\Unit\Fikrea;

use Tests\TestCase;

use Fikrea\GeoIp;

class GeoIpTest extends TestCase
{
    /**
     * Test de geolocalización por la dirección Ip
     *
     * 07-05-2021
     * #ip: "195.53.12.1"
     * #continent: "Europe"
     * #country: "France"
     * #region: "Île-de-France"
     * #city: "Paris"
     * #latitude: 48.8607
     * #longitude: 2.3281
     * @return void
     */

    /** @test */
    public function geoIp():void
    {
        $geoip = new GeoIp('195.53.12.1');

        $this->assertEquals('Spain', $geoip->country);
        $this->assertEquals('Madrid', $geoip->region);
        $this->assertEquals('San Sebastián de los Reyes', $geoip->city);
    }
}
