<?php

/**
 * Modelo de ProcessFeedback
 *
 * Gestiona los comentarios realizados en cada proceso de valdiacion
 * por el usuario "firmante"
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessFeedback extends Model
{
    /**
     * Tabla asociada al modelo
     *
     * @var string
     */
    protected $table = 'process_feedback';

    /**
     * Campos de la tabla
     *
     * @var array
     */
    protected $fillable = [
        'comment',                  // El comentario
        'commentable_id',           // El id del modelo padre
        'commentable_type',         // El model padre
        'validation_type',          // El tipo de valdiacion o null si no existe
    ];

    /**
     * El modelo padre al cual pertenece => [ Audio, Video, Passport, VerificationForm,
     *                                      Formdata, DocumentRequest, Sign, Capture, Textbox ]
     *
     */
    public function commentable()
    {
        return $this->morphTo();
    }
}
