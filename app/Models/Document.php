<?php

/**
 * Modelo Documento
 *
 * Representa un documento subido por el usuario para ser firmado y compartido
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Lang;

/**
 * Modelos requeridos
 */

use App\Models\Signer;
use App\Models\Validation;

/**
 * Enumeraciones requeridas
 */
use App\Enums\ValidationType;
use Carbon\Carbon;

class Document extends Model
{
    /**
     * Las imágenes resultantes de procesar el documento
     * No persisten en el modelo
     *
     * @var array
     */
    public $images;

    /**
     * Atributos del documento
     *
     * @var array
     */
    protected $fillable =
    [
        'name',                 // El nombre del documento
        'comment',              // El comentario o descripción
        'content',              // El contenido del documento (si se ha creado el documento en lugar se ser subido)
        'guid',                 // El identificador único global o GUID del documento
        'original_path',        // La ruta del archivo original
        'original_md5',         // El hash md5 del archivo original
        'original_sha1',        // El hash sha1 del archivo original
        'converted_path',       // La ruta del archivo convertido a PDF (si tenía otro formato)
        'signed_path',          // La ruta del archivo firmado
        'signed_md5',           // El hash md5 del archivo firmado
        'signed_sha1',          // El hash sha1 del archivo firmado
        'type',                 // El tipo Mime del archivo
        'size',                 // El tamaño del archivo original en bytes
        'pages',                // Número de páginas del documento
        'converted_size',       // El tamaño del archivo convertido en bytes
        'sent',                 // Si el documento a sido enviado a los usuarios firmantes o no
        'deleted_at',           // Momento de eliminación del documento
        'sent_at',              // Momento en el que se ha realizado el último envío del documento a los firmantes
        'processing',           // Si el documento está siendo procesado en este momento o no
        'processed_at',         // Momento en el que ha sido procesado el documento por última vez
        'copy_at',              // Momento en el que ha sido copiado a otra ubicacion el documento
    ];

