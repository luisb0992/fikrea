<?php

/**
 * Modelo de comentario dentro del workspace
 *
 * Representa los comentarios en el workspace relacionado a una solicitud de documento requerido
 *
 * @author Jonathan Sanchez <jonathanch1991@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkspaceComment extends Model
{
    /**
     * Lista de atributos WorkspaceComment
     *
     * @var array
     */
    protected $fillable =
    [
        'document_request_id',        // El id del documento solicitado
        'signer_id',                  // El id del signer
        'status',                     // El nombre del status en el workspace
    ];

    /**
     * El comentario tiene una solicitud de documentos asociado
     *
     * @return BelongsTo                        La solicitud
     */
    public function documentRequest() : BelongsTo
    {
        return $this->belongsTo('App\Models\DocumentRequest');
    }

    /**
     * El que comenta es un signer a la solicitud del documento
     *
     * @return BelongsTo                        El signer
     */
    public function signer() : BelongsTo
    {
        return $this->belongsTo('App\Models\Signer');
    }
}
