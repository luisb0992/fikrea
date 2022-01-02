<?php

/**
 * Manejador de excepciones
 *
 * @link https://laravel.com/docs/8.x/errors
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

/**
 * Excepciones requeridas
 */
use Illuminate\Database\QueryException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Reporta una execpción
     *
     * @param  Throwable  $exception            Una excepción
     *
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Registro de excepciones
     *
     * @return void
     */
    public function register():void
    {
        //
    }

    /**
     * Convierte una excepción en una respuesta HTTP
     *
     * @param  Request   $request               La solicitud
     * @param  Throwable $exception             La excepción
     *
     * @return Response                         Una respuesta
     *
     * @throws Throwable                        Una excepción
     */
    public function render($request, Throwable $exception)
    {
        // Comprueba la conexión a la base de datos
        if ($this->ifDataBaseConnectionFailed($exception)) {
            return response('Error en la conexión a la base de datos', 503);
        }
    
        return parent::render($request, $exception);
    }

    /**
     * Comprueba si ha fallado la conexión a la base de datos
     *
     * Bien porque la base de datos se encuentra caída, o las credenciales de
     * acceso no son válidas y el acceso ha sido denegado
     *
     * Si la conexión falla se devuelve un error HTTP/503 Service Unavailable
     *
     * @param \Throwable $exception             Una excepción
     *
     * @return bool                             true si la conexión a la base de datos ha fallado
     *                                          false en caso contrario
     */
    protected function ifDataBaseConnectionFailed(Throwable $exception):bool
    {
        if ($exception instanceof QueryException) {
            // Comprueba si el mensaje de error es el [1045]
            $message= $exception->getMessage();
            
            return strpos($message, '[1045]') !== false;
        }
        
        return false;
    }
}
