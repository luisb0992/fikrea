<?php

/**
 * Modelo de firmante
 *
 * Representa un usuario al que se le invita a firmar o validar un documento,
 * o bien, a subir la documentación que le ha sido solicitada
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Excepciones requeridas
 */

use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Trait UserDeviceTrait
 */
use App\Http\Controllers\Traits\UserDeviceTrait;
use Carbon\Carbon;

/**
 * Modelos utilizados
 */
use App\Models\Sms;

/**
 * Enums
 */
use App\Enums\SignerProcesType;

class Signer extends Model
{
    use UserDeviceTrait;
    
    /**
     * Los atributos de un usuario "firmante"
     *
     * @var array
     */
    protected $fillable =
        [
            'creator',                      // Si el firmante es el propio creador del documento
            'name',                         // El nombre
            'lastname',                     // Los apellidos
            'email',                        // La dirección de correo
            'phone',                        // El teléfono de contato
            'dni',                          // El número de documento identificativo (DNI)
                                            // @link https://es.wikipedia.org/wiki/DNI_(Espa%C3%B1a)
            'company',                      // La compañía
            'position',                     // El cargo dentro de la compañía
            'token',                        // El token de acceso
            'canceled_at',                  // Fecha en la que el firmante ha cancelado su proceso de firma
            'canceled_subject',             // El motivo opcional de la cancelación del proceso

            'verification_form_id',         // el proceso de verificación de datos (si posee)
            'verificationform_at',          // la fecha que realizo la verificación de datos (si posee)
        ];

    /**
     * Las conversiones de tipos
     *
     * @var array
     */
    protected $casts =
        [
            'creator'               => 'boolean',
            'canceled_at'           => 'datetime',
            'verificationform_at'   => 'datetime',
        ];

    /**
     * No hay marcas de tiempo
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Obtiene las firmas de un firmante
     *
     * @return HasMany                          Un firmante puede tener varias firmas
     *                                          en un mismo documento
     */
    public function signs(): HasMany
    {
        return $this->hasMany(Sign::class);
    }

    /**
     * Obtiene las cajas de texto de un firmante
     *
     * @return HasMany                          Un firmante puede tener varias cajas de texto
     *                                          en un mismo documento
     */
    public function boxs(): HasMany
    {
        return $this->hasMany(Textbox::class);
    }

    /**
     * Obtiene las grabaciones de audio del firmante
     *
     * @return HasMany                          Un documento puede tener varias grabaciones de audio asociadas
     *                                          a un mismo firmante
     */
    public function audios(): HasMany
    {
        return $this->hasMany(Audio::class);
    }

    /**
     * Obtiene las grabaciones de video del firmante
     *
     * @return HasMany                          Un documento puede tener varias grabaciones de video asociadas
     *                                          a un mismo firmante
     */
    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    /**
     * Obtiene las capturas de pantalla del firmante
     *
     * @return HasMany                          Un documento puede tener varias capturas de pantalla asociadas
     *                                          a un mismo firmante
     */
    public function captures(): HasMany
    {
        return $this->hasMany(Capture::class);
    }

    /**
     * Obtiene los documentos acreditativos del firmante
     *
     * @return HasMany                          Un documento puede tener varios documentos acreditativos asociados
     *                                          a un mismo firmante
     */
    public function passports(): HasMany
    {
        return $this->hasMany(Passport::class);
    }

    /**
     * Obtiene el formulario de datos a ser validado por medio del documento
     *
     * @return HasMany                           Un de formulario de datos requerido
     */
    public function formdata(): HasMany
    {
        return $this->hasMany(FormData::class);
    }

    /**
     * Obtiene las verificación de datos asignadas para ser validadas fuera del documento
     *
     * @return BelongsTo|null                           Un de formulario de datos o
     *                                                vacio si no posee alguno
     */
    public function verificationForm(): ?BelongsTo
    {
        return $this->belongsTo(VerificationForm::class);
    }

    /**
     * Obtiene el documento que se va a firmar o validar
     *
     * @return BelongsTo                        El documento relacionado
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    /**
     * Obtiene el proceso perteneciente
     *
     * @return MorphOne                     El proceso relacionado
     */
    public function process() : MorphOne
    {
        return $this->morphOne(Process::class, 'statable');
    }

