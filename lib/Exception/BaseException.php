<?php

namespace Fikrea\Exception;

/**
 * La clase BaseException
 * Establece la clase padre de las excepciones de la aplicación
 *
 * @package exception
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 */
class BaseException extends \Exception implements JSONSerializableException
{
    /**
     * El constructor
     * Está protegido, por lo que no se pueden crear excepciones de esta clase
     * directamente
     *
     * @param  string   $message                Un mensaje descriptivo de la excepción
     *
     */
    protected function __construct(string $message)
    {
        // El método heredado getMessage permite obtener el mensaje de la execpción
        // Este método está declarado como final por lo que no se puede sobreescribir
        parent::__construct($message);
    }

    /**
     * El método mágico __set
     *
     * Establece un acceso directo a propiedades
     * creando además getters de forma automática o
     * utilizando los que se han creado de forma explícita
     *
     * @param string $property              El nombre de la propiedad
     * @param mixed  $value                 El valor
     * @return void
     * @throws PropertyNotFoundException    Se lanza cuando la propiedad no existe
     */
    public function __set(string $property, $value):void
    {
        if (property_exists($this, $property)) {
            $set_method= 'set'. ucfirst($property);
            if (method_exists($this, $set_method)) {
                call_user_func_array([$this, $set_method], [$value]);
            } else {
                $this->$property= $value;
            }
        } else {
            throw new PropertyNotFoundException($property, get_class());
        }
    }

    /**
     * El método mágico __get
     *
     * Fija directamente las propieades
     * creando además setters de forma automática o
     * utilizando los que se han creado de forma explícita
     *
     * @param string $property              El nombre de la propiedad
     * @return mixed                        El valor de la propiedad
     * @throws PropertyNotFoundException    Se lanza cuando la propiedad no existe
     */
    public function __get(string $property)
    {
        if (property_exists($this, $property)) {
            $get_method= 'get'. ucfirst($property);
            if (method_exists($this, $get_method)) {
                return call_user_func_array([$this, $get_method]);
            } else {
                return$this->$property;
            }
        } else {
            throw new PropertyNotFoundException($property, get_class());
        }
    }

    /**
     * Serializa la excepción a JSON
     *
     * @return string                           Un objeto JSON
     */
    public function toJSON():string
    {
        return json_encode([
            'message' => $this->getMessage()
        ]);
    }
}
