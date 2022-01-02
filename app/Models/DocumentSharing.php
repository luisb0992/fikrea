<?php

/**
 * Modelo de Envíos realizados de un Documento (Compartición)
 *
 * Cada vez que se realiza un envío de un documento, se crea una compartición de documento
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class DocumentSharing extends Model
{
    /**
     * Atributos del modelo
     *
     * @var array
     */
    protected $fillable = [
        'document_id',          // El documento
        'sent_at',              // Momento en el que se ha realizado el envío del documento a los firmantes
        'signers',              // Una lista de destinatarios/firmantes a los cuales se ha enviado el documento
        'type',                 // El tipo de compartición
        'token',                // Token de acceso
        'title',                // El título
        'description'           // La descripción
    ];

    /**
     * Las conversiones de tipos
     *
     * @var array
     */
    protected $casts =
    [
        'sent_at'       => 'datetime',
        'visited_at'    => 'datetime',
    ];

    /**
     * No hay marcas de tiempo
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * El documento
     *
     * @return BelongsTo                        El documento relacionado con el envío o compartición
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Los contactos con los cuales se realiza el proceso de compartición de documentos
     *
     * @return HasMany                          Los contactose
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(DocumentSharingContact::class);
    }

    /**
     * Las visitas a la descarga de un conjunto de documentos
     *
     * @return HasMany                          Las visitas al conjunto de documentos compartidos
     */
    public function histories(): HasMany
    {
        return $this->hasMany(DocumentSharingHistory::class);
    }

    /**
     * Obtiene los firmantes del documento que han recibido el documento
     *
     * @return Collection                       Una colección de firmantes del documento
     */
    public function getSignersAttribute(): Collection
    {
        $signers = json_decode($this->attributes['signers'], true);

        // si existe la llave signers, se mapean todos los registros y se ignoran los vacios
        if (isset($signers['signers'])) {
            return collect(array_map(fn ($signer) => Signer::find($signer), $signers['signers']))->filter();
        } else {
            return collect([]);
        }
    }

    /**
     * Obtiene un conjunto de documentos compartidos por su token
     *
     * @param string $token                     El token del archivo
     *
     * @return self                             Un documento compartido
     * @throws ModelNotFoundException           No existe el archivo de token dado
     */
    public static function findByToken(string $token): self
    {
        // Obtiene el conjunto compartido de documentos
        // por el token genérico, que puede utilizar cualquier usuario
        $documentSharing = self::where('token', $token)->first();

        // Si no se ha encontrado un conjunto de documentos que corresponda con un token genérico,
        // se busca un token entre los usuarios con los cuales se ha compartido
        if (!$documentSharing) {
            $contact = DocumentSharingContact::where('token', $token)->firstOrFail();

            $documentSharing = $contact->documentSharing;

            // La compartición de documentos puede no existir porque todos sus documentos han sido eliminados
            if (!$documentSharing) {
                throw new ModelNotFoundException();
            }
        }

        return $documentSharing;
    }

    /**
     * Devulve un objeto configurable con la data que se va a compartir
     * para un documento compartido al workspace
     *
     * @return object|null          El objeto o null
     */
    public function getDataSharingInWorkspace() : ?object
    {
        return (object) [
            'token'         => $this->token,
            'title'         => $this->title,
            'description'   => $this->description,
            'size'          => $this->document ? $this->document->size : null,
            'name'          => $this->document ? $this->document->name : null,
            'type'          => 'application/zip',
            'user' => (object)[
                'image'     => $this->document ? $this->document->user->image : null,
                'name'      => $this->document ? $this->document->user->name : null,
                'lastname'  => $this->document ? $this->document->user->lastname : null,
                'email'     => $this->document ? $this->document->user->email : null,
                'phone'     => $this->document ? $this->document->user->phone : null,
                'dial_code' => $this->document ? $this->document->user->dial_code : null,
                'position'  => $this->document ? $this->document->user->position : null,
                'company'   => $this->document ? $this->document->user->company : null
            ]
        ];
    }

    /**
     * Marca la compartición como visitada
     *
     * @return void
     */
    public function visited(): void
    {
        $this->visited_at = now();
        $this->save();
    }
}