    /**
     * Obtiene las validaciones que debe realizar el firmante para aprobar el documento
     *
     * @return Collection|null                  Una lista de validaciones o
     *                                          null si no hay documento y no hay, por tanto, validaciones a realizar
     */
    public function validations(): ?Collection
    {
        if ($this->document) {
            return Validation::where('user', $this->id)
                ->where('document_id', $this->document->id)
                ->get();
        } else {
            return null;
        }
    }

    /**
     * Comprueba si el firmante ha completado las validaciones que se le han propuesto
     *
     * @return bool                             true si se han completado todas las validaciones propuestas
     *                                          false en caso contrario
     */
    public function hasDoneAllValidations(): bool
    {
        // Comprueba el número de valdidaciones que deben ser realizadas
        // con el número de valdiaciones que se han realizado
        return  $this->validations()->count() ==
                $this->validations()->filter(
                    fn ($validation) => $validation->process && $validation->process->isDone()
                )->count();
    }

    /**
     * Comprueba si el firmante tiene validaciones pendientes
     *
     * @return bool                             true si tiene al menos 1 validación pendiente
     *                                          false en caso contrario
     */
    public function hasPendingValidations(): bool
    {
        // Comprueba si al menos hay 1 validación pendiente
        // No se tienen en cuenta las validaciones por captura de pantalla porque estas
        // son adicionales a otras validaciones (hasta el momento de la firma manuscrita)
        return $this->validations()
            ->filter(
                fn ($validation) => $validation->validation !== \App\Enums\ValidationType::SCREEN_CAPTURE_VERIFICATION
                &&
                $validation->process->isPending()
            )->count() > 0;
    }

    /**
     * Comprueba si el firmante tiene validaciones canceladas
     *
     * @return bool                             true si tiene al menos 1 validación cancelada
     *                                          false en caso contrario
     */
    public function hasCanceledValidations(): bool
    {
        // Comprueba si al menos hay 1 validación cancelada
        return $this->validations()->filter(fn ($validation) => $validation->process->isCanceled())->count() > 0;
    }

    /**
     * Obtiene las visitas que ha realizado el firmante
     *
     * @return HasMany                          Un firmante puede hacer varias visitas
     *                                          para realizar las firmas requeridas y las validaciones
     *                                          asociadas a esta
     */
    public function visits(): HasMany
    {
        return $this->hasMany(SignerVisit::class);
    }

    /**
     * Obtiene la solicitud de documentos que debe realizar el usuario "firmante"
     *
     * @return DocumentRequest|null             La solicitud de documentos
     *                                          o null si no hay ninguna
     */
    public function request(): ?DocumentRequest
    {
        return $this->document_request_id ?
             DocumentRequest::where('id', $this->document_request_id)->first() : null;
    }

    /**
     * Los archivos de una solicitud de documentos
     *
     * @return HasMany|null                 Los archivos de una solicitud de documentos
     *                                      o null si tal solicitud no existe
     */
    public function requestFiles(): ?HasMany
    {
        return  $this->document_request_id ?
            $this->hasMany(DocumentRequestFile::class) : null;
    }

    /**
     * Comprueba si el usuario "firmante" ha respondido o no a la solicitud de documentos
     *
     * @param Signer $signer                    El usuario
     *
     * @return bool                             true si la solicitud ha sido contestada
     *                                          false en caso contrario
     */
    public function requestIsDone(): bool
    {
        // Obtiene los archivos de la solictud de documentos (si existe)
        $files = $this->requestFiles;

        // Comprueba si el usuario "firmante" ha adjuntado archivos a la solicitud o no
        return $files ?
            $files->filter(fn ($signer) => $signer->signer_id == $this->id)->count() > 0
                                            :
            false;
    }

     /**
     * Comprueba si el usuario "firmante" ha respondido o no a la verificación de datos
     *
     * @param Signer $signer                    El usuario
     *
     * @return bool                             true si la verificación ha sido contestada
     *                                          false en caso contrario
     */
    public function verificationFormIsDone(): bool
    {
        $status =  false;

        // si existe alguna verificación de datos asignada
        if ($this->verificationForm) {
            // comprabar si fue realizada o cancelada
            if ($this->verificationform_at) {
                $status = true;
            }
        }

        return $status;
    }

