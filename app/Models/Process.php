<?php

/**
 * Modelo de Process
 *
 * Representa un proceso
 * que puede ser una validación, una solicitud de documentos, etc
 *
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Lang;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Models\Signer;

class Process extends Model
{
    /**
     * El nombre de la tabla que almacena las grabaciones de audio
     *
     * @var string
     */
    protected $table = 'process';

    /**
     * Si se utilizan los campos created_at y updated_at en la tabla
     *
     * @var string
     */
    public $timestamps = false;

    /**
     * Atributos del documento
     *
     * @var array
     */
    protected $fillable =
        [
            'reason_cancel_request_id',     // Razón por la que se cancela el proceso
            'activity',                     // Actividad sobre el proceso JSON
            'active',                       // Si está activo o no
            'workspace_statu_id',           // El estado
        ];
    
    /**
     * Devuelve el modelo padre 'statable' (Validation, Signer)
     */
    public function statable()
    {
        return $this->morphTo();
    }

    /**
     * Relación con el estado del workspace o estado del proceso
     *
     * @return BelongsTo              El estado del proceso
     */
    public function workspaceStatus() : BelongsTo
    {
        return $this->belongsTo(WorkspaceStatu::class, 'workspace_statu_id');
    }

    /**
     * Marca el proceso como realizado
     *
     * @return self                             El proceso
     */
    public function done(): self
    {
        // Establezco el estado como realizado
        $this->workspace_statu_id = \App\Enums\WorkspaceStatu::REALIZADO;
        $this->save();

        return $this;
    }

    /**
     * Marca el proceso como cancelado
     *
     * @return self                             El proceso
     */
    public function cancel(): self
    {
        // Establezco el estado como realizado
        $this->workspace_statu_id = \App\Enums\WorkspaceStatu::CANCELADO;
        $this->save();

        return $this;
    }

    /**
     * Establece el proceso como que se está atendiendo
     * por el firmante
     *
     * @return void
     */
    public function markAsActive() : void
    {
        // Marcamos el proceso como activo
        $this->active = 1;
        $this->save();
    }

    /**
     * Actualiza la actividad el proceso de un documento
     * adicionando signer y validación que está realizando
     *
     * @param Signer     $signer     El firmante que atiende x validación
     * @param Validation $validation La validación que comenzó a atender
     *
     * @return void
     */
    public function addDocumentActivity(Signer $signer, Validation $validation) : void
    {
        // Otengo la actividad actual del documento, vacío si nulo
        $activity = json_decode($this->activity, true) ?? [];

        // Se adiciona firmante => validación
        $newActivity = [
            'signer'     => $signer->id,
            'validation' => $validation->id
        ];

        if ($activity == []) {
            // Si está vacío adiciono
            array_push($activity, $newActivity);
        } else {
            // Verifico que no se haya adicionado
            if (!array_filter(
                $activity,
                fn ($item) => $item['signer'] == $signer->id && $item['validation'] == $validation->id
            )) {
                array_push($activity, $newActivity);
            }
        }

        $this->activity = json_encode($activity);
        $this->save();
    }

    /**
     * Establece el proceso como que NO se está atendiendo
     * por el firmante
     *
     * @return void
     */
    public function markAsInactive() : void
    {
        $this->active = 0;
        $this->save();
    }

    /**
     * Actualiza la actividad del proceso sobre un documento
     * eliminando signer y validación que estába realizando
     *
     * @param Signer     $signer     El firmante que atiende x validación
     * @param Validation $validation La validación que comenzó a atender
     *
     * @return void
     */
    public function removeDocumentActivity(Signer $signer, Validation $validation) : void
    {
        // Otengo la actividad actual del documento, vacío si nulo
        $activity = json_decode($this->activity, true) ?? [];

        // elimino la actividad del firmante
        $this->activity = json_encode(
            array_filter(
                $activity,
                fn ($item) => $item['signer'] != $signer->id && $item['validation'] != $validation->id
            )
        );
        $this->save();
    }

    /**
     * Retorna la actividad sobre un proceso
     *
     * @return string
     */
    public function getDocumentActivity() : string
    {
        // Otengo la actividad actual del documento, vacío si nulo
        $activity = json_decode($this->activity, true) ?? [];
        $salida = '';

        foreach ($activity as $act) {
            $signer = Signer::findOrFail($act['signer']);
            $validation = Validation::findOrFail($act['validation']);

            $salida .= "<b>$signer->name $signer->lastname</b> "
                . Lang::get('se encuentra ahora mismo revisando la validación de ')
                . (string) \App\Enums\ValidationType::fromValue($validation->validation)
                ."<br/>";
        }

        return $salida;
    }

    /**
     * Devuelve si el proceso ha sido realizada o no
     * false cuando se ha cancelado o pendiente
     *
     * @return bool             Si se ha realizado
     */
    public function isDone()
    {
        return $this->workspace_statu_id === \App\Enums\WorkspaceStatu::REALIZADO;
    }

    /**
     * Devuelve si el proceso ha sido cancelada o no
     * false cuando se ha realizado o pendiente
     *
     * @return bool             Si se ha cancelado
     */
    public function isCanceled()
    {
        return $this->workspace_statu_id === \App\Enums\WorkspaceStatu::CANCELADO;
    }

    /**
     * Devuelve si el proceso está pendiente o no
     * false cuando se ha realizado o cancelado
     *
     * @return bool             Si está pendiente
     */
    public function isPending()
    {
        return $this->workspace_statu_id === \App\Enums\WorkspaceStatu::PENDIENTE;
    }

    /**
     * Actualiza la actividad sobre la solicitud de documentos
     * eliminando el signer que está aportando documentos
     *
     * @param Signer $signer El firmante que atiende la solicitud
     *
     * @return void
     */
    public function removeRequestActivity(Signer $signer) : void
    {
        // Otengo la actividad actual de la solicitud de documento, vacío si nulo
        $activity = json_decode($this->activity, true) ?? [];

        // elimino la actividad del firmante
        $this->activity = json_encode(
            array_filter(
                $activity,
                fn ($item) => $item['signer'] != $signer->id
            )
        );
        $this->save();
    }

    /**
     * Actualiza la actividad sobre la solicitud de documentos
     * adicionando el signer que está aportando documentos
     *
     * @param Signer $signer El firmante que atiende la solicitud
     *
     * @return void
     */
    public function addRequestActivity(Signer $signer) : void
    {
        // Otengo la actividad actual de la solicitud de documento, vacío si nulo
        $activity = json_decode($this->activity, true) ?? [];

        // Se adiciona firmante => validación
        $newActivity = [
            'signer'     => $signer->id
        ];

        if ($activity == []) {
            // Si está vacío adiciono
            array_push($activity, $newActivity);
        } else {
            // Verifico que no se haya adicionado
            if (!array_filter(
                $activity,
                fn ($item) => $item['signer'] == $signer->id
            )) {
                array_push($activity, $newActivity);
            }
        }

        $this->activity = json_encode($activity);
        $this->save();
    }

    /**
     * Retorna la actividad sobre la solicitud de documento
     *
     * @return string
     */
    public function getRequestActivity() : string
    {
        // Otengo la actividad actual de la solicitud de documento, vacío si nulo
        $activity = json_decode($this->activity, true) ?? [];
        $salida = '';

        foreach ($activity as $act) {
            $signer = Signer::findOrFail($act['signer']);

            $salida .= "<b>$signer->name $signer->lastname</b> "
                . Lang::get('se encuentra ahora mismo revisando la solicitud de documentos')
                ."<br/>";
        }

        return $salida;
    }

    /**
     * Retorna la actividad sobre la verificación de datos
     *
     * @return string
     */
    public function getVerificationFormActivity() : string
    {
        // Otengo la actividad actual de la verificación de datos
        // o un array vacio si no hay actividad
        $activity = json_decode($this->activity, true) ?? [];
        $msj = '';

        foreach ($activity as $act) {
            $signer = Signer::findOrFail($act['signer']);

            $msj .= "<b>$signer->name $signer->lastname</b> "
                . Lang::get('se encuentra ahora mismo revisando la verificación de datos')
                ."<br/>";
        }

        return $msj;
    }

    /**
     * Actualiza la actividad sobre la verificación de datos
     * eliminando el signer que está verificando los datos
     *
     * @param Signer $signer El firmante o usuario asignado
     *
     * @return void
     */
    public function removeVerificationFormActivity(Signer $signer) : void
    {
        // Otengo la actividad actual de la solicitud de documento, vacío si nulo
        $activity = json_decode($this->activity, true) ?? [];

        // filtrar la actividad por el usuario firmante
        $activityFilter = array_filter($activity, fn ($item) => $item['signer'] != $signer->id);

        // elimino la actividad del firmante
        $this->activity = json_encode($activityFilter);
        $this->save();
    }

    /**
     * Actualiza la actividad sobre la verificación de datos
     *
     * @param Signer $signer El firmante que atiende la verificación
     *
     * @return void
     */
    public function addVerificationFormActivity(Signer $signer) : void
    {
        // Obtengo la actividad actual de la verificación de datos
        // o un array vacio si no hay nada
        $activity = json_decode($this->activity, true) ?? [];

        // Se adiciona firmante => validación
        $newActivity = ['signer' => $signer->id];

        // Si está vacío adiciono
        if ($activity == []) {
            array_push($activity, $newActivity);

        // Verifico que no se haya adicionado
        } else {
            $activityFilter = array_filter($activity, fn ($item) => $item['signer'] == $signer->id);

            if (!$activityFilter) {
                array_push($activity, $newActivity);
            }
        }

        $this->activity = json_encode($activity);
        $this->save();
    }
}
