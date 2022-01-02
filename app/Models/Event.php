<?php

/**
 * Modelo de gestion de eventos
 *
 * Gestiona los eventos que proporciona la app como encuestas, recoleccion de firmas, votaciones
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use App\Enums\Event\EventStatus;
use App\Enums\Event\EventType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Lang;

class Event extends Model
{
    /**
     * la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'events';

    /**
     * Los atributos asociados al modelo.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',                  // El usuario creador del evento
        'title',                    // El titulo para el evento
        'description',              // La descripcion (opcional) para el evento
        'start_date',               // Fecha de incio del evento (opcional)
        'end_date',                 // Fecha de cierre del evento (opcional)
        'purpose_event_id',         // Finalidad/categoria del evento
        'type',                     // Tipo de evento (EventType)
        'is_public',                // Evento publico o privado (solo son privados las votaciones)
        'is_anonymous',             // Si la votacion/encuesta/firma es anonima, si lo es no se mostrara la votacion que ha realizado el externo
        // En el caso de la votacion siempre es anonima, lo demas lo puede decidir el usuario
        'event_status',             // Estado del evento (EventStatus)
        'token',                    // Token de acceso
        'kiosk_mode',               // Si se activa el modo kiosko (permite al creador llenar manualmente el evento, responder encuestas, etc)
        'min_goal',                 // La meta minima
        'max_goal',                 // La meta deseada
        'is_block_return',          // Si se prohibe regresar a la pregunta anterior (solo para votaciones y encuestas)
    ];

    /**
     * El casting para atributos de tipo nativo.
     *
     * @var array
     */
    protected $casts = [
        'is_public'         => 'boolean',   // (false por defecto)
        'is_anonymous'      => 'boolean',   // (false por defecto)
        'kiosk_mode'        => 'boolean',   // (false por defecto)
        'is_block_return'   => 'boolean',   // (false por defecto)
        'start_date'        => 'datetime',
        'end_date'          => 'datetime',
    ];

    /**
     * Devuelve el usuario creador del evento
     *
     * @return BelongsTo|null           El usuario o null si no existe
     */
    public function user(): ?BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Devuelve la finalidad o categoria de un evento
     *
     * @return BelongsTo            La finalidad
     */
    public function purpose(): BelongsTo
    {
        return $this->belongsTo(PurposeEvent::class);
    }

    /**
     * Devuelve un video explicativo opcional del evento
     *
     * @return HasOne|null           El video o null si no posee
     */
    public function video(): ?HasOne
    {
        return $this->hasOne(EventVideo::class);
    }

    /**
     * Devuelve una imagen opcional del evento
     *
     * @return HasOne|null           La imagen o null si no posee
     */
    public function image(): ?HasOne
    {
        return $this->hasOne(EventImage::class);
    }

    /**
     * Obtiene las validaciones requeridas para el evento
     *
     * @return HasOne|null          Las validaciones requeridas
     */
    public function requiredValidation(): ?HasOne
    {
        return $this->hasOne(RequiredEventValidation::class);
    }

    /**
     * Las personas que intervienen en el evento
     *
     * @return HasMany          El censo de personas
     */
    public function participants(): HasMany
    {
        return $this->hasMany(EventPaticipant::class);
    }

    /**
     * Devuelve las preguntas pertenecientes en un evento
     *
     * @return HasMany          Las preguntas
     */
    public function questions(): HasMany
    {
        return $this->hasMany(EventQuestion::class);
    }

    /**
     * Devuelve las preguntas respondidas por el usuario en el resultado
     * de un tipo de evento
     *
     * @return BelongsToMany            las preguntas que han sido respondidas
     */
    public function questionsAnswered(): BelongsToMany
    {
        return $this->belongsToMany(EventQuestion::class, 'participant_event_answers', 'event_id', 'event_question_id');
    }

    /**
     * Guarda un archivo de tipo imagen o video para el evento
     *
     * @param [type] $isFile            Si es un archivo
     * @param [type] $file              El archivo (imagen o video)
     * @param [type] $url               La url (si es un archivo por url)
     * @param [type] $type              El tipo de archivo a guardar
     * @return void
     */
    public function saveEventFile($isFile, $file, $url, $type): void
    {
        if ($isFile) {
            $file = base64_encode(file_get_contents($file->getRealPath()));

            $type == 'image' ?
                $this->image()->create(['image' => $file]) :
                $this->video()->create(['video' => $file]);
        } else {

            $type == 'image' ?
                $this->image()->create(['url' => $url]) :
                $this->video()->create(['url' => $url]);
        }
    }

    /**
     * Verificar si un evento puede ser editable en todos sus procesos
     *
     * @return boolean          Un true o false
     */
    public function cannotBeEdited(): bool
    {
        return $this->event_status === EventStatus::ACTIVE_EVENT;
    }

    /**
     * Formato aceptado para mostrar la descripcion de un evento
     *
     * @return string|null              La descripcion del evento o null
     */
    public function getFormatDescriptionAttribute(): ?string
    {
        return $this->description ? $this->description : Lang::get('Sin descripción');
    }

    /**
     * Determinar si un evento es publico o privado segun el tipo de evento
     *
     * @param [type] $type          El tipo de evento
     * @return boolean              True si es publico o false caso contrario
     */
    public static function isPublic($type): bool
    {
        $isPublic = false;

        switch ($type) {
            case EventType::VOTE:
                $isPublic = false;
                break;
            case EventType::SURVEY:
                $isPublic = true;
                break;
            case EventType::SIGNATURE_COLLECTION:
                $isPublic = true;
                break;
            case EventType::SURVEY_AND_SIGNATURE_COLLECTION:
                $isPublic = true;
                break;
            default:
                break;
        }

        return $isPublic;
    }

    /**
     * Devuelve el listado de eventos de un usuario ordenado
     *
     * @param User $user            El usuario perteneciente
     * @return Builder|null         Una instanica del modelo base
     */
    public static function eventsByUser(User $user): ?Builder
    {
        return self::where('user_id', $user->id)->orderBy('created_at', 'desc');
    }

    /**
     * Obtiene los eventos cn el estado modo borrador
     *
     * @param User $user            El usuario perteneciente
     * @return Builder|null         Una instanica del modelo base
     */
    public static function draftEventsByUser(User $user): ?Builder
    {
        return self::eventsByUser($user)->where('event_status', EventStatus::DRAFT_EVENT);
    }
}
