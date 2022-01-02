<?php

/**
 * TODO
 * @REYNIER
 * COMENTARIAR CLASE COMO EL RESTO
 */

 namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaType extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'media_type',
        'type',
        'subtype',
        'extensions',
        'signable',
        'can_apply_ocr',
        'description',
    ];

    protected $casts = [
        'signable' => 'boolean',
        'can_apply_ocr' => 'boolean',
    ];

    /**
     * Obtener los mime type de los archivos o documentos
     * que pueden ser o no firmados
     *
     * @return array|null               Un array de mimes o null
     */
    public static function getMimesCanBeSigned(): ?array
    {
        return self::where('signable', true)
            ->get('media_type')
            ->pluck('media_type')
            ->toArray();
    }
}
