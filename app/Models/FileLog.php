<?php

/**
 * TODO
 * @REYNIER
 * COMENTARIAR CLASE COMO EL RESTO
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id',
        'action',
        'description',
    ];

    /**
     * Retorna el fichero sobre el que se registra el log
     *
     * @return BelongsTo
     */
    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}
