<?php

/**
 * Modelo de platilla para eventos
 *
 * Gestiona las plantillas proporcionadas por la app o personales del usuario
 * para la gestion de eventos
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventTemplate extends Model
{
    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'event_templates';

    /**
     * Los atributos asociados al modelo.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',                  // Si pertenece a un usuario, sino es un platilla de la app
        'template_title',           // Titulo para la plantilla (opcional)
        'type'                      // el tipo de platilla dedicada a un evento
    ];

    /**
     * Devuelve el usuario perteneciente
     *
     * @return BelongsTo|null           El usuario o null si no posee
     */
    public function user(): ?BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
