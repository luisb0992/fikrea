<?php

namespace Fikrea;

/**
 * Excepciones requeridas
 */
use Fikrea\Exception\PropertyNotFoundException;

/**
 * Clase AppObject
 *
 * Es el objeto base de la aplicación.
 * La mayor parte de los objetos heredan de esta clase, implementado ciertas funcionalidades:
 *
 * <ol>
 *   <li>Acceso directo a propiedades privadas y potegidas de forma transparente.</li>
 *   <li>Setters y getters automáticos, que pueden ser redefinidos.</li>
 *   <li>Conversión del objeto en un array.</li>
 * </ol>
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
*/
abstract class AppObject
{
    /**
     * El constructor
     *
     * @param array $data                   Un array de propiedades que construyene un objeto
     */
    public function __construct(array $data = [])
    {
        foreach ($data as $property => $value) {
            try {
                $this->$property= $value;
            } catch (PropertyNotFoundException $e) {
                throw $e;
            }
        }
    }

    /**
     * El método mágico __set
     *
     * Establece un acceso directo a propieades
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
     
        if (property_exists($this, $property) || property_exists($this, '_'.$property)) {
            $set_method= 'set'. ucfirst(ltrim($property, '_'));
            if (method_exists($this, $set_method)) {
                call_user_func_array([$this, $set_method], [$value]);
            } else {
                $this->$property= $value;
            }
        } else {
            throw new PropertyNotFoundException($property, get_called_class());
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
        if (property_exists($this, $property) || property_exists($this, '_'.$property)) {
            $get_method= 'get'. ucfirst(ltrim($property, '_'));
            if (method_exists($this, $get_method)) {
                return call_user_func_array([$this, $get_method], [$property]);
            } else {
                return $this->$property;
            }
        } else {
            throw new PropertyNotFoundException($property, get_called_class());
        }
    }

    /**
     * Maneja los getters y setters de forma automática
     *
     * @param string $method                    El método
     * @param array  $parameters                Los parámetros de llamada al mismo
     * @return mixed                            Devuelve el valor de retorno del método
     */
    public function __call(string $method, array $parameters)
    {
        // Maneja los setters y getters
        if (strlen($method)>3 && substr($method, 0, 3) == 'set'
                &&
            !method_exists($this, $method)) {
                $property = lcfirst(substr($method, 3));
                $this->$property= $parameters[0];
                return $this;
        } elseif (strlen($method)>3 && substr($method, 0, 3) == 'get'
                &&
            !method_exists($this, $method)) {
                $property = lcfirst(substr($method, 3));
                return $this->$property;
        } else {
            return call_user_func_array([$this, $method], [$parameters]);
        }
    }

    /**
     * Crea un array con las propiedades protegidas y públicas de un objeto
     * omitiendo cualquier propiedad privada
     *
     * @return array                        Un array
     */
    public function toArray():array
    {
        $reflect    = new \ReflectionClass($this);
        $properties = $reflect->getProperties(
            \ReflectionProperty::IS_PUBLIC
                        |
            \ReflectionProperty::IS_PROTECTED
        );

        foreach ($properties as $property) {
            $key= $property->getName();
            $value = $this->$key;
            if (is_subclass_of($value, self::class)) {
                $array[$key]= $value->toArray();
            } else {
                $array[$key]= $value;
            }
        }

        return $array;
    }
}
