<?php

/**
 * Modelo de Documento Requerido de Ejemplo
 *
 * Representa un documento requerido que puede ser solicitados a los usuarios firmantes
 * Por ejemplo, el DNI, el pasaporte, su Curriculum Vitae
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class RequiredDocumentExample extends Model
{
    /**
     * Los atributos del modelo
     *
     * @var array
     */
    protected $fillable =
        [
            'lang',             // El código ISO-639-1 del idioma
            'name',             // El nombre original del documento
            'validity',         // La validez mínima del documento
            'validity_unit',    // La unidad de tiempo utilizada para expresar la validez mínima
        ];

    /**
     * Obtiene los ejemplos de documentos requeridos para el idioma seleccionado
     *
     * @param string $lang                      El código ISO-639-1 del idioma
     *
     * @return Collection                       Una colección de documentos requeridos de ejemplo
     *                                          ordenados alfabéticamente
     */
    public static function get(string $lang): Collection
    {
        return self::where('lang', $lang)->orderBy('name')->get();
    }
}
