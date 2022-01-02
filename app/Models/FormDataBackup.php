<?php

/**
 * Modelo de copia formulario de datos
 *
 * Almacena una copia de los datos originales, para este caso la respuesta
 * o concepto de respuesta del usuario
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormDataBackup extends Model
{
    /**
     * Nombre para la tabla
     *
     * @var string
     */
    protected $table = 'form_data_backups';

    /**
     * Atributos o campos para la tabla
     *
     * @var array
     */
    protected $fillable = [
        'form_data_id',                 // llave foranea hacia la tabla form_Data
        'old_field_text',               // antiguo campo de texto
        'new_field_text'                // nuevo campo de texto guardado
    ];

    /**
     * Obtener los antiguos datos del formulario de datos
     * en este caso el input con toda la informacion y sus validaciones
     *
     * @return BelongsTo        La relacion con el formulario de datos
     */
    public function formData(): BelongsTo
    {
        return $this->belongsTo(FormData::class);
    }
}
