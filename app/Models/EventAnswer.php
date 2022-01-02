<?php

/**
 * Modelo de respuestas a la pregunta del evento
 *
 * Gestiona las respuestas pertenecientes a una pregunta del  evento
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAnswer extends Model
{
    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'event_answers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'event_question_id',            // La pregunta asociada
        'description',                  // la descripcion de la respuesta
        'is_active'                     // Si la respuesta esta disponible
    ];

    /**
     * Devuelve la pregunta a la que perteneciente
     *
     * @return BelongsTo|null           La pregunta perteneciente o null si no posee
     */
    public function question() : ?BelongsTo
    {
        return $this->belongsTo(EventQuestion::class);
    }

    /**
     * Devuelve las respuestas activas
     *
     * @return self             las respuestas
     */
    public static function isActive() : self
    {
        return self::where('is_active', true)->get();
    }
}
