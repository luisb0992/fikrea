<?php

/**
 * Modelo FileSharingContact
 *
 * Representa un contacto con el cual se realiza una compartición de archivos
 *
 * @author rosellpp <rpupopolanco@gmail.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class FileSharingContact extends Model
{
    /**
     * Atributos del modelo
     *
     * @var array
     */
    protected $fillable =
        [
            'file_sharing_id',                  // Id de la compartición de archivo
            'name',                             // El nombre del contacto
            'lastname',                         // Los apellidos del contacto
            'email',                            // La dirección de correo electrónico del contacto
            'phone',                            // El teléfono del contacto
            'dni',                              // El número de documento indentificativo (DNI)
                                                // @link https://es.wikipedia.org/wiki/DNI_(Espa%C3%B1a)
            'company',                          // La compañía
            'position',                         // El cargo o puesto dentro de la compañía
            'token',                            // El token de acceso al conjunto de archivos compartido
        ];

    /**
     * Obtiene la compartición de archivo
     *
     * @return HasOne                           La compartición de archivo
     */
    public function fileSharing(): BelongsTo
    {
        return $this->belongsTo(FileSharing::class);
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
