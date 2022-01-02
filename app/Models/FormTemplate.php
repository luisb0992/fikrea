<?php

/**
 * Modelo de plantillas para formulario
 *
 * Gestiona las plantillas del formulario de datos para usuarios y
 * para la nativas de la aplicacion
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormTemplate extends Model
{
    /**
     * la tabla plantillas de formulario relacionada al modelo
     *
     * @var string
     */
    protected $table = 'form_templates';

    /**
     * Atributos de la tabla form_templates
     *
     * @var array
     */
    protected $fillable = [
        'user_id',              // Id del usuario propietario de la plantilla
        // si esta lleno es una plantilla de usuario
        // sino es una plantilla del sistema
        'type',                 // tipo de formulario (particular o empresarial)
        'template_number',      // numero de la plantilla
        'field_name',           // nombre del campo
        'field_text',           // descripcion o texto del campo
        'min',                  // min de texto aceptado
        'max',                  // maximo de texto aceptado
        'character_type'        // tipo de caracter (numerico, texto, expecial....)
    ];

    /**
     * Obtiene el usuario creador de la plantilla
     *
     * @return BelongsTo                        El usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene el ultimo valor para el numero de plantilla almacenado en la BD
     *
     * @param integer $userId                           // Id del usuario
     * @param integer $type                             // Tipo de formulario
     * @return integer|null                             // valor del template number o null
     */
    public static function getTemplateNumberFilterdByTypeAndUSer(int $type, int $userId): ?int
    {
        return self::where('type', $type)
                    ->where('user_id', $userId)
                    ->latest()->value('template_number');
    }

    /**
     * Configura el array obtenido dando el numero de plantilla antes de almacenar
     * esto para diferenciar y agrupar  las plantillas de las demas
     *
     * @param int $usrId                            // Id del usuario
     * @param array $clearFormData                  // Array agrupado ha ser configurado
     * @return Array|null                           // El array limpio ya agrupado
     */
    public static function getClearFormDataWithTemplateNumber(array $clearFormData, int $userId): ?Array
    {
        foreach ($clearFormData as $key => $inputs) {
            $lastTemplateNumber = self::getTemplateNumberFilterdByTypeAndUSer($inputs['type'], $userId);

            $templateNumber = ($lastTemplateNumber) ? ($lastTemplateNumber + 1) : 1;

            $clearFormData[$key]['template_number'] = $templateNumber;
        }

        return $clearFormData;
    }

    /**
     * Almacena multiples plantillas d formulario de datos
     *
     * @param array $formTemplate                  // Contiene elos datos a ser almacenados
     */
    public static function saveMultipleFormTemplate(array $formTemplate)
    {
        foreach ($formTemplate as $inputs) {
            FormTemplate::create($inputs);
        }
    }
}
