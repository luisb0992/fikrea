<?php

/**
 * Modelo de preguntas del evento
 *
 * Gestiona las preguntas pertenecientes a un evento
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventQuestion extends Model
{
    /**
     * la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'event_questions';

    /**
     * Los atributos asociados al modelo.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',                 // El evento perteneciente
        'event_template_id',        // La llave si pertenece a una platilla de la app o del usuario
        'description',              // La pregunta
        'is_miltiple',              // Si es una pregunta de seleccion multiple
        'response_limit',           // Si es una seleccion multiple, cuantas opciones puede marcar el usuario
        'is_left_empty',            // Si se puede dejar la pregunta sin responder y pasar a la siguiente
        'answered_with_a_comment',  // Si la pregunta no tiene respuestas asignada se toma como una pregunta a base de un comentario
        'is_active',                // Si la pregunta esta disponible
    ];

    /**
     * Devuelve el evento perteneciente
     *
     * @return BelongsTo|null           El evento o null si no posee
     */
    public function event() : ?BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Devuelve la platilla perteneciente
     *
     * @return BelongsTo|null           la plantilla o null si no posee
     */
    public function template() : ?BelongsTo
    {
        return $this->belongsTo(EventTemplate::class);
    }

    /**
     * Devuelve las respuestas de la pregunta realizada
     *
     * @return HasMany              Las respuestas
     */
    public function answers() : HasMany
    {
        return $this->hasMany(EventAnswer::class);
    }

    /**
     * Devuelve las preguntas activas
     *
     * @return self             Las preguntas
     */
    public static function isActive() : self
    {
        return self::where('is_active', true)->get();
    }
}
