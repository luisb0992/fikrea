<?php

namespace App\Models;

/**
 * Modelo de censo para un evento
 *
 * Gestiona las personas que participan en un evento
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventPaticipant extends Model
{
    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'event_paticipants';

    /**
     * Los atributos asociados al modelo.
     *
     * @var array
     */
    protected $fillable = [
        'event_id',             // El evento en cuestion
        'dni',                  // numero Identificacion
        'name',                 // nombre
        'lastname',             // apellido
        'email',                // email
        'phone',                // telefono
        'company',              // empresa o compañia perteneciente
        'position',             // cargo dentro de la compañia
        'address',              // direccion
        'user_image',           // La ruta del archivo d la imagen frontal
        'front_path',           // anverso del documento identificativo
        'back_path',            // reverso del documento identificativo
        'face_recognition',     // Si la imagen frontal coincide con el documento proporcionado
        'has_participated',     // Si el usuario completo su participacion
        'token',                // el token de acceso al evento
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
