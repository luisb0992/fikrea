<?php

/**
 * Modelo de las respuestas de los participantes del evento
 *
 * Gestiona las respuestas de los participantes de un evento especifico
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParticipantEventAnswer extends Model
{
    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'participant_event_answers';

    /**
     * Los atributos asociados al modelo.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',                 // El evento
        'event_paticipants_id',     // El participante
        'event_question_id',        // La pregunta
        'event_answers_id',         // La respuesta o las respuestas
        'answered_with_a_comment',  // La respuesta a base de un comentario
    ];

    /**
     * Devuelve el evento perteneciente
     *
     * @return BelongsTo            El evento relacioando
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Devuelve el participante del evento
     *
     * @return BelongsTo            El evento relacioando
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(EventPaticipant::class);
    }

    /**
     * Devuelve la pregunta de un evento
     *
     * @return BelongsTo            La pregunta relacioanda
     */
    public function quetion(): BelongsTo
    {
        return $this->belongsTo(EventQuestion::class);
    }

    /**
     * Devuelve la respuesta de una pregunta
     *
     * @return BelongsTo            La respuesta relacioanda
     */
    public function answer(): BelongsTo
    {
        return $this->belongsTo(EventAnswer::class);
    }
}
