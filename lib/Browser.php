<?php

/**
 * La Clase Browser
 *
 * Obtiene el sistema y navegador del usuario
 *
 * Para ello se hace uso de la función:
 *
 * https://www.php.net/manual/es/function.get-browser.php
 *
 * Debe estar instalado el archivo browscap.ini en el sistema, que se puede descargar:
 *
 * @see https://www.browscap.org/
 *
 * la versión reducida de este archivo (<1 MB) suele ser suficiente para la mayor parte de los propositos
 *
 * Se puede almacenar browscap.ini en una ruta como:
 *
 * /etc/php/7.4/cli/extra/browscap.ini
 *
 * y configurar en el archivo php.ini, la directivaBlade::component('package-name', PackageNameComponent::class);
 *
 * browscap = /etc/php/7.4/cli/extra/browscap.ini
 *
 * En producción, si se usa apache utilizar la ruta /etc/php7.4/apache2/...
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 *
 */

namespace Fikrea;

class Browser extends AppObject
{
    /**
     * El agente de usuario
     *
     * @var string|null
     */
    protected ?string $user_agent;

    /**
     * Un objeto con el resultado
     *
     * @var object
     */
    protected object $result;
    
    /**
     * El constructor
     *
     * @param string|null $user_agent           El agente de usuario
     *
     */
    public function __construct(?string $user_agent)
    {
        parent::__construct(
            [
                'user_agent' => $user_agent,
                'result'     => get_browser($user_agent),
            ]
        );
    }

    /**
     * Obtiene el sistema operativo
     *
     * @return string                           El sistema operativo del usuario
     */
    public function getOs(): string
    {
        return $this->result->platform;
    }

    /**
     * Obtiene el navegador
     *
     * @return string                           El navegador del usuario
     */
    public function getBrowser(): string
    {
        return $this->result->parent;
    }

    /**
     * Devuelve un objeto con la información del sistema y navegador
     * El objeto de salida tiene la estructura dada por la función get_browser()
     *
     * @see https://www.php.net/manual/es/function.get-browser.php
     *
     * @return object                           Un objeto
     */
    public function get(): object
    {
        return $this->result;
    }
}
