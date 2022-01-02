<?php

/**
 * Modelo de Sello
 *
 * Es un sello que dispone el usuario y que puede estampar en un documento
 *
 * Los sellos que puede usar cualquier usuario, es decir, de uso público
 * son aquellos no asociados a usuario alguno (user_id es null),
 * sólo poseen un nombre y una ruta donde se encuentra el archivo de imagen del sello
 *
 * Para los sellos subidos por el usuario, no poseen ruta y se guardan en la base de datos
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Stamp extends Model
{
    /**
     * Lista de atributos del modelo
     *
     * @var array
     */
    protected $fillable =
        [
            'user_id',                  // El id del usuario asociado al sello
                                        // o null si es un sello no asociado a un usuario concreto
                                        // sino que es un sello de la librería de sellos predeterminada
            'name',                     // El nombre del sello
            'path',                     // Para un sello de la librería de sellos predeterminada
                                        // es el nombre del archivo de imagen del sello (en formato PNG)
                                        // Es null cuando es un sello asociado a un usuario
            'stamp',                    // La imagen del sello
            'thumb',                    // La imagen del sello en miniatura
            'width',                    // Ancho de la imagen del sello en miniatura
            'height',                   // Altura de la imagen del sello en miniatura
            'type',                     // El tipo mime de la imagen
            'created_at',               // La fecha de creación
        ];

    /**
     * Conversiones de tipo
     *
     * @var array
     */
    protected $casts =
        [
            'created_at'    => 'datetime',
        ];

    /**
     * No utilizar marcas de tiempo
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Retorna el tipo MIME del documento
     *
     * @return BelongsTo
     */
    public function mimeType(): BelongsTo
    {
        return $this->belongsTo(MediaType::class, 'media_type', 'type');
    }

    /**
     * Obtiene el usuario asociado al sello
     *
     * @return BelongsTo                        El usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene los sellos de la librería predeterminada que se proporciona a los usuarios
     *
     * Estos sellos no están asociados a ningún usuario en concreto, sino que son públicos
     * para que pueda hacer uso cualquier usuario de ellos
     *
     * @param string $language                  El código ISO-639-1 del idioma
     *
     * @return Collection                       Una coleción de sellos
     */
    public static function library(string $lang): Collection
    {
        // Obtiene los sellos públicos, que non están asociados a ningún usuario en concreto
        $stamps = self::whereNull('user_id')->where('lang', $lang)->get();

        // Obtiene la carpeta que contiene las imáganes de los sellos en formato PNG
        $stampsFolder = config('stamps.folder');

        // Obtiene el path de cada sello, indicando la carpeta en la que se encuentra el archivo
        $stamps->each(fn ($stamp) => $stamp->path =  "{$stampsFolder}/{$stamp->path}");

        return $stamps;
    }
}
