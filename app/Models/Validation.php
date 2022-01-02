<?php

/**
 * Modelo de Validación
 *
 * Las validaciones son cada una de las acciones que debe realizar un usuario para aprobar un documento
 * Una validación puede ser su firma digital manuscrita, grabar un audio confirmándolo, etc
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

use Illuminate\Database\Eloquent\Model;

/**
 * Enumeraciones requeridas
 */
use App\Enums\WorkspaceStatu as WorkspaceStatus;    // Enum WorkspaceStatu

class Validation extends Model
{
    /**
     * Lista de atributos completables
     *
     * @var array
     */
    protected $fillable =
    [
        'document',             // El documento
        'user',                 // El usuario
        'validation',           // El tipo de validación a realizar
        'validated',            // Si la validación ha sido realizada o no
        'validated_at',         // La fecha de la validación
    ];

    /**
     * Conversiónd de tipos
     *
     * @var array
     */
    protected $casts =
    [
        'validated'     => 'boolean',
        'validated_at'  => 'datetime',
    ];

    /**
     * No hay marcas de tiempo
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * El docunmento sobre el que realiza la validación
     *
     * @return BelongsTo                        Un documento
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * El firmante que realiza la validación sobre el documento
     *
     * @return BelongsTo                        Cada validación a realizar la realiza un firmante
     */
    public function signer(): BelongsTo
    {
        return $this->belongsTo(Signer::class, 'user');
    }

    /**
     * Marca la validación como efectuada
     *
     * @return self                             La validación
     */
    public function validated(): self
    {
        $this->validated_at = now();
        $this->validated    = true;
        $this->save();

        // Actualizo el estado del proceso correspondiente
        $this->process->done();

        // La marcamos como Inactiva
        $this->markAsInactive();

        return $this;
    }

    /**
     * Obtiene el proceso perteneciente
     *
     * @return MorphOne                     El proceso relacionado
     */
    public function process() : MorphOne
    {
        return $this->morphOne(Process::class, 'statable');
    }

    /**
     * Obtiene el comentario realizado
     *
     * @return MorphOne         Comentario agregado
     */
    public function feedback() : MorphOne
    {
        return $this->morphOne(ProcessFeedback::class, 'commentable');
    }

    /**
     * Devuelve si la validación ha sido realizada o no
     * false cuando se ha cancelado o pendiente
     *
     * @return bool             Si se ha realizado
     */
    public function done()
    {
        // Si no existe se crea
        if (!$this->process) {
            $this->process = $this->process()->create([]);
        }

        return $this->process->isDone();
    }

    /**
     * Devuelve si la validación ha sido cancelada o no
     * false cuando se ha realizado o pendiente
     *
     * @return bool             Si se ha cancelado
     */
    public function canceled()
    {
        // Si no existe se crea
        if (!$this->process) {
            $this->process()->create([]);
        }

        return $this->process->isCanceled();
    }

    /**
     * Devuelve si la validación está pendiente o no
     * false cuando se ha realizado o cancelado
     *
     * @return bool             Si está pendiente
     */
    public function pending()
    {
        // Si no existe se crea
        if (!$this->process) {
            $this->process()->create([]);
        }

        return $this->process->isPending();
    }

    /**
     * Establece la validación como que se está atendiendo
     * por el firmante
     *
     * @return void
     */
    public function markAsActive() : void
    {
        // Si no existe se crea
        if (!$this->process) {
            $this->process()->create([]);
        }
        
        // Marcamos el proceso de validación como activo
        $this->process->markAsActive();

        // Actualizamos la actividad en el documento
        $this->document->addActivity($this->signer, $this);
    }

    /**
     * Guarda el comentario para un tipo de validacion especifica
     *
     * @param string $comment                   El comentario a guardar
     * @return void
     */
    public function saveComment(string $comment) : void
    {
        $this->feedback()->create(['comment' => $comment]);
    }

    /**
     * Establece la validación como que NO se está atendiendo
     * por el firmante
     *
     * @return void
     */
    public function markAsInactive() : void
    {
        // Si no existe se crea
        if (!$this->process) {
            $this->process()->create([]);
        }

        // Marcamos el proceso de validación como inactivo
        $this->process->markAsInactive();

        // Actualizamos la actividad en el documento
        $this->document->removeActivity($this->signer, $this);
    }

    /**
     * Devuelve el icono que representa el estado de la validación
     * con su color correspondiente
     * Naranja - Pendiente
     * Rojo    - Cancelado
     * Verde   - Realizado
     *
     * @return string           El color
     */
    public function getIconStatus() : string
    {
        return !$this->process? 'fa-square text-warning' : (
            $this->process->workspace_statu_id == WorkspaceStatus::PENDIENTE?
            'fa-square text-warning' : ($this->process->workspace_statu_id == WorkspaceStatus::CANCELADO?
                'fa-window-close text-danger' : 'fa-check-square text-success')
        );
    }
}
