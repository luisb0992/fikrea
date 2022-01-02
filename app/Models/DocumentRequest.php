<?php

/**
 * Modelo de Solicitud de Documentos
 *
 * Una solicitud de documentos consiste en una serie de uno o más documentos que son solicitados a uno o más
 * usuarios. Por ejemplo, se puede enviar una solicitud a varios usuarios para que suban su carné de conducir y
 * el permiso de circulación del vehículo
 *
 * Por tanto una solicitud de documentos debe poseer:
 *
 * 1. Una lista de usuarios que se almacenan en la tabla de firmantes o "signers".
 *    En este caso el concepto de usuario firmante se refiere a aquel al cual se le solicita la documentación.
 * 2. Una lista de documentos requeridos.
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class DocumentRequest extends Model
{
    /**
     * Lista de atributos del modelo de Solictud de Documentos
     *
     * @var array
     */
    protected $fillable =
    [
        'user_id',                          // El id del usuario autor o propietario de la solicitud
        'name',                             // El nombre de la solitud de documentos
        'comment',                          // Un comentario para la solicitud de documentos
        'created_at',                       // La fecha de creación de la solicitud
        'workspace_statu_id',               // El status de la solicitud
        'reason_cancel_request_id',         // Motivo de rachazo al documento solicitado
    ];

    /**
     * No hay marcas de tiempo
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Conversiones de tipos
     *
     * @var array
     */
    protected $casts =
        [
            'created_at'    =>  'datetime',
        ];

    /**
     * Obtiene el grado de progreso, en tanto por ciento, de una solicitud de documentos
     *
     * @return int                              El grado de progreso de la solicitud
     */
    public function getProgressAttribute(): int
    {
        // Obtiene el número de usuario firmantes que reciben la solicitud de documentos
        $signers = $this->signers->count();
        
        // Obtiene el número de participantes que ha realizado la solicitud de documentos
        $signersHaveDoneRequest = $this->signers
                                       ->filter(fn ($signer) => $signer->requestIsDone())
                                       ->count();

        return intval($signersHaveDoneRequest * 100 / $signers);
    }

    /**
     * Devuelve si se ha completado la solicitud de documentos
     *
     * @return bool                    Si se ha completado o no
     */
    public function done(): bool
    {
        return $this->progress === 100;
    }
    
    /**
     * El usuario que crea la solicitud de documentos
     *
     * @return BelongsTo                        El usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * La lista de usuarios a los que se le solicita la documentación
     *
     * @return HasMany                          Una lista de usuarios "firmantes" a los que se le solicita
     *                                          la documentación requerida
     */
    public function signers(): HasMany
    {
        return $this->HasMany(Signer::class);
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
     * La lista de documentos requeridos
     *
     * @return HasMany                          Los documentos requeridos en la solicitud
     */
    public function documents(): HasMany
    {
        return $this->hasMany(RequiredDocument::class);
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
     * La lista de documentos requeridos que están expirando
     *
     * @return Collection                          Los documentos requeridos en la solicitud que expiran pronto
     */
    public function expiringDocuments()
    {
        /**
         * Para chequear si la Solicitud tiene Documentos cerca de expirar
         * debemos:
         * Para cada Documento Requerido de la solicitud
         * verificar que su último documento aportado no esté próximo a vencer
         */
        return $this->documents->filter(
            fn($document) => $document->files->count()>0 && $document->files->last()->isNearToExpire()
        );
    }

    /**
     * La lista de archivos adjuntados a la solicitud de documentos
     *
     * @return HasMany                          Los archivos aportados respondiendo a la solicitud
     */
    public function files(): HasMany
    {
        return $this->hasMany(DocumentRequestFile::class);
    }

    /**
     * Obtiene los envíos o comparticiones de la solicitud de documentos
     *
     * Una solicitud de documentos se comparte una vez cuando se crea,
     * pero puede ser compartido en sucesivas ocasiones con los usuarios
     * que no han efectuado todavía el proceso
     *
     * @return HasMany
     */
    public function sharings(): HasMany
    {
        return $this->hasMany(DocumentRequestSharing::class)->orderBy('sent_at', 'desc');
    }

    /**
     * Obtiene las solicitudes de documentos enviadas por un usuario
     * que tienen usuarios "firmantes" asignados, que son los que deben atender a la solicitud
     *
     * @param User $user                        El usuario
     *
     * @return Builder                          Una lista de solicitudes de documentos
     *                                          con usuarios asignados
     */
    public static function findByUser(User $user): Builder
    {
        return self::where('user_id', $user->id)
            ->has('signers')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Obtiene las visitas que han realizado los firmantes
     *
     * @return HasManyThrough                   Las visitas a la solicitud de deocumentos
     */
    public function visits(): HasManyThrough
    {
        return $this->hasManyThrough(SignerVisit::class, Signer::class, 'document_request_id', 'signer_id');
    }

    /**
     * Si la solicitud de documentos ya ha sido compartido a fecha actual o no
     *
     * @return bool                             true si la solicitud ya se ha enviado, ha sido compartido hoy mismo
     *                                          con los firmantes o false en caso contrario
     */
    public function sharingHasBeenSentToday(): bool
    {
        return $this->sharings->contains(
            fn ($sharing) => (new \DateTime($sharing->sent_at))->format('d-m-Y') == (new \DateTime)->format('d-m-Y')
        );
    }
    /**
     * Obtiene el status al cual sera asignado a una solicitud
     *
     * @return BelongsTo                        El status
     */
    public function workspaceStatu(): BelongsTo
    {
        return $this->belongsTo(WorkspaceStatu::class);
    }

    /**
     * Obtiene el motivo al cual se rechaza una solicitud
     *
     * @return BelongsTo                        El motivo
     */
    public function reasonCancelRequests(): BelongsTo
    {
        return $this->belongsTo(ReasonCancelRequest::class);
    }

    /**
     * Comentarios adjuntados a la solicitud de documentos
     *
     * @return HasMany                          Los comentarios a la solicitud
     */
    public function documentRequestComments(): HasMany
    {
        return $this->hasMany(WorkspaceComment::class);
    }

    /**
     * Actualiza la actividad sobre la solicitud de documentos
     * eliminando el signer que está aportando documentos
     *
     * @param Signer $signer El firmante que atiende la solicitud
     *
     * @return void
     */
    public function removeActivity(Signer $signer) : void
    {
        $this->process->removeRequestActivity($signer);
    }

    /**
     * Actualiza la actividad sobre la solicitud de documentos
     * adicionando el signer que está aportando documentos
     *
     * @param Signer $signer El firmante que atiende x validación
     *
     * @return void
     */
    public function addActivity(Signer $signer) : void
    {
        $this->process->addRequestActivity($signer);
    }

    /**
     * Retorna la cantidad de firmantes aportando la solicitud
     *
     * @return int
     */
    public function getActivityCountAttribute() : int
    {
        // Otengo la actividad actual de la solicitud de documento, vacío si nulo
        $activity = json_decode($this->activity, true) ?? [];
        return count($activity);
    }

    /**
     * Si la solicitud está marcado como activo
     * si algún firmante está atendiendo la solicitud en este momento
     *
     * @return bool                 Si se está atendiendo por algún firmante
     */
    public function isActive(): bool
    {
        return $this->signers->contains(
            fn($signer) => $signer->process and $signer->process->active === 1
        );
    }

    /**
     * Retorna la actividad sobre la solicitud de documento
     *
     * @return string
     */
    public function getActivity() : string
    {
        return $this->process->getRequestActivity();
    }

    /**
     * Retorna si un firmante está atendiendo la solicitud
     *
     * @param Signer $signer El firmante que se quiere verificar
     *
     * @return bool             Si está atendiendo la solicitud o no
     */
    public function signerIsActive(Signer $signer) : bool
    {
        if (!$this->process) {
            $this->process = $this->process()->create([]);
        }

        // Otengo la actividad actual de la solicitud de documento, vacío si nulo
        $activity = json_decode($this->process->activity, true) ?? [];
        return array_filter(
            $activity,
            fn ($item) => $item['signer'] == $signer->id
        )? true:false;
    }

    /**
     * Devuelve el comentario del proceso
     *
     * @return string|null          El comentario o null si no existe
     */
    public function getIfCommentExists(): ?string
    {
        return $this->feedback ? $this->feedback->comment : null;
    }

    /**
     * Guarda el comentario
     *
     * @param string $comment                   El comentario a guardar
     * @return void
     */
    public function saveComment(string $comment) : void
    {
        if (!$this->feedback) {
            $this->feedback()->create(['comment' => $comment]);
        }
    }
}
