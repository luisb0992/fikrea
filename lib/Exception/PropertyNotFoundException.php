<?php

namespace Fikrea\Exception;

/**
 * La clase PropertyNotFoundException
 * Se lanza cuando un objeto carece de la propiedad indicada
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 */
class PropertyNotFoundException extends BaseException
{
    /**
     * @var string                          El nombre de la propiedad
     */
    protected string $property;

    /**
     * @var string                          El nombre de la clase
     */
    protected string $class;

    /**
     * El constructor.
     *
     * @param  string   $property           La propiedad
     * @param  string   $class              La clase
     *
     */
    public function __construct(string $property, string $class)
    {
        parent::__construct(
            "La propiedad {$property} no ha sido definida en la instancia de la clase {$class}"
        );
        $this->property= $property;
        $this->class= $class;
    }
}
