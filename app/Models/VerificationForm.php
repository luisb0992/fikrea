<?php

/**
 * Modelo de certificacion de datos
 *
 * Gestiona la verificacion o certificacion de datos de formularios
 * de datos como proceso independiente de un documento
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Lang;

class VerificationForm extends Model
{
    /**
     * Nombre de la tabla a usar
     *
     * @var string
     */
    protected $table = 'verification_forms';

    /**
     * Atributos de la tabla verification_forms
     *
     * @var array
     */
    protected $fillable = [
        'user_id',                  // usuario propietario del formulario
        'name',                     // Nombre para el formulario
        'comment',                  // comentario opcional del formulario
        'status'                    // estado del formulario
    ];

    /**
     * Delver el usuario propietario del formulario
     * o null si no tiene asignado un usuario
     *
     * @return BelongsTo|null           Una relacion
     */
    public function user(): ?BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Devolver todos los inputs o fila de inputs pertenecientes al formulario
     *
     * @return HasMany                  Los inputs
     */
    public function fieldsRow(): HasMany
    {
        return $this->hasMany(FormData::class);
    }

    /**
     * La lista de usuarios a los que se le solicita la verificación
     *
     * @return HasMany                          Una lista de usuarios "firmantes"
     */
    public function signers(): HasMany
    {
        return $this->HasMany(Signer::class);
    }

    /**
     * Obtiene los envíos o comparticiones de la verificación de datos
     *
     * Una verificación de datos se comparte una vez cuando se crea,
     * pero puede ser compartido en sucesivas ocasiones con los usuarios
     * que no han efectuado todavía el proceso
     *
     * @return HasMany
     */
    public function sharings(): HasMany
    {
        return $this->hasMany(VerificationFormSharing::class)->orderBy('sent_at', 'desc');
    }

    /**
     * Obtiene las visitas que han realizado los firmantes
     *
     * @return HasManyThrough                   Las visitas de la verificación de datos
     */
    public function visits(): HasManyThrough
    {
        return $this->hasManyThrough(SignerVisit::class, Signer::class, 'verification_form_id', 'signer_id');
    }

    /**
     * Obtiene el proceso perteneciente
     *
     * @return MorphOne                     El proceso relacionado
     */
    public function process(): MorphOne
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
     * Devuelve todas las verificaciónes de datos (formularios) del usuario indicado
     * ordenada por fecha de creacion mayor
     *
     * @param User $user                el usuario perteneciente
     * @return Builder|null             la data con la informacion o null si no posee alguna
     */
    public static function verificationByUser(User $user): ?Builder
    {
        return self::where('user_id', $user->id)->has('signers')->orderBy('created_at', 'desc');
    }

    /**
     * Si la verificación está marcado como activo
     * si algún firmante está atendiendo la verificación en ese instante
     *
     * @return bool                 True si se esta atendiendo
     */
    public function isActive(): bool
    {
        return $this->signers->contains(fn ($signer) => $signer->process and $signer->process->active === 1);
    }

    /**
     * Retorna la actividad sobre la verificación de datos
     *
     * @return string
     */
    public function getActivity(): string
    {
        return $this->process->getVerificationFormActivity();
    }

    /**
     * Obtiene el grado de progreso, en tanto por ciento, de una verificación de datos
     *
     * @return int                              El grado de progreso de la solicitud
     */
    public function getProgressAttribute(): int
    {
        // Obtiene el número de usuario firmantes que reciben la verificación de datos
        $signers = $this->signers->count();

        // Obtiene el número de participantes que ha realizado la verificación de datos
        $signersHaveDoneVerification = $this->signers->filter(
            fn ($signer) => $signer->verificationFormIsDone()
        )->count();

        // usuarios que han realizado * 100 / el total de usuarios
        return intval($signersHaveDoneVerification * 100 / $signers);
    }

    /**
     * Retorna si un firmante está atendiendo la verificación
     *
     * @param Signer $signer            el usuario perteneciente
     * @return bool                     true si esta atendiendo la verificación
     */
    public function signerIsActive(Signer $signer): bool
    {
        if (!$this->process) {
            $this->process()->create([]);
        }

        if (!$this->process) {
            return false;
        }

        // Otengo la actividad actual de la verificación de datos
        // o un array vacio caso contrario
        $activity = json_decode($this->process->activity, true) ?? [];
        return array_filter($activity, fn ($item) => $item['signer'] == $signer->id) ? true : false;
    }

    /**
     * Obtiene el nombre de la verificación
     * en caso contrario se formatea por otro
     *
     * @return string
     */
    public function getFormatNameAttribute(): string
    {
        return $this->name ?? Lang::get('Verificación de datos');
    }

    /**
     * Obtiene el comentario de la verificación o
     * en caso contrario formateapor otro
     *
     * @return string
     */
    public function getFormatCommentAttribute(): string
    {
        return $this->comment ?? Lang::get('Sin comentarios');
    }

    /**
     * Si la verificación de datos ya ha sido compartido a la  fecha actual o no
     *
     * @return bool                     true si la verificación ya se ha enviado o false en caso contrario
     */
    public function sharingHasBeenSentToday(): bool
    {
        return $this->sharings->contains(
            fn ($sharing) => (new \DateTime($sharing->sent_at))->format('d-m-Y') == (new \DateTime)->format('d-m-Y')
        );
    }

    /**
     * Devuelve si se ha completado la verificación de datos
     *
     * @return bool                    Si se ha completado o no
     */
    public function isDone(): bool
    {
        return $this->progress === 100;
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

    /**
     * Devuelve el usuario firmante no creador de la verificacion
     *
     * @return string|null          El nombre del usuario "firmante" o null si no xiste
     */
    public function noCreatorSigner() : ?string
    {
        $signer = $this->signers->filter(fn ($signer) => !$signer->creator)->first();

        return $signer ? $signer->fullname : null;
    }
}
