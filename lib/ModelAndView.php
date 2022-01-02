<?php

namespace Fikrea;

/**
 * La clase ModelAndView
 *
 * Renderiza una página utilizando una plantilla de blade
 *
 * Blade es el motor de creación de plantilla potente
 * A diferencia de otros populares motores de plantillas de PHP,
 * Blade no te impide usar código PHP simple en tus vistas.
 * De hecho, todas las vistas de Blade se compilan en código PHP simple
 * y se almacenan en la memoria caché hasta que se modifiquen.
 *
 * Los archivos Blade View usan la extensión .blade.php y
 * típicamente se almacenan en el directorio views.
 *
 * Para estudiar el motor de plantillas Blade para la construcción de vistas
 * vease:
 *
 * @link https://laravel.com/docs/8.x/blade
 *
 * @copyright 2021 Retail Servicios Externos SL
 * @author javieru <javi@gestoy.com>
 */

class ModelAndView extends AppObject implements Interfaces\Renderizable
{
    /**
     * La vista a renderizar
     *
     * @var string                              Una ruta a la vista
     */
    protected string $view;
    
    /**
     * El modelo
     *
     * @var array                               Colección de objetos a pasar a la vista
     */
    protected array $model = [];

    /**
     * El constructor
     *
     * @param string $view                      La ruta de la vista
     *
     */
    public function __construct(string $view)
    {
        $this->view = $view;
    }

    /**
     * Renderiza una vista tomando una plantilla
     * y un array asociativo con los parámetros a sustituir en la misma
     *
     * @param  array  $model                    Un array asociativo con las sustituciones a realizar en la vista
     * @return string
     */
    public function render(array $model = []):string
    {
        // Incorpora los elementos de la colección pasada al modelo
        foreach ($model as $key => $item) {
            $this->model[$key] = $item;
        }
           
        // Obtiene la vista de Blade
        return view($this->view, $this->model)->render();
    }

    /**
     * Añade elementos a la vista
     *
     * @param array $item                           Una lista de elementos a inyectar en la vista
     * @return self                                 El propio objeto
     *
     * @example
     *
     * Añade los usuarios a la vista:
     *
     * $mav = new ModelAndView('my.custom.view');
     * $mav->append([
     *      'users' => $users,
     * ]);
     * $mav->render();
     *
     */
    public function append(array $items):self
    {
        foreach ($items as $key => $item) {
            $this->model[$key] = $item;
        }

        return $this;
    }

    /**
     * Limpia el código HTML generado
     * Requiere la extensión tidy cargada, si no lo está se devuelve el código HTML de entrada
     *
     * @link https://www.php.net/manual/es/class.tidy.php
     *
     * @param string $html                      El código HTML a reparar
     *
     * @return string                           El código HTML reparado si la extensión ha sido cargada
     *                                          o el mismo codigo de entrada si la extensión no está disponible
     */
    protected function clean(string $html):string
    {
        // Si está la extensión Tidy cargada y no es un fragmento de código HTML la ejecuta,
        // en caso contrario devuelve no aplica tity y se devuelve el código tal cual
        // Un fragmento HTML no posee la etiqueta <html>
        $isHTMLFragment= strpos(strtolower($html), '<html') === false;
        
        if (extension_loaded('tidy') && !$isHTMLFragment) {
            // Cargamos Tidy
            $tidy = new \Tidy;
           
            $tidy->parseString(
                $html,
                [
                    'indent'              => true,     // Autoindentar las líneas
                    'indent-spaces'       => 4,        // Indentación con cuatro espacios en blanco
                    'drop-empty-elements' => false,    // No eliminar las etiquetas vacias
                    'hide-comments'       => true,     // Suprimir los comentarios
                    'wrap'                => 175,      // Ajuste de línea máxima
                    'sort-attributes'     => true,     // Ordenar los atributos alfabéticamente
                ]
            );
            
            // Reparamos el código
            $tidy->cleanRepair();
            
            return $tidy;
        } else {
            return $html;
        }
    }
}
