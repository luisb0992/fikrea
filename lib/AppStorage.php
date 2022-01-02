<?php

/**
 * La Clase AppStorage
 *
 * Contiene métodos de utilidad para controlar el almacenamiento de la aplicación
 * que puede ser en un servicio de almacenamiento de objetos como S3
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos SL
 *
 */

namespace Fikrea;

use Illuminate\Support\Facades\Storage;

class AppStorage
{
    /**
     * El constructor
     */
    protected function __contruct()
    {
        // No se puede invocar
    }

    /**
     * Comprueba si el Almacanamiento de la aplicación es S3
     *
     * @return bool                             true si el almacenamiento configurado para la aplicación es Amazon S3
     *                                          false en cualquier otro caso
     */
    public static function isS3(): bool
    {
        return env('APP_STORAGE') == 's3';
    }

    /**
     * Obtiene la ruta absoluta de un documento dada su ruta relativa
     *
     * Tiene en cuenta el tipo de almacenamiento de la aplicación
     * Si se está utilziando Amazon S3, estas rutas son del tipo:
     *
     * s3://bucket-name/path-to-file
     *
     * @param string $path                      La ruta relativa
     *
     * @return string                           La ruta absoluta
     */
    public static function path(string $path): string
    {
        if (self::isS3()) {
            $bucketName = env('AWS_BUCKET');
            return "s3://{$bucketName}/{$path}";
        } else {
            return Storage::disk(env('APP_STORAGE'))->path($path);
        }
    }
}
