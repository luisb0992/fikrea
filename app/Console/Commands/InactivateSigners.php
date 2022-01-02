<?php

/**
 * Define el comando:
 *
 * Php artisan signers-activity:clean
 *
 * Cuando agregamos los campos activity en la tabla document y document_requests
 * para controlar las acciones de los firmantes en tiempo real sobre los mismos
 * estas pueden quedarse colgadas cuando este cierra su navegador inadecuadamente.
 * Por eso todos los dias, a las 00:01 inactivamos todas estas actividades
 * para evitar falsos positivos.
 *
 * @author    rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos SL
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Modelos requeridos
 */
use App\Models\Validation;
use App\Models\Process;
use App\Models\Document;
use App\Models\DocumentRequest;

class InactivateSigners extends Command
{
    /**
     * Signatura y nombre del comando
     *
     * @var string
     */
    protected $signature = 'signers-activity:clean';

    /**
     * La descripción del comando
     *
     * @var string
     */
    protected $description = 'Anula la actividad de los firmantes sobre procesos
        de firma y solicitud de documentos';

    /**
     * El constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ejecuta el comando de consola
     *
     * @return void
     */
    public function handle() : void
    {
        $this->setActivityToNull();
    }

    /**
     * Cambia los campos active de las validaciones
     * y activity de los documentos a nulos
     * Y el active y activity en las solicitudes de documentos
     *
     * @return void
     */
    protected function setActivityToNull() : void
    {
        // Inactivo los procesos
        foreach (Process::where('active', 1)
            ->orWhere('activity', '<>', null)
            ->get() as $process) {
            $process->active = 0;
            $process->activity = json_encode([]);
            $process->save();
        };
    }
}
