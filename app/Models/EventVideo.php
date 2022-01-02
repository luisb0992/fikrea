<?php

/**
 * Modelo de video de eventos
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventVideo extends Model
{
    /**
     * La tabla asignada al modelo.
     *
     * @var string
     */
    protected $table = 'event_videos';

    /**
     * Los atributos asociados al modelo.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',             // El evento pertneciente
        'url',                  // Url del video (opcional)
        'video',                // Blob del video
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
