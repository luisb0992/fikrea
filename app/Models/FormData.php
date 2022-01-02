<?php

/**
 * Modelo de formulario de datos
 *
 * Gestiona los inputs o entradas, utlizado como proceso independiente o
 * como un proceso dentro de firma de un documento
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use App\Enums\FormType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Lang;

class FormData extends Model
{
    /**
     * tabla elacioanda al modelo
     *
     * @var string
     */
    protected $table = 'form_data';

    /**
     * atributos o campos para la tabla
     *
     * @var array
     */
    protected $fillable = [
        'user_id',              // usuario que envio la validacion  (para el documento)
        'signer_id',            // firmante o receptor              (para el documento e independiente)
        'document_id',          // id del documento                 (para el documento)
        'verification_form_id', // id del formulario perteneciente  (para un proceso independiente)
        'type',                 // tipo de formulario (particular o empresarial)
        'template_number',      // numero de la plantilla
        'field_name',           // nombre del campo
        'field_text',           // descripcion o texto del campo
        'min',                  // min de texto aceptado
        'max',                  // maximo de texto aceptado
        'character_type',       // tipo de caracter (numerico, texto, expecial....)
        'ip',                   // ip de donde se valida
        'user_agent',           // User Agent o informacion del SO
        'latitude',             // Latitud
        'longitude',            // Longitud
        'device',               // Dispositivo que ha usado el firmante
    ];

    /**
     * Obtener la copia que se ha hecho al formulario de datos
     * especificamentea los campos: field_text
     * puede o no tener creado algun registro, por lo tanto es opcional
     *
     * @return HasOne           La relacion con el backup del formulario de datos
     */
    public function formDataBackup(): HasOne
    {
        return $this->hasOne(FormDataBackup::class);
    }

    /**
     * La relacion con la verificacion de datos
     *
     * @return BelongsTo              Una verificacion de datos
     */
    public function verificationForm(): BelongsTo
    {
        return $this->belongsTo(VerificationForm::class);
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
    public static function getClearFormDataWithTemplateNumber(array $clearFormData, int $userId): ?array
    {
        foreach ($clearFormData as $key => $inputs) {
            $lastTemplateNumber = self::getTemplateNumberFilterdByTypeAndUSer($inputs['type'], $userId);

            $templateNumber = ($lastTemplateNumber) ? ($lastTemplateNumber + 1) : 1;

            $clearFormData[$key]['template_number'] = $templateNumber;
        }
        return $clearFormData;
    }

    /**
     * Comprobar si hay diferencia en segundos entre dos fechas
     * tomando la fecha de creacion y actualizacion del campo del formulario
     *
     * @return boolean                              // devuelve true o false si hay diferencias de segundos
     */
    public function theyAreSameDates(): bool
    {
        $seconds = $this->created_at->diffInSeconds($this->updated_at);

        return $seconds === 0;
    }

    /**
     * Obtiene el tipo de formulario en formato legible para el usuario
     *
     * @return string|null
     */
    public function getFormatTypeAttribute(): ?string
    {
        $string = null;

        if ($this->type == FormType::PARTICULAR_FORM) {
            $string = Lang::get('Formulario Particular');
        } elseif ($this->type == FormType::BUSINESS_FORM) {
            $string = Lang::get('Formulario Empresarial');
        }

        return $string;
    }

    /**
     * Guardar una actualizacion a el formulario de datos
     * Obteniendo una copia del formulario de datos original
     *
     * @param array $input          Array de campos para ser actualizados
     * @return void
     */
    public function saveFormDataBackup(array $input): void
    {
        $this->formDataBackup()->create([
            'old_field_text' => $this->field_text ?? '---',
            'new_field_text' => $input['field_text']
        ]);
    }

    /**
     * Comprobar si los textos son iguales
     *
     * @param $textField                Un nombre recibido para comparar
     * @return boolean                  Verdadero si son iguales
     */
    public function textFieldAreTheSame($textField): bool
    {
        return trim($this->field_text) == trim($textField);
    }

    /**
     * Comprubea si la el formulario ya fue modificado
     *
     * @return boolean          True si fue modificado
     */
    public function isDone(): bool
    {
        return $this->ip || $this->user_agent || $this->device;
    }

    /**
     * Obtener el campo field_text validado, tnto si tiene o no algun backup previo
     *
     * @return string|null           El valor del field_text o null si fue creado vacio
     */
    public function getFormatFieldTextAttribute(): ?string
    {
        return $this->formDataBackup ? $this->formDataBackup->old_field_text : ($this->field_text ?? '---');
    }
}
