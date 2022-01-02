<?php

/**
 * Modelo de documento acreditativo de la identidad de la persona
 *
 * Debe ser un documento que identifique a la persona:
 *
 * 1. El pasaporte
 *      @link https://es.wikipedia.org/wiki/Pasaporte
 *
 * 2. El DNI documento nacional de identidad (ES) o cédula de identidad en otros países
 *      @link https://es.wikipedia.org/wiki/DNI_(Espa%C3%B1a)
 *
 * 3. El NIE o documento de identidad para extranjeros (ES)
 *      @link https://es.wikipedia.org/wiki/N%C3%BAmero_de_identidad_de_extranjero
 *
 * 4. El Carné, Licencia o Permiso de conducción
 *      @link https://es.wikipedia.org/wiki/Autorizaci%C3%B3n_para_la_conducci%C3%B3n_de_veh%C3%ADculos
 *
 * Para este documento se debe adjuntar una imagen de su anverso y su reverso
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Passport extends Model
{
    /**
     * Lista de atributos completables
     *
     * @var array
     */
    protected $fillable =
    [
        'user_id',                   // El id del usuario propietario
        'signer_id',                 // El id del firmante
        'document_id',               // El id del documento
        'type',                      // Tipo de documento indetificativo utilizado
        // Usar un lemento de la enumeración Enums\IdentificationDocumentType
        'number',                    // El número de documento
        'expedition_date',           // La fecha de expedición
        'expiration_date',           // La fecha de expiración
        'size',                      // La suma de los tamaños de los documentos idnetificativos
        'user_image',                // La ruta del archivo con la imagen frontal del usuario
        'front_path',                // La ruta del archivo del anverso del documento
        'back_path',                 // La ruta del archivo del reverso del documento
        'face_recognition',          // +1 : Si la imagen frontal del usuario coincide con la del documento
        //  0 : Si la imagen frontal del usuario no coincide con la del documento
        // -1 : Si la identificación facial no ha sido realizada
        'ip',                        // La dirección IP desde la que se ha efectuado la grabación
        'user_agent',                // El agente de usuario desde el que se ha hecho la grabación
        'latitude',                  // La latitud desde la que se ha hecho la grabación
        'longitude',                 // La longitud desde la que se ha hecho la grabación
        'device',                    // Dispositivo que ha usado el firmante
    ];

    /**
     * Convesiones de tipo
     *
     * @var array
     */
    protected $casts =
    [
        'expedition_date'   => 'datetime',
        'expiration_date'   => 'datetime',
    ];

    /**
     * Obtiene el usuario propietario del archivo
     *
     * @return BelongsTo                        El usuario propietario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene el firmante
     *
     * @return BelongsTo                        El firmante
     */
    public function signer(): BelongsTo
    {
        return $this->belongsTo(Signer::class);
    }

    /**
     * Obtiene el documento
     *
     * @return BelongsTo                        El documento
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
