<?php

/**
 * Modelo de Archivo
 *
 * Representa un archivo subido por el usuario
 *
 * @author    javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use App\Exceptions\FileIsNotAFolderException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class File extends Model
{
    /**
     * Los atributos del modelo de archivo
     *
     * @var array
     */
    protected $fillable = [
        'user_id',      // El usuario
        'name',         // El nombre original del archivo
        'size',         // El tamaño del archivo en bytes
        'path',         // La ruta del archivo
        'md5',          // El hash md5 del archivo
        'type',         // El tipo mime del archivo
        'token',        // El token del archivo
        'parent_id',    // ID de la carpeta que contiene el archivo; nulo, si está en la raíz.
        'is_folder',    // Verdadero si el archivo es una carpeta; falso, en caso contrario.
        'notes',        // Comentarios acerca de la carpeta (para indicar que está asociada a un contacto, por ejemplo)
        'full_path',    // Ruta completa hasta el archivo
        'locked',
    ];

    protected $casts = [
        'full_path' => 'json',
    ];

    /**
     * Retorna el tipo MIME del documento
     *
     * @return BelongsTo
     */
    public function mimeType(): BelongsTo
    {
        return $this->belongsTo(MediaType::class, 'media_type', 'type');
    }

    /**
     * El usuario propietario del archivo
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene las comparticiones por redes sociales realizadas
     *
     * @return MorphMany         Las comparticiones
     */
    public function shareSocialNetwork() : MorphMany
    {
        return $this->morphMany(ShareOnSocialNetwork::class, 'shareontable');
    }

    /**
     * Retorna la carpeta a la que pertenece el archivo
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(__CLASS__);
    }

    /**
     * Retorna los ficheros contenidos en una carpeta
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     * @throws \App\Exceptions\FileIsNotAFolderException
     */
    public function files(): HasMany
    {
        if (!$this->is_folder) {
            throw new FileIsNotAFolderException();
        }

        $files = $this->hasMany(__CLASS__, 'parent_id');

        // Excluir los ficheros bloqueados
        $files->where('locked', false);

        return $files->orderBy('name');
    }

    /**
     * Retorna los logs de las acciones ejecutadas sobre el fichero
     *
     * @return HasMany
     */
    public function logs(): HasMany
    {
        return $this->hasMany(FileLog::class);
    }

    /**
     * Obtiene un archivo por su token
     *
     * @param string $token El token del archivo
     *
     * @return self                             Un archivo
     * @throws ModelNotFoundException           No existe el archivo de token dado
     */
    public static function findByToken(string $token): self
    {
        return File::where('token', $token)->firstOrFail();
    }

    /**
     * Determina si un archivo puede ser firmado o no
     *
     * Un archivo como una imagen, un documento Microsoft Word o Excel, un PDF
     * son ejemplos de documentos que se pueden firmar
     *
     * @return bool                             true si el archivo se puede firmar
     *                                          false en caso contrario
     */
    public function canBeSigned(): bool
    {
        return MediaType::where('media_type', $this->type)->first()->signable ?? false;
    }
}
