<?php

/**
 * La Clase Environment
 *
 * Proporciona el entorno de ejecución de la aplicación
 *
 * Puede ser un entorno local, producción, testing
 * El entorno de ejecución se fija en el archivo .env:
 *
 * APP_ENV=local
 *
 * @link https://laravel.com/docs/8.x/configuration#environment-configuration
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 *
 *
 * @example
 *
 * use Fikrea\Environmet;
 *
 * Comprueba si es entorno de ejecución actual de la aplicación es de producción
 *
 * if (Environment::isProdution()) {
 *      // El entorno es de producción
 * }
 *
 * Obteniene el entorno actual de ejecución de la aplicación:
 *
 * $environment = Environment::get();
 *
 */

namespace Fikrea;

class Environment extends AppObject
{
    /**
     * Entorno local
     *
     * @var string
     */
    public const LOCAL       = 'local';

    /**
     * Entorno de proudcción
     *
     * @var string
     */
    public const PRODUCCTION = 'production';
    
    /**
     * Entorno de testing
     *
     * @var string
     */
    public const TEST        = 'testing';
    
    /**
     * El constructor no es accesible directamente
     *
     */
    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * Obtiene el entorno actual de ejecución de la aplicación
     *
     * @return string                           El entorno actual de ejecución de la aplicación
     */
    public static function get(): string
    {
        return config('app.env');
    }

    /**
     * Si el entorno es local
     *
     * @return bool                             true si el entorno es de desarrollo (local)
     *                                          false en caso contrario
     */
    public static function isLocal(): bool
    {
        return config('app.env') == 'local';
    }

    /**
     * Si el entorno es de producción
     *
     * @return bool                             true si el entorno es de producción (real)
     *                                          false en caso contrario
     */
    public static function isProduction(): bool
    {
        return config('app.env') == 'production';
    }

    /**
     * Si el entorno es de testing
     *
     * @return bool                             true si el entorno es de testing (test)
     *                                          false en caso contrario
     */
    public static function isTesting(): bool
    {
        return config('app.env') == 'testing';
    }
}
