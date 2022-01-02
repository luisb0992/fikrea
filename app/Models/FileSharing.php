<?php

/**
 * Modelo de FileSharing o paquete de archivos que es compartido con uno o más destinatarios
 *
 * Representa un conjunto de archivos seleccionados que se desean compartir con
 * uno o más destinatarios
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Excepciones requeridas
 */
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FileSharing extends Model
{
    /**
     * Atributos del modelo
     *
     * @var array
     */
    protected $fillable =
        [
            'user_id',                          // El id del usuario propietario
            'token',                            // El token para el paquete de archivos compartidos
            'files',                            // La lista de archivos (ids)
            'recipients',                       // La lista de destinatarios (email o teléfono)
            'title',
            'description',
        ];

    /**
     * El usuario propietario que comparte los archivos
     *
     * @return BelongsTo                        El usuario propietario de los archivos
     */
    protected function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene los archivos que componen el paquete de archivos compartidos
     *
     * @return Collection                       Una colección de archivos
     */
    public function getFilesAttribute(): Collection
    {
        $files = json_decode($this->attributes['files'], true);

        return collect(array_map(fn ($file) => File::find($file), $files));
    }

    /**
     * Obtiene el nombre del conjunto de archivos compartido
     *
     * @return string                           Un nombre que identifica el conjunto de archivos
     *                                          compartidos o compartición
     */
    public function getNameAttribute(): string
    {
        $appname  = Str::lower(config('app.name'));

        return "{$appname}-my-files-{$this->id}";
    }

    /**
     * Obtiene el número de archivos del conjunto
     *
     * @return int                              El número de archivos
     */
    public function getNumFilesAttribute(): int
    {
        return $this->files->sum(fn ($file) => $file != null);
    }

    /**
     * Obtiene el tamaño del archivo del conjunto de archivos compartido sin compresión
     *
     * @return int                              El tamaño del archivo en bytes
     */
    public function getSizeAttribute(): int
    {
        return $this->files->sum(fn ($file) => $file->size ?? 0);
    }

    /**
     * Los contactos con los cuales se realiza el proceso de compartición de los archivos
     *
     *
     * @return HasMany                          Los contactos con los cuales se comparte
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(FileSharingContact::class);
    }

    /**
     * Las visitas a la descarga de un conjunto de archivos que han sido compartidos
     *
     *
     * @return HasMany                          Las visitas al set o conjunto de archivos compartidos
     */
    public function histories(): HasMany
    {
        return $this->hasMany(FileSharingHistory::class);
    }

    /**
     * Obtiene un conjunto de archivos compartidos por su token
     *
     * @param string $token                     El token del archivo
     *
     * @return self                             Un archivo
     * @throws ModelNotFoundException           No existe el archivo de token dado
     */
    public static function findByToken(string $token): self
    {
        // Obtiene la el conjunto compartido de archivos
        // por el token genérico, que puede utilizar cualquier usuario
        $fileSharing = FileSharing::where('token', $token)->first();

        // Si no se ha encontrado un conjunto de archivos que corresponda con un token genérico,
        // se busca un token entre los usuarios con los cuales se ha compartido
        if (!$fileSharing) {
            $contact = FileSharingContact::where('token', $token)->firstOrFail();
      
            $fileSharing = $contact->fileSharing;

            // La compartición de archivos puede no existir porque todos sus archivos han sido eliminados
            if (!$fileSharing) {
                throw new ModelNotFoundException;
            }
        }

        return $fileSharing;
    }
}
