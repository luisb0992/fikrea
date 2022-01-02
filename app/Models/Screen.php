<?php

/**
 * Modelo de Screen
 *
 * Representa un archivo de video grabado
 * mediante la herramienta de grabación de escritorio
 *
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\File;

class Screen extends Model
{
    /**
     * Atributos del documento
     *
     * @var array
     */
    protected $fillable =
        [
            'user_id',          // Usuario que sube el archivo
            'filename',         // Nombre del archivo
            'type',             // Tipo del archivo
            'duration',         // Tipo del archivo
            'size',             // Tamaño del archivo
            'path',             // Carpeta padre de ubicación del archivo
            'token',            // Token único del archivo
        ];

    /**
     * Obtiene el usuario
     *
     * @return BelongsTo                        El usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene el archivo relacionado
     *
     * @return BelongsTo                        El archivo
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }

    /**
     * Convierte Screen a File
     *
     * Se crea un registro File para el archivo temporal, para que aparezca en el
     * sistema de archivos de fikrea
     *
     * @return bool         Si se ha convertido o no a File
     */
    public function toFile() : int
    {
        $data = [];

        try {
            $finalPath = config('files.folder') . '/' . $this->filename;
            Storage::disk(env('APP_STORAGE'))->put(
                $finalPath,
                Storage::disk(env('APP_STORAGE'))->get(
                    config('screen.folder.temp') . '/' . $this->filename
                )
            );
            $data['path'] = $finalPath;
            $data['md5'] = md5(
                Storage::disk(env('APP_STORAGE'))->get(
                    config('files.folder') . '/' . $this->filename
                )
            );
            $data['user_id'] = $this->user_id;
            $data['name'] = $this->filename;
            $data['size'] = $this->size;
            $data['type'] = $this->type;
            $data['token'] = Str::random(64);
            $data['parent_id'] = $this->path ? intval($this->path) : null;
           
            //$data['full_path'] = json_encode([value]);

            return File::create($data)->id;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
