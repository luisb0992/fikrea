<?php

/**
 * Modelo de Notificación
 *
 * Representa una notificación recibida por el usuario
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\User;
use App\Enums\NotificationTypeEnum;

class Notification extends Model
{
    /**
     * Los atributos completables
     *
     * @var array
     */
    protected $fillable =
    [
        'user_id',                          // El usuario
        'reason_cancel_request_id',         // la razon de cancelar la solicitud
        'title',                            // El título
        'message',                          // El mensaje o contenido de la notificación
        'url',                              // La url
        'created_at',                       // La fecha de creación de la notificación
        'read_at',                          // La fecha en la que ha sido leída o null
        // si está pendiente de ser leída
        'type',                             // Tipo de notificación, para diferenciarlas a la hora de mostrarse
    ];

    /**
     * Conversiones de tipo
     *
     * @var array
     */
    protected $casts =
    [
        'created_at'    => 'datetime',
        'read_at'       => 'datetime',
    ];

    /**
     * No hay marcas de tiempo
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * El usuario que recibe la notificación
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con la razon para cancelar un proceso
     *
     * @return BelongsTo              La razon de la cancelacion
     */
    public function reasonCacel() : BelongsTo
    {
        return $this->belongsTo(ReasonCancelRequest::class, 'reason_cancel_request_id');
    }

    /**
     * Marca una notificación como leída
     *
     * @return bool                             true si la notificación se marca leída con éxito
     *                                          false en caso contrario
     */
    public function read(): bool
    {
        $this->read_at = new \DateTime;
        return  $this->save();
    }

    /**
     * Devuelve el estilo con que se mostrara el header de la notificación
     * según el typo definido en la misma
     *
     * @return string                           clase con que se representa
     */
    public function getViewStyleAttribute(): string
    {
        switch ($this->type) {
            case NotificationTypeEnum::SUCCESSFULLY:
                return 'bg-success text-white';
                break;
            case NotificationTypeEnum::CANCELLED:
                return 'bg-danger text-white';
                break;
            case NotificationTypeEnum::ATTENTION:
                return 'bg-warning text-white';
                break;
            
            default:
                return 'bg-light text-secondary';
                break;
        }
    }
}
