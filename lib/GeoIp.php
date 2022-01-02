<?php

/**
 * La Clase GeoIp
 *
 * Proporciona la localización de una dirección IP
 *
 * Basado en geoip-location:
 *
 * @link https://github.com/victorybiz/geoip-location
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 *
 */

namespace Fikrea;

use Victorybiz\GeoIPLocation\GeoIPLocation;

class GeoIp extends AppObject
{
    /**
     * La dirección Ip
     *
     * @var string
     */
    protected string $ip;

    /**
     * El continente o null si no se ha determinado
     *
     * @var string|null
     */
    protected ?string $continent;

    /**
     * El país o null si no se ha determinado
     *
     * @var string|null
     */
    protected ?string $country;

    /**
     * La región o null si no se ha determinado
     *
     * @var string|null
     */
    protected ?string $region;
    
    /**
     * La ciudad o null si no se ha determinado
     *
     * @var string|null
     */
    protected ?string $city;

    /**
     * La latitud aproximada o null si no se ha determinado
     *
     * @var float|null
     */
    protected ?float $latitude;

    /**
     * La longitud aproximada o null si no se ha determinado
     *
     * @var float|null
     */
    protected ?float $longitude;

    /**
     * El constructor
     *
     * @param string $ip                        La dirección Ip
     */
    public function __construct(string $ip)
    {
        $geoip = new GeoIPLocation(['ip' => $ip]);
        
        parent::__construct(
            [
                'ip'            => $ip,
                'continent'     => $geoip->getContinent(),
                'country'       => $geoip->getCountry(),
                'region'        => $geoip->getRegion(),
                'city'          => $geoip->getCity(),
                'latitude'      => $geoip->getLatitude(),
                'longitude'     => $geoip->getLongitude(),
            ]
        );
    }
}
