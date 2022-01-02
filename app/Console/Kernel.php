<?php

/**
 * Ejecución de las tareas programadas del sitio web
 *
 * @see https://laravel.com/docs/8.x/scheduling
 *
 * Para el funcionamiento de este controlador debemos añadir una tarea a cron en el servidor
 * que se ejecute cada minuto y ejecute el comando de artisan schedule:run
 *
 * Para ello ejecutar crontab -e y añadir la siguiente línea:
 *
 *      * * * * * cd /{path-to-your-project} && php artisan schedule:run >> /dev/null 2>&1
 *
 * Ejemplo:
 *
 *      * * * * * cd /var/www/fikrea && php artisan schedule:run >> /dev/null 2>&1
 *
 * En desarrollo no hace falta usar cron, sino ejecutar en primer plano:
 *
 *      php artisan schedule:work
 *
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Console;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * Lista de comandos de Artisan proveídos para la aplicación
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define la programación de tareas
     *
     * @param Schedule $schedule El programador de tareas
     *
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        //
        // Establece como inactivos a todos los firmantes de la app
        //
        // @see App\Console\Commands\InactivateSigners
        //
        $schedule->command('signers-activity:clean')->dailyAt('00:01');

        //
        // Verifica el estado de los usuarios admin de la app
        //
        // @see App\Console\Commands\CheckForAdminUsers
        //
        $schedule->command('check:admin-users')->dailyAt('00:30');

        //
        // Llama al recolector de basura
        //
        // Elimina los archivos antiguos no utilizados del almacenamiento público local
        //
        // @see App\Console\Commands\GarbageCollector
        //
        $schedule->command('garbage-collector:run')->dailyAt('02:00');

        //
        // Envía notificaciones de firmantes y usuarios
        // sobre vencimiento de documentos aportados, con enlace para proveer
        // un nuevo documento
        //
        // @see App\Console\Commands\CheckForExpiringAportedDocuments
        //
        $schedule->command('check:expiring-aported-files')->dailyAt('06:30');

        //
        // Envía notificaciones de recordatorio a los usuarios
        // invitándoles a que procedan a validar y firmar los documentos propuestos
        //
        // @see App\Console\Commands\SendReminderPendingValidation
        //
        $schedule->command('send:reminderPendingValidation')->dailyAt('13:00');

        // Eliminar los ficheros subidos en estado bloqueado que llevan más de 24 horas en ese estado
        $schedule->command('locked-files:clean')->hourly();
    }

    /**
     * Registra los comandos para la aplicación
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');
        include base_path('routes/console.php');
    }
}
