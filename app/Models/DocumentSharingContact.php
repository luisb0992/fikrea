<?php

/**
 * Modelo de Envíos realizados de un Documento a contactos especificos
 *
 * @author luisbardev <luisbardev@gmail.com> <luisbardev.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class DocumentSharingContact extends Model
{
    /**
     * La tbla asociada al modelo
     *
     * @var string
     */
    protected $table = 'document_sharing_contacts';

    /**
     * Atributos del modelo
     *
     * @var array
     */
    protected $fillable =
        [
            'document_sharing_id',              // Id de la compartición del documento
            'name',                             // El nombre del contacto
            'lastname',                         // Los apellidos del contacto
            'email',                            // La dirección de correo electrónico del contacto
            'phone',                            // El teléfono del contacto
            'dni',                              // El número de documento indentificativo (DNI)
                                                // @link https://es.wikipedia.org/wiki/DNI_(Espa%C3%B1a)
            'company',                          // La compañía
            'position',                         // El cargo o puesto dentro de la compañía
            'token',                            // El token de acceso al conjunto de documentos compartido
        ];

    /**
     * Obtiene la compartición de documentos
     *
     * @return HasOne                           La compartición de documentos
     */
    public function documentSharing(): BelongsTo
    {
        return $this->belongsTo(DocumentSharing::class);
    }

    /**
     * Obtiene el contacto por el token
     *
     * @param string $token                     El token del usuario
     *
     * @return self|null                        El contacto que corresponde con ese token
     *                                          o null sino se corresponde con ningún contacto
     */
    public static function findByToken(string $token): ?self
    {
        return self::where('token', $token)->first();
    }

    /**
     * Obtiene los mensajes sms recibidos
     *
     * @return MorphMany                     Los sms recibidos
     */
    public function smses() : MorphMany
    {
        return $this->morphMany(Sms::class, 'sendable');
    }
}