    /**
     * Si el firmante debe realizar una validación determinada sobre un documento o no
     *
     * @param Document $document                El documento
     * @param int      $validation              La validación a realizar
     *
     * @return bool                             true si el firmante debe realizar la validación
     *                                          false en caso contrario
     */
    public function mustValidate(Document $document, int $validation): bool
    {
        return Validation::where('user', $this->id)
            ->where('document_id', $document->id)
            ->where('validation', $validation)
            ->count() != 0;
    }
    
    /**
     * Registra una visita del firmante
     *
     * @return SignerVisit                      La vista del firmante
     */
    public function registerVisit(): SignerVisit
    {
        return $this->visits()->create(
            [
                'document_id' => $this->document->id ?? null,               // El documento visitado
                'request'     => request()->route()->getName(),             // La ruta visitada
                'ip'          => request()->ip(),                           // La dirección IP
                'user_agent'  => request()->server('HTTP_USER_AGENT'),      // El agente de usuario
                'starts_at'   => now(),                                     // El momento de inicio de la visita
                'device'      => $this->getDevice(),                        // El dispositivo
            ]
        );
    }

    /**
     * Obtiene el firmante por su token
     *
     * @param string $token                     El token
     *
     * @return Signer                           Un firmante
     * @throws ModelNotFoundException           No existe el firmante
     */
    public static function findByToken(string $token): Signer
    {
        return Signer::where('token', $token)->firstOrFail();
    }

    /**
     * Cancela el proceso de firma, validación o solicitud de documento del firmante
     *
     * @param string|null                       Un motivo opcional para la cancelación del proceso
     *
     * @return void
     */
    public function cancel(?string $subject): void
    {
        $this->canceled_subject = $subject;
        $this->canceled_at      = now();
        $this->save();
    }

    /**
     * Comprueba si el proceso de firma, validación o solicitud de documento del firmante
     * ha sido cancelado
     *
     * @return bool                             true si el proceso de firma ha sido cancelado
     *                                          false si está vigente
     */
    public function hasBeenCanceled(): bool
    {
        return $this->canceled_at != null;
    }

    /**
     * Comentarios adjuntados a la solicitud de documentos de un signer
     *
     * @return HasMany                          Los comentarios a la solicitud de un signer
     */
    public function signerComments(): HasMany
    {
        return $this->hasMany(WorkspaceComment::class);
    }

    /**
     * Establece los procesos de las validaciones del firmante como inactivas
     *
     * @return void
     */
    public function markValidationsAsInactive(): void
    {
        // Si estoy validando un documento
        if ($this->validations()) {
            $this->validations()->each(
                fn ($validation) => $validation->markAsInactive()
            );
        } else {
            // Si estoy aportando documentos a una solicitud
            if ($this->document_request_id && !$this->document_id) {
                // Marco al firmante como inactivo
                $this->markAsInactive();
            }
        }
        // Marco al firmante como inactivo
        $this->markAsInactive();
    }

    /**
     * Establece al firmante como que NO se está atendiendo una solicitud de documentos
     *
     * @return void
     */
    public function markAsInactive() : void
    {
        // Si no existe el proceso relacionado con el firmante lo creo
        if (!$this->process) {
            $this->process = $this->process()->create([]);
        }
        
        // Marcamos mi proceso de solicitud como inactivo
        $this->process->markAsInactive();

        // Si estoy en una solicitud de documentos
        if ($this->request()) {
            // Si no existe el proceso de la solicitud de documento lo creo
            if (!$this->request()->process) {
                $this->request()->process = $this->request()->process()->create([]);
            }

            // Actualizamos la actividad en la solicitud documento
            $this->request()->removeActivity($this);
        }

        // si existe una verificación de datos pendiente
        if ($this->verificationForm) {
            // crear si no existe el proceso de para verificación
            if (!$this->verificationForm->process) {
                $this->verificationForm->process = $this->verificationForm->process()->create([]);
            }

            // se actualiza la actividad para la verificación de datos
            $this->verificationForm->process->removeVerificationFormActivity($this);
        }
    }

