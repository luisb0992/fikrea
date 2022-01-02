<?php

/**
 * Modelo de imagen de eventos
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventImage extends Model
{
    /**
     * La tabla asignada al modelo.
     *
     * @var string
     */
    protected $table = 'event_images';

    /**
     * Los atributos asociados al modelo.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',             // El evento pertneciente
        'url',                  // Url de la imagen (opcional)
        'image',                // el blob de la imagen
    ];

    /**
     * Devuelve el evento perteneciente
     *
     * @return BelongsTo|null           El evento o null si no posee
     */
    public function event(): ?BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
