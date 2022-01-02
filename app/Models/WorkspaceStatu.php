<?php

/**
 * Modelo de WorkspaceStatu
 *
 * Representa los status en el workspace relacionado a una solicitud de documento requerido
 *
 * @author Jonathan Sanchez <jonathanch1991@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Enums\WorkspaceStatu as WorkspaceStatus;

class WorkspaceStatu extends Model
{
    /**
     * Lista de atributos WorkspaceStatu
     *
     * @var array
     */
    protected $fillable =
    [
        'status',                     // El nombre del status en el workspace
    ];

    /**
     * Devuelve un color representativo según el estado
     *
     * @return string                El color que representa el estado
     */
    public function getColor() : string
    {
        return $this->id == WorkspaceStatus::PENDIENTE?
            'warning' : ($this->id == WorkspaceStatus::CANCELADO? 'danger' : 'success');
    }
}