    /**
     * Establece al firmante como que está atendiendo su solicitud de documentos
     *
     * @return void
     */
    public function markAsActive() : void
    {
        // Si no existe el proceso relacionado con el firmante lo creo
        if (!$this->process) {
            $this->process = $this->process()->create([]);
        }

        // Marcamos el proceso de solicitud como activo
        $this->process->markAsActive();

        // verifica si existe una solicitud de documentos
        if ($this->request()) {
            // Si no existe el proceso de la solicitud de documento lo creo
            $this->request()->process or $this->request()->process()->create([]);

            // Actualizamos la actividad en la solicitud documento
            $this->request()->process->addRequestActivity($this);
        }

        // verifica si existe una verificación de datos
        if ($this->verificationForm) {
            // Si no existe el proceso de la verificación de datos se crea
            $this->verificationForm->process or $this->verificationForm->process->create([]);

            // Actualizamos la actividad en la verificación de datos
            $this->verificationForm->process->addVerificationFormActivity($this);
        }
    }

    /**
     * Establece al firmante como active en su workspace
     *
     * @return void
     */
    public function activate() : void
    {
        $this->active = 1;
        $this->save();
    }

    /**
     * Establece al firmante como inactive en su workspace
     *
     * @return void
     */
    public function deactivate() : void
    {
        $this->active = 0;
        $this->save();
    }

    /**
     * Obtiene el grado de progreso de firma y validación del firmante
     *
     * Si n es el número de validaciones a realizar por el firmante y m el número de validaciones efectuadas,
     * el grado de progreso se define como:
     *
     * r = m · 100 / n
     *
     * @return int                              El grado de progreso del firmante en tanto por ciento
     */
    public function getProgressAttribute(): int
    {
        try {
            return intval(
                $this->validations()->filter(
                    fn ($validation) => $validation->process->isDone()
                )->count() * 100
                    /
                    $this->validations()->count()
            );
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Si el firmante debe validarse con una grabación de pantalla
     *
     * @return bool                             true si el firmante debe ser validado con una
     *                                          grabación de pantalla
     */
    public function mustBeValidateByScreenCapture(): bool
    {
        return in_array(
            \App\Enums\ValidationType::SCREEN_CAPTURE_VERIFICATION,
            $this->validations()->pluck('validation')->toArray()
        );
    }

    /**
     * Marcar la verificación de datos como realizada e inidcando
     * la fecha de realizacion
     *
     * @return void
     */
    public function markVerificationFormDone(): void
    {
        $this->verificationform_at = Carbon::now();
        $this->save();
    }

    /**
     * Comprobar si es valida la peticion para realizar un proceso de verificación de datos
     * sea guardar, ver informacion
     *
     * @return boolean                  si es valida la peticion o no
     */
    public function requestIsValidForVerificationForm(): bool
    {
        return !$this->verificationForm || $this->verificationFormIsDone() || $this->process->isCanceled();
    }

    /**
     * Obtener nombre completo del firmante
     *
     * @return string|null          El nombre o vacio si no posee datos registrados
     */
    public function getFullNameAttribute(): ?string
    {
        return $this->name ? $this->name.' '.$this->lastname : $this->email;
    }

    /**
     * Obtiene el comentario para un proceso de validacion especifico
     *
     * @param [type] $validationType            El proceso de validacion a evaluar
     * @return string|null                      El comentario o null si no existe
     */
    public function getIfCommentExists($validationType) : ?string
    {
        // el comentario
        $comment = null;

        // El proceso de validacion a evaluar
        $existingValidation =
            $this->validations()->filter(fn($validation) => $validation->validation == $validationType)->first();

        // si existe el proceso
        if ($existingValidation) {
            // si existe el comentario sino retorna un valor null
            $comment = $existingValidation->feedback ? $existingValidation->feedback->comment : null;
        }

        return $comment;
    }

    /**
     * Devuelve el proceso al que debe responder el firmante
     *
     * O a un proceso de validaciones
     * O a un proceso de solicitud de documentos
     * O a un proceso de certificación de formulario
     *
     * @return int
     */
    public function signerProcess(): ?int
    {
        // Es un proceso de validación cuando
        // tengo un documento y no tengo más nada y cuando
        // tengo un documento y un document request
        if (($this->document_id && !$this->document_request_id && !$this->verification_form_id)
            ||
            ($this->document_id && $this->document_request_id && !$this->verification_form_id)
        ) {
            return SignerProcesType::VALIDATION_PROCESS;
        } elseif (!$this->document_id && $this->document_request_id && !$this->verification_form_id) {
            return SignerProcesType::REQUEST_PROCESS;
        } elseif (!$this->document_id && !$this->document_request_id && $this->verification_form_id) {
            return SignerProcesType::FORM_PROCESS;
        } else {
            return null;                      // No se encontró ningún proceso
        }
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
