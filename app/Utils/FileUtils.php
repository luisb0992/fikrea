<?php

namespace App\Utils;

use App\Models\File;
use App\Models\Guest;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FileUtils
{
    /**
     * Retorna todos los ficheros contenidos en una carpeta, descendiendo de manera recursiva todos los posibles niveles
     *
     * @param File   $folder   La carpeta a partir de la que se inicia la búsqueda
     * @param string $fullPath La base de la ruta de todos los archivos (predeterminado a la propia carpeta)
     * @return array                        El arreglo de los archivos contenidos en la carpeta, a todos los niveles
     */
    public static function getInnerFiles(File $folder, string $fullPath = '.'): array
    {
        $files = [];

        foreach ($folder->files as $file) {
            if ($file->is_folder) {
                $files += self::getInnerFiles($file, $fullPath . '/' . $file->name);
            } else {
                $file->real_path = $file->path;
                $file->full_name = $fullPath . '/' . $file->name;

                $files[$file->id] = $file;
            }
        }

        return $files;
    }

    /**
     * Elimina el estado de bloqueado de todos los ficheros que sea posible, teniendo en cuenta el espacio disponible y
     * la suscripción del usuario. Este método debe de ser llamado siempre que ocurra un cambio en el espacio
     * disponible para el usuario (porque se elimine uno o varios fichero, o porque se cambie de suscripción).
     */
    public static function unlockFiles(): void
    {
        // Obtiene el usuario actual
        $user = Auth::user() ?? Guest::user();

        // Obtener los ficheros bloqueados por más de 24 horas
        $locked = DB::table('files')->where('locked', true)->where('user_id', $user->id)->get(['id', 'size']);

        $diskSpace = $user->disk_space;

        // Mientras haya ficheros bloqueados y espacio disponible, se irán quitando el bloqueo a los ficheros subidos
        // en ese estado
        while (($file = $locked->shift()) && ($diskSpace->used < $diskSpace->available)) {
            if ($diskSpace->used + $file->size <= $diskSpace->available) {
                // Desbloquear este fichero
                DB::table('files')->where('id', $file->id)->update(['locked' => false]);

                // Actualizar el espacio disponible, para la próxima iteración
                $diskSpace->used += $file->size;
            }
        }
    }

    /**
     * Coleccionar datos de un CSV dado y devolverlos como un arreglo
     *
     * @param string $filename
     * @param string $delimiter
     * @param int    $skipLines
     * @return array|bool
     */
    public static function seedFromCSV(string $filename, string $delimiter = ',', int $skipLines = 0)
    {
        if (!file_exists($filename) || !is_readable($filename)) {
            return false;
        }

        $header = null;
        $data = [];

        if (($handle = fopen($filename, 'r')) !== false) {
            for ($i = 0; $i < $skipLines; $i++) {
                fgetcsv($handle);
            }

            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header) {
                    $header = $row;
                } else {
                    try {
                        $data[] = array_combine($header, $row);
                    } catch (Exception $e) {
                        //
                    }
                }
            }

            fclose($handle);
        }

        return $data;
    }
}
