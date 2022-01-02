<?php

/**
 * Define el comando:
 *
 * php artisan garbage-collector:run
 *
 * El recolector de basura
 * Elimina archivos no necesarios del almacenamiento ṕublico local servidor
 *
 * @copyright 2021 Retail Servicios Externos SL
 * @author javieru <javi@gestoy.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

/**
 * Librerías de Fikrea
 */
use Fikrea\AppStorage;
use Illuminate\Support\Collection;

class GarbageCollector extends Command
{
    /**
     * Signatura y nombre del comando
     *
     * @var string
     */
    protected $signature = 'garbage-collector:run';

    /**
     * Descripción del comando de consola
     *
     * @var string
     */
    protected $description = 'Elimina archivos no necesarios del almacenamiento en el servidor';

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
    public function handle(): void
    {
        // Realiza la limpieza en todos los casos
        $this->clean();

        // Si se dispone de almacenamiento S3, realiza la limpieza de archivos para este tipo de almacenamiento
        if (AppStorage::isS3()) {
            $this->cleanS3();
        }
    }

    /**
     * Obtiene los archivos de una carpeta del almacenamiento público cuya
     * antigueddad en días sea superior a la indicada (por defecto, un día)
     *
     * @param string|array $folders             La ruta de la carpeta relativa a la carpeta de almacenamiento
     *                                          público.
     *                                          Por ejemplo: documents/images
     *                                          o una lista de carpetas
     *                                          Por ejemplo: ['documents/images', 'documents/converted']
     *
     * @param int    $days                      El número de días de antiguedad de los archivos
     *                                          Por defecto, es un día
     */
    protected function getOldFiles($folders, int $days = 1): Collection
    {
        // Si se da como argumento una única carpeta se crea una lista de un único elemento con la carpeta dada
        if (is_string($folders)) {
            $folders = [$folders];
        }

        // La colección de archivos que se va eliminar
        $files = collect();

        // Los archivos de la carpeta con una antiguedad superior a la indicada en dias
        foreach ($folders as $folder) {
            $files = $files->merge(
                collect(
                    Storage::disk('public')
                    ->listContents($folder)
                )->filter(fn ($file) => $file['timestamp'] < Carbon::now()->subDays($days)->timestamp)
            );
        }

        return $files;
    }

    /**
     * Elimina la colección de archivos
     *
     * @param  Colelction $files                Una colección de archivos a ser eliminados de forma permanente
     *
     * @return void
     */
    protected function removeFiles(Collection $files): void
    {
        // Obtiene las rutas de los archivos a eliminar
        $filepaths = $files->map(fn ($file) => "{$file['dirname']}/{$file['basename']}")->toArray();

        // Elimina los archivos
        Storage::disk('public')->delete($filepaths);
    }

    /**
     * Realiza la limpieza de archivos no utilizados con independencia del medio de almacenamiento de archivos
     * utilizado
     *
     * @return void
     */
    public function clean(): void
    {
        // Obtiene y elimina los archivos de imágenes antiguos del almacenamiento público local
        $files = $this->getOldFiles(config('documents.folder.images'));

        $this->removeFiles($files);
    }

    /**
     * Realiza la limpieza de archivos innecesarios cuando se utiliza un sistema de almacenamiento S3
     * Esto elimina archivos intermeidos utilizado en el procesamiento en el servidor
     *
     * @return void
     */
    public function cleanS3(): void
    {
        // Obtiene los documentos antiguos del almacenamiento público local
        $files = $this->getOldFiles(
            [
                config('documents.folder.original'),    // Elimina copias de documentos originales
                config('documents.folder.converted'),   // Elimina copias de documentos resultantes de la conversión
                config('documents.folder.signed'),      // Elimina copias de documentos generados en el proceso de firma
            ]
        );

        // Elimina los archivos obtenidos
        $this->removeFiles($files);
    }
}