    /**
     * Conversiones de tipos
     *
     * @var array
     */
    protected $casts =
    [
        'purged'        => 'boolean',
        'sent'          => 'boolean',
        'deletedAt'     => 'datetime',
        'sentAt'        => 'datetime',
        'processing'    => 'boolean',
        'processed_at'  => 'datetime',
        'copy_at'       => 'datetime',
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
     * Obtiene el usuario propietario o creador del documento
     *
     * @return BelongsTo                        El usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtiene los firmantes de un documento
     *
     * @return HasMany                          Un documento puede ser firmado por varias personas
     *                                          que son los firmantes
     */
    public function signers(): HasMany
    {
        return $this->hasMany(Signer::class, 'document_id')->orderBy('creator', 'desc')->orderBy('lastname');
    }

    /**
     * Obtiene las validaciones de un documento
     *
     * @return HasMany                          Un documento puede tener varias validaciones
     *                                          o acciones que realizan sobre él los firmantes
     */
    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class);
    }

    /**
     * Obtiene el proceso perteneciente
     *
     * @return MorphOne                     El proceso relacionado
     */
    public function process(): MorphOne
    {
        return $this->morphOne(Process::class, 'statable');
    }

    /**
     * Obtiene los formularios de datos del documento
     *
     * @return HasMany                          Un documento puede tener varios formularios de datos
     *                                          para distintos usuarios o firmantes
     */
    public function formdata(): HasMany
    {
        return $this->hasMany(FormData::class);
    }

    /**
     * Obtiene las firmas de un documento
     *
     * @return HasMany                          Un documento puede tener varias firmas
     *                                          Cada firma corresponde a un firmante en una posición
     *                                          determinada del documento
     */
    public function signs(): HasMany
    {
        return $this->hasMany(Sign::class);
    }

    /**
     * Obtiene las cajas de texto de un documento
     *
     * @return HasMany                          Un documento puede tener varias cajas de texto
     *                                          Cada caja corresponde a un firmante en una posición
     *                                          determinada del documento
     */
    public function boxs(): HasMany
    {
        return $this->hasMany(Textbox::class);
    }

    /**
     * Obtiene los sellos estampados sobre un documento
     *
     * @return HasMany                          Un documento puede tener varios sellos estampados
     *                                          en una posición determinada por su creador
     */
    public function stamps(): HasMany
    {
        return $this->hasMany(DocumentStamp::class);
    }


    /**
     * Obtiene las grabaciones de audio de un documento
     *
     * @return HasMany                          Un documento puede tener varias grabaciones de audio asociadas
     *                                          Estas grabaciones de audio pueden ser de usuarios distintos o
     *                                          pueden haber sido proporcionadas por el mismo usuario
     */
    public function audios(): HasMany
    {
        return $this->hasMany(Audio::class);
    }

    /**
     * Obtiene las grabaciones de video de un documento
     *
     * @return HasMany                          Un documento puede tener varias grabaciones de video asociadas
     *                                          Estas grabaciones de video pueden ser de usuarios distintos o
     *                                          pueden haber sido proporcionadas por el mismo usuario
     */
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    /**
     * Obtiene las capturas de pantalla de un documento
     *
     * @return HasMany                          Un documento puede tener varias capturas de pantalla asociadas
     *                                          Estas capturas de pantalla pueden ser de usuarios distintos o
     *                                          pueden haber sido proporcionadas por el mismo usuario
     */
    public function captures(): HasMany
    {
        return $this->hasMany(Capture::class);
    }

    /**
     * Obtiene las solicitudes de documentos(como parte de procesos de validaciones) de un documento
     *
     * @return Collection                          Un documento puede tener varias solicitudes de documentos
     *                                             asociadas a validaciones de sus firmantes
     */
    public function requests(): Collection
    {
        // Aqui debo obtener las solicitudes de documentos de mis firmantes
        return $this->signers
            ->filter(fn ($signer) => $signer->request() != null)
            ->map(fn ($signer) => $signer->request());
    }

    /**
     * Obtiene los envíos o comparticiones de un documento
     *
     * Un documento se comparte una vez cuando se crea,
     * pero puede ser compartido en sucesivas ocasiones con los usuarios
     * que no ha efectuado todavía las validaciones que se les ha propuesto
     *
     * @return HasMany
     */
    public function sharings(): HasMany
    {
        return $this->hasMany(DocumentSharing::class)->orderBy('sent_at', 'desc');
    }

    /**
     * Obtiene las visitas al documento
     *
     * @return HasMany                          Las visitas realizadas al documento
     */
    public function visits(): HasMany
    {
        return $this->hasMany(SignerVisit::class);
    }

    /**
     * Añade al propio autor del documento como firmante si no estaba añadido previamente
     *
     * @param User $user                        El usuario autor del documento
     *
     * @return void
     */
    public function addCreatorAsSigner(User $user): void
    {
        if (!$this->signers()->where('creator', true)->first()) {
            // Obtiene el usuario autor del documento como firmante
            $signer = $user->toArray();
            $signer['creator'] = true;

            $this->signers()->create($signer);
        }
    }

    /**
     * Obtiene un documento mediante el token de acceso de un usuario
     *
     * @param string $token                     El token de acceso de un usuario
     *
     * @return Document                         El documento
     * @throws ModelNotFoundException           No existe el documento
     */
    public static function findByToken(string $token): Document
    {
        $signer = Signer::where('token', $token)->firstOrFail();

        return $signer->document;
    }

    /**
     * Envía el documento a firmar por parte de los firmantes
     */
    public function send(): void
    {
        // Se actualiza la fecha de actualización del documento
        $this->updated_at = new \DateTime;

        // Se marca que ha sido enviado
        $this->sent    = true;
        $this->sent_at = new \DateTime;

        // Se guarda el documento
        $this->save();
    }

    /**
     * Obtiene si un documento ha sido firmado por todos y cada uno de los firmantes del mismo
     *
     * @return bool                             true si el archivo ha sido firmado por todos y cada
     *                                          uno de los firmantes, false en caso contrario
     */
    public function hasBeenSigned(): bool
    {
        // Si el archivo debe ser validado mediante firma manuscrita.
        // compara el número de firmas efectuadas con las que deben ser realizadas
        return $this->mustBeValidateByHandWrittenSignature() &&
            $this->signs->filter(fn ($sign) => $sign->signed)->count() ===  $this->signs->count();
    }

    /**
     * Determina si el origen del documento es que ha sido subido al servidor
     * o ha sido creado manualmente
     *
     * @return bool                             true si el archivo ha sido subido
     *                                          false si ha sido creado manualmente
     */
    public function getHasBeenUploadedAttribute(): bool
    {
        // Los archivos subidos al servidor carecen de contenido
        // El contenido se crea manualmente a través de un editor
        return $this->content == null;
    }

    /**
     * Obtiene el grado de progreso de firma y validación de un documento
     *
     * Si n es el número de validaciones a realizar en el documento y m el número de validaciones efectuadas,
     * el grado de progreso se define como:
     *
     * r = m · 100 / n
     *
     * @return int                              El grado de progreso del documento en tanto por ciento
     */
    public function getProgressAttribute(): int
    {
        try {
            return intval(
                $this->validations->filter(
                    fn ($validation) => $validation->process && $validation->process->isDone()
                )->count() * 100
                    /
                    $this->validations->count()
            );
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Obtiene el tipo de validaciones que se deben efectuar sobre un documento
     *
     * @return ValidationType[]                 Una lista de validaciones a realizar
     */
    public function getValidationTypesAttribute(): array
    {
        return $this->validations->map(fn ($validation) => $validation->validation)
            ->unique()
            ->toArray();
    }

    /**
     * Si el documento debe ser validado mendiente edición de textos
     *
     * @return bool                             true si el documento debe ser validado con
     *                                          edición de textos
     */
    public function mustBeValidateByTextBoxs(): bool
    {
        return in_array(ValidationType::TEXT_BOX_VERIFICATION, $this->validationTypes);
    }

    /**
     * Si el documento debe ser validado mendiente firma manuscrita
     *
     * @return bool                             true si el documento debe ser validado con
     *                                          firma manuscrita
     */
    public function mustBeValidateByHandWrittenSignature(): bool
    {
        return in_array(ValidationType::HAND_WRITTEN_SIGNATURE, $this->validationTypes);
    }

    /**
     * Si el documento debe ser validado mendiente una grabación de audio
     *
     * @return bool                             true si el documento debe ser validado con una
     *                                          grabación de audio
     */
    public function mustBeValidateByAudioFile(): bool
    {
        return in_array(ValidationType::AUDIO_FILE_VERIFICATION, $this->validationTypes);
    }

    /**
     * Si el documento debe ser validado mendiente una grabación de video
     *
     * @return bool                             true si el documento debe ser validado con una
     *                                          grabación de video
     */
    public function mustBeValidateByVideoFile(): bool
    {
        return in_array(ValidationType::VIDEO_FILE_VERIFICATION, $this->validationTypes);
    }

    /**
     * Si el documento debe ser validado mendiente una grabación de pantalla
     *
     * @return bool                             true si el documento debe ser validado con una
     *                                          grabación de pantalla
     */
    public function mustBeValidateByScreenCapture(): bool
    {
        return in_array(ValidationType::SCREEN_CAPTURE_VERIFICATION, $this->validationTypes);
    }

    /**
     * Si el documento debe ser validado mendiente una solicitud de documentos
     *
     * @return bool                             true si el documento debe ser validado con una
     *                                          solicitud de documentos
     */
    public function mustBeValidateByDocumentRequest(): bool
    {
        return in_array(ValidationType::DOCUMENT_REQUEST_VERIFICATION, $this->validationTypes);
    }

    /**
     * Si el documento debe ser validado mendiante algún documento de identificación
     * como el pasaporte o el carné de identidad
     *
     * @return bool                             true si el documento debe ser validado con un
     *                                          documento de indentificación
     */
    public function mustBeValidateByPassport(): bool
    {
        return in_array(ValidationType::PASSPORT_VERIFICATION, $this->validationTypes);
    }

    /**
     * Si el documento debe ser validado con un formulario de datos especificos
     *
     * @return boolean                          true si el documento debe ser validado con un
     *                                          formulario de datos especifico
     */
    public function mustBeValidateByFormData(): bool
    {
        return in_array(ValidationType::FORM_DATA_VERIFICATION, $this->validationTypes);
    }

    /**
     * Si el documento ya ha sido compartido a fecha actual o no
     *
     * @return bool                             true si el documento ya se ha enviado, ha sido compartido hoy mismo
     *                                          con los firmantes o false en caso contrario
     */
    public function sharingHasBeenSentToday(): bool
    {
        return $this->sharings->contains(
            fn ($sharing) => (new \DateTime($sharing->sent_at))->format('d-m-Y') == (new \DateTime)->format('d-m-Y')
        );
    }

    /**
     * Determina si un documento puede ser firmado o no
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

    /**
     * Elimina el documento
     *
     * El documento no se elimina físicamente, pasa a la papelera
     *
     * @return bool                             true si se ha eliminado con éxito
     *                                          false en caso contrario
     */
    public function purge(): bool
    {
        // Marcamos el documento como eliminado
        $this->purged     = true;
        $this->deleted_at = new \Datetime;

        return $this->save();
    }

    /**
     * Recupera el documento de la papelera
     *
     * @return bool                             true si se ha recuperado con éxito
     *                                          false en caso contrario
     */
    public function restore(): bool
    {
        // Marcamos el documento como eliminado
        $this->purged     = false;
        $this->deleted_at = null;

        return $this->save();
    }

    /**
     * Elimina el documento definitivamente
     *
     * @return bool                             true si se ha eliminado con éxito
     *                                          false en caso contrario
     */
    public function remove(): bool
    {
        // Elimina los archivo originales y convertidos de cualquier almacenamiento
        // utilizado por la aplicación
        foreach (['s3', 'public'] as $store) {
            Storage::disk($store)->delete(
                [
                    $this->original_path,
                    $this->converted_path,
                ]
            );
        }

        return $this->delete();
    }

    /**
     * Marca que el documento está siendo procesado
     *
     * Indica que se está generando el documento firmado, proceso que puede demorar tiempo
     *
     * @return void
     */
    public function isBeingProcessed(): void
    {
        $this->processing = true;
        $this->save();
    }

    /**
     * Marca que el documento ha terminado de ser procesado
     *
     * @return void
     */
    public function hasBeenProcessed(): void
    {
        $this->processing   = false;
        $this->processed_at = new \DateTime;
        $this->save();
    }

    /**
     * Comprueba si el documento está siendo procesado en este momento o no
     *
     * @return bool                             true si el documento está siendo procesado ahora
     *                                          false en caso contrario
     */
    public function isInProcess(): bool
    {
        return $this->processing;
    }

    /**
     * Obtiene los firmantes de un documento que deban cumplir con alguna validacion especifica
     * y que estos firmantes no sea el creador del documento
     *
     * @param ValidationType                    // El tipo de validacion
     * @return Collection|null
     */
    public function signersComplyWithValidation($validationType): ?Collection
    {
        return $this->signers->filter(
            fn ($signer) => !$signer->creator && $signer->mustValidate($this, $validationType)
        );
    }

    /**
     * Devuelve las comparticiones realizadas con destinatarios o contactos fuera de la firma
     * del documento
     *
     * @return Collection|null                  La coleccionde comparticiones o null
     */
    public function getDocumentSharingWithToken(): ?Collection
    {
        return $this->sharings->filter(fn ($sharing) => $sharing->token);
    }

    /**
     *  Agrupa los datos del formulario de datos para ser procesados adecuadamente
     *  los datos se agrupan por el firmante y devuelve el array agrupado
     *
     * @param Array                     todos los campos y sus validaciones
     * @return Array|null
     */
    public function groupFormDataToBeSaved($request): ?array
    {
        $groupArray = [];

        foreach ($request['formDataValidate'] as $key => $formdata) {
            if (array_key_exists('signer_id', $formdata)) {
                $groupArray[$formdata['signer_id']][] = $formdata;
            } else {
                $groupArray[""][] = $formdata;
            }
        }

        return $groupArray;
    }

    /**
     * Comprueba si el documento tiene validaciones de Solicitud de documentos
     * y el estado de la misma, si se ha configurado la misma o no
     *
     * @return bool                             true si se ha configurado la solicitud de documentos
     *                                          false en caso contrario
     */
    public function isRequestValidationConfigured(): bool
    {
        $configured = false;
        // Obtengo las validaciones de Solicitud de Documentos
        // Verifico que el firmante de cada una tenga el document_request_id asignado
        $validations = $this->validations()
            ->where('validation', ValidationType::DOCUMENT_REQUEST_VERIFICATION);

        // Si no tiene validaciones de Solicitud de documentos
        // es como si estuviera configurado porque no hay que hacer nada
        if ($validations->get()->isEmpty()) {
            return true;
        }

        $validations
            ->each(
                function ($validation, $key) use (&$configured) {
                    if ($validation->signer->request()) {
                        $configured = true;
                    }
                }
            );

        return $configured;
    }

    /**
     * Si hay que validar o no firma manuscrita en el documento actual
     *
     * @param ValidationType                    el tipo de validacion a verificar
     * @return array|null                       El array con las validaciones o vacio
     */
    public function validateProcessOf($validationType): ?array
    {
        return array_filter(
            $this->validations->toArray(),
            fn ($validation) => $validation['validation'] == $validationType
        );
    }

    /**
     * Si el documento está marcado como activo
     * si alguna validación se está atendiendo en este momento
     *
     * @return bool                 Si se está atendiendo una validación o no
     */
    public function isActive(): bool
    {
        return $this->validations->contains(
            fn ($validation) => $validation->process and $validation->process->active === 1
        );
    }

    /**
     * Actualiza la actividad sobre un documento
     * adicionando signer y validación que está realizando
     *
     * @param Signer     $signer     El firmante que atiende x validación
     * @param Validation $validation La validación que comenzó a atender
     *
     * @return void
     */
    public function addActivity(Signer $signer, Validation $validation): void
    {
        // Si no existe se crea
        if (!$this->process) {
            $this->process()->create([]);
        }

        // Actualizo actividad en el proceso
        $this->process->addDocumentActivity($signer, $validation);
    }

    /**
     * Actualiza la actividad sobre un documento
     * eliminando signer y validación que estába realizando
     *
     * @param Signer     $signer     El firmante que atiende x validación
     * @param Validation $validation La validación que comenzó a atender
     *
     * @return void
     */
    public function removeActivity(Signer $signer, Validation $validation): void
    {
        // Si no existe se crea
        if (!$this->process) {
            $this->process()->create([]);
        }

        // Actualizo actividad en el proceso
        $this->process->removeDocumentActivity($signer, $validation);
    }

    /**
     * Retorna la actividad sobre un documento
     *
     * @return string
     */
    public function getActivity(): string
    {
        // Si no existe se crea
        if (!$this->process) {
            $this->process()->create([]);
        }

        // Otengo la actividad actual del documento
        return $this->process->getDocumentActivity();
    }

    /**
     * Elimino toda actividad de firmantes sobre el documento
     *
     * @return void
     */
    public function inactivate(): void
    {
        if ($this->process->activity) {
            $this->process->activity = json_encode([]);
            $this->save();
        }
    }

    /**
     * Si el documento tiene una verificación de datos realizada o configurada
     *
     * @return boolean                      true si ya fue realizada
     *                                      false si no esta realizada
     */
    public function hasADataVerificationPerformed(): bool
    {
        $status = false;

        if ($this->mustBeValidateByFormData()) {
            if ($this->formdata->count()) {
                $status = true;
            }
        }

        return $status;
    }

    /**
     * Elimina las imágenes utilizadas en el procesamiento para firmar o meter laos textos
     *
     * @return void
     */
    public function deleteImagesUsesInProcess(): void
    {
        // Eliminar las imágenes utilizadas en el procesamiento
        foreach ($this->images as $image) {
            unlink($image);
        }
    }

    /**
     * Devuelve un true o false si existe o no el documento original y el firmado
     *
     * @return boolean                                      Un true si ambos existen o false
     */
    public function originalDocumentAndSignedDocumentExist(): bool
    {
        return $this->originalDocumentExists() && $this->signedDocumentExists();
    }

    /**
     * Verifica si el documento original existe
     *
     * @return boolean                  True si existe o false caso contrario
     */
    public function originalDocumentExists(): bool
    {
        return Storage::disk(env('APP_STORAGE'))->exists($this->original_path);
    }

    /**
     * Verifica si el documento firmado existe
     *
     * @return boolean                  True si existe o false caso contrario
     */
    public function signedDocumentExists(): bool
    {
        return Storage::disk(env('APP_STORAGE'))->exists($this->signed_path);
    }

    /**
     * Marcar el documento como copiado
     *
     * @return void
     */
    public function markAsCopied() : void
    {
        $this->copy_at = Carbon::now();
        $this->save();
    }

    /**
     * Devuelve un nombre para el documento "original" que puede ser usado
     * cuando es copiado a otro lugar
     *
     * @return string                       El nuevo nombre
     */
    public function getOriginalDocumentNameAttribute() : string
    {
        return '['.Lang::get('Original').'-'.date('dmYhis').']';
    }

    /**
     * Devuelve un nombre para el documento "firmado" que puede ser usado
     * cuando es copiado a otro lugar
     *
     * @return string                       El nuevo nombre
     */
    public function getSignedDocumentNameAttribute() : string
    {
        return '['.Lang::get('Firmado').'-'.date('dmYhis').']';
    }

    /**
     * Comprobar si el documento o archivo segun el mime type
     * puede ser o no firmado
     *
     * @return boolean          True si puede ser firmado o false caso contrario
     */
    public function onlyCanBeSigned(): bool
    {
        $mimesSigned = MediaType::getMimesCanBeSigned();

        $mimesSigned = count($mimesSigned) ? $mimesSigned : [];

        return in_array($this->type, $mimesSigned);
    }
}
