<?php

/**
 * Modelo Usuario
 *
 * Se trata de un usuario de la aplicación, que puede estar
 * registrado o no (actuar como usuario invitado)
 * Cada usuario se asocia con una subscripción a un plan de negocio concreto
 *
 * @author    javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cookie;

use App\Models\Contact;
use App\Models\Document;
use App\Models\File;
use App\Models\FileSharing;
use App\Models\Plan;

use App\Models\Traits\Subscribable;

use Fikrea\DiskSpace;

use App\Enums\Role;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    use Notifiable, HasFactory, Subscribable;

    /**
     * Lista de atributos del usuario
     *
     * @var array
     */
    protected $fillable =
        [
            'type',                 // El tipo de usuario (tipo de cuenta de usuario)
            // Puede ser una cuenta personal o una cuenta de empresa
            // @see \App\Enums\UserType
            'name',                 // El nombre
            'lastname',             // Los apellidos
            'email',                // La dirección de correo (utilizada en el login)
            'email_verified_at',    // La fecha en la que la cuenta de usuario (dirección de correo) ha sido verificada
            'password',             // La contraseña
            'validation_code',      // El código de validación
            'address',              // La dirección postal
            'phone',                // El teléfono de contacto
            'city',                 // La localidad/ciudad
            'province',             // La provincia/región/estado
            'country',              // El país
            'company',              // El nombre de la empresa (en una cuenta de empresa)
            'position',             // El cargo del usuario (en una cuenta de empresa)
            'locale',               // El idioma del usuario
            'custom_disk_space',    // Espacio de disco personalizado para la cuenta en Mb
            // Normalmente el espacio de disco para el usuario es el valor
            // establecido en su plan de subscripción, pero aqui podemos
            // definir cualquier otro valor
            'active',               // Si el usuario está activo o no
            'code_postal',          // El codigo postal
            'dial_code',            // Codigo prefijo del pais
        ];

    /**
     * Atributos ocultos
     *
     * @var array
     */
    protected $hidden =
        [
            'password',             // La contraseña de acceso
            'remember_token',       // El token de recuperación de la contraseña
            'guest_token',          // El token del usuario invitado
        ];

    /**
     * Conversión de tipos
     *
     * @var array
     */
    protected $casts =
        [
            'email_verified_at' => 'datetime',
            'active' => 'boolean',
        ];

    /**
     * Obtiene el nombre completo del usuario
     *
     * @return string                           El nombre conmpleto
     */
    public function getFullNameUser($delimiter = null): string
    {
        return $delimiter ? "{$this->name}$delimiter{$this->lastname}"  : "{$this->name} {$this->lastname}";
    }

    /**
     * Obtiene la configuración del usuario
     *
     * @return StdClass                         Un objeto configuración
     *
     * @example
     *
     * $user = Auth::user();
     *
     * $audioText = $user->config->audio->text;
     *
     * Obtiene el texto que se debe leer, como referencia, durante una locución en un validación
     * mediante grabación de audio
     *
     */
    public function getConfigAttribute(): \StdClass
    {
        // Obtiene la configuración del usuario decodificando el JSON
        $config = json_decode($this->attributes['config']);

        // Si no hay configuración previa, se establecen valores por defecto
        if (!$config) {
            $config = json_decode(
                json_encode(
                    [
                        'sign' =>
                            [
                                'sign' => null,
                                'useAsDefault' => false,
                            ],
                        'audio' =>
                            [
                                'text' => config('validations.audio.text'),   // El texto que sirve de apoyo
                                // o guión a la grabación
                                'sample' => null,                               // El ejemplo de audio
                            ],
                        'video' =>
                            [
                                'text' => config('validations.video.text'),   // El texto que sirve de apoyo
                                // o guión a la grabación
                                'sample' => null,                               // El ejemplo de video
                            ],

                        'identificationDocument' =>
                            [
                                'useFacialRecognition' => true,
                            ],
                        'notification' =>
                            [
                                'days' => 1,
                                'receive' => true,
                            ]
                    ]
                )
            );
        }

        // Valores por defecto para la config de audio
        $config->audio->sample ??= null;

        // Valores por defecto para la config de video
        $config->video->sample ??= null;

        // Valores por defecto de configuración de notificaciones
        // si no se han establecido con anterioridad
        $config->notification->days ??= 1;
        $config->notification->receive ??= true;

        return $config;
    }

    /**
     * Obtiene el espacio de disco utilizado y disponible para el usuario
     *
     * @return DiskSpace                        El espacio ocupado por los archivos del usuario en bytes
     */
    public function getDiskSpaceAttribute(): DiskSpace
    {
        // Obtiene el espacio disponible por la subscripción actual en bytes
        // Si el usuario tiene un espacio de disco personalizado se toma ese valor,
        // en caso contrario, se tome el valo establecido en su plan de subscripción
        // El espacio suado se mide en Mb, 1 Mb = 2**20 bytes
        $availableSpace = ($this->custom_disk_space ?? $this->subscription->plan->disk_space) * pow(2, 20);

        // Contabiliza el espacio ocupado por los archivos y documentos subidos
        $usedByFiles = $this->files(null, false)->sum('size') + $this->lockedFiles()->sum('size');
        $usedByDocuments = $this->documents->sum('size') + $this->documents->sum('converted');
        $usedByUploads =
            $this->audios->sum('size') + $this->videos->sum('size') + $this->passports->sum('size') +
            $this->required()->sum('size');

        // Calcula el espacio total usado
        $usedSpace = $usedByFiles + $usedByDocuments + $usedByUploads;

        // Si los archivos a firmar no son documentos PDF se require un espacio de disco adicional
        // para los archivo resultantes de la conversión

        // Obtiene el espacio libre
        $freeSpace = $availableSpace - $usedSpace;

        return new DiskSpace(
            [
                'available' => $availableSpace,
                'used' => $usedSpace,
                'usedByFiles' => $usedByFiles,
                'usedByDocuments' => $usedByDocuments,
                'usedByUploads' => $usedByUploads,
                'free' => $freeSpace > 0 ? $freeSpace : 0,
            ]
        );
    }

    /**
     * Retorna la cantidad de ficheros regulares (que no son carpetas) y que no se encuentran en estado bloqueado para
     * este usuario.
     *
     * @return int
     */
    public function getRegularFilesCountAttribute(): int
    {
        return $this->hasMany(File::class)->where('is_folder', false)->where('locked', false)->count();
    }

    /**
     * Retorna la cantidad de ficheros bloqueados para este usuario.
     *
     * @return int
     */
    public function getLockedFilesCountAttribute(): int
    {
        return $this->hasMany(File::class)->where('locked', true)->count();
    }

    /**
     * Obtiene el usuario cuyo token de usuario invitado es el dado
     *
     * @param string $token El token del usuario invitado
     *
     * @return self                             El usuario
     * @throws ModelNotFoundException           El usuario no existe
     */
    public static function findByGuestToken(string $token): self
    {
        return User::where('guest_token', '=', $token)->firstOrFail();
    }

    /**
     * Obtiene los datos de facturación del usuario
     *
     * La cuenta de usuario se factura a una compañía
     * Los datos de la compañía se aplican únicamente con objeto de poder efectuar la facturación de la cuenta,
     * es decir, que la aplicación emita una factura por el uso que el usuario hace de la aplicación
     *
     * @return HasOne                           Un usuario se corresponde con unos datos de facturación
     */
    public function billing(): HasOne
    {
        return $this->hasOne(Company::class);
    }

    /**
     * Obtiene la subscripción de un usuario
     *
     * Cada usuario posee una susbcripción gratuita o de padgo
     *
     * @return HasOne                           Un usuario sólo posee una subscripción
     */
    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class);
    }

    /**
     * Obtiene los pedidos de un usuario
     *
     * Cada renovación de la subscripción genera un pedido
     *
     * @return HasMany                          Un usuario puede tener varios pedidos
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->orderBy('created_at', 'desc');
    }

    /**
     * Obtiene los contactos de un usuario
     *
     * Los contactos son personas que el usuario tiene guardados en su agenda personal
     * ordenados por el apellido
     *
     * @return HasMany                          Un usuario puede tener varios contactos
     */
    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class)->orderBy('lastname');
    }

    /**
     * Obtiene los documentos de un usuario
     * ordenados por fecha de creación descendente
     *
     * @return HasMany                          Un usuario puede tener varios documentos
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class)->orderBy('created_at', 'desc');
    }

    /**
     * Obtiene las solicitudes de documentos de un usuario
     * ordenados por fecha de creación descendente
     *
     * @return HasMany                          Un usuario puede tener varias solicitudes de documentos
     */
    public function documentRequests(): HasMany
    {
        return $this->hasMany(DocumentRequest::class)->orderBy('created_at', 'desc');
    }

    /**
     * Obtiene las verificaciónes de datos que el usuario ha realizado
     * como proceso independiente fuera del documento
     *
     * @return HasMany                          Las verificaciónes de datos
     */
    public function verificationForm(): HasMany
    {
        return $this->hasMany(VerificationForm::class)->orderBy('created_at', 'desc');
    }

    /**
     * Obtiene todos los eventos del usuario que ha publicado
     *
     * @return HasMany          Los eventos del usuario
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class)->orderBy('created_at', 'desc');
    }

    /**
     * Devuelve todas las plantillas de formulario de datos del usuario
     *
     * @return HasMany                          las plantillas del usuario
     */
    public function formTemplate(): HasMany
    {
        return $this->hasMany(FormTemplate::class);
    }

    /**
     * Obtiene los sellos de un usuario
     *
     * @return HasMany                          Un usuario puede tener varios sellos
     */
    public function stamps(): HasMany
    {
        return $this->hasMany(Stamp::class);
    }

    /**
     * Obtiene las solicitudes de documentos de un usuario
     * que tienen usuarios "firmantes" asignados
     *
     * @return Builder                          Una lista de solicitudes de documentos
     */
    public function findDocumentRequests(): Builder
    {
        return DocumentRequest::findByUser($this);
    }

    /**
     * Devuelve los documentos que ha compartido el usuario fuera del proceso de firma
     *
     * @return object|null          un objeto con las comparticiones o null
     */
    public function documentSharing() : ?object
    {
        return $this->documents->map(fn ($document) => $document->getDocumentSharingWithToken())->flatten() ?? [];
    }

    /**
     * Contar la cantidad de documentos compartidos por el usuario
     *
     * @return int                  La cantidad de documentos compartidos
     */
    public function getCountDocumentSharing() : int
    {
        return $this->documentSharing()->isNotEmpty() ? $this->documentSharing()->count() : 0;
    }

    /**
     * Obtiene los archivos subidos por un usuario en una determinada carpeta o en la raíz ordenados por fecha de
     * creación descendente
     *
     * @param int|null $id              ID de la carpeta que contiene los archivos; nulo, si es la raíz
     * @param bool     $folderFiltering Verdadero si únicamente se retornan los archivos
     *                                  de la carpeta indicada; falso, en caso contrario
     * @return HasMany                          Un usuario puede tener varios archivos
     */
    public function files(int $id = null, bool $folderFiltering = true): HasMany
    {
        $files = $this->hasMany(File::class);

        // Limitar los archivos retornados a los que están contenidos en la carpeta indicada
        // (comportamiento predeterminado);
        // o en caso contrario, retornar todos los ficheros del usuario (no filtrar por carpeta).
        if ($folderFiltering) {
            $files->where('parent_id', $id);
        }

        // Excluir los ficheros bloqueados
        $files->where('locked', false);

        return $files->orderBy('is_folder', 'desc')->orderBy('created_at', 'desc');
    }

    /**
     * Obtiene los archivos subidos por un usuario en estado bloqueado
     *
     * @return HasMany  Un usuario puede tener varios archivos
     */
    public function lockedFiles(): HasMany
    {
        $files = $this->hasMany(File::class);

        // Únicamente los ficheros bloqueados
        $files->where('locked', true);

        return $files->orderBy('is_folder', 'desc')->orderBy('created_at', 'asc');
    }

    /**
     * Los archivos que ha requerido a otros usuarios
     * a través de solicitudes de documentos
     *
     * @return HasMany                          Un usuario puede haber requerido varios archivos
     */
    public function required(): HasMany
    {
        return $this->hasMany(DocumentRequestFile::class);
    }

    /**
     * Obtiene las comparticiones o conjuntos de archivos compartidos por el usuario
     * con uno o má destinatarios
     *
     * @return HasMany                          Un usuario puede tener varias comparticiones o FileSharings.
     *                                          Cada compartición se compone de un conjunto de archivos
     *                                          y de un conjunto de usuarios con los cuales se comparte el conjunto
     */
    public function fileSharings(): HasMany
    {
        return $this->hasMany(FileSharing::class)->orderBy('created_at', 'desc');
    }

    /**
     * Obtiene los archivos de audio
     *
     * @return HasMany                          Un usuario puede ser propietario de varios archivos de audio
     */
    public function audios(): HasMany
    {
        return $this->hasMany(Audio::class);
    }

    /**
     * Obtiene los archivos de video
     *
     * @return HasMany                          Un usuario puede ser propietario de varios archivos de video
     */
    public function videos(): HasMany
    {
        return $this->hasMany(Audio::class);
    }

    /**
     * Obtiene los documentos identificativos
     *
     * @return HasMany                          Un usuario puede ser propietario de varios documentos identificativos
     */
    public function passports(): HasMany
    {
        return $this->hasMany(Passport::class);
    }

    /**
     * Obtiene los documentos enviados por un usuario
     *
     * @return HasMany                          Un usuario puede tener varios documentos enviados
     */
    public function sents(): HasMany
    {
        return $this->hasMany(Document::class)->orderBy('created_at');
    }

    /**
     * Obtiene las notificaciones de un usuario
     *
     * @return HasMany                          Un usuario puede tener varias notificaciones
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    /**
     * Obtiene los usuarios registrados
     *
     * @return Builder                          Los usuarios que han completado su registro
     */
    public static function registered(): Builder
    {
        return User::where('email_verified_at', '!=', null);
    }

    /**
     * Obtiene los usuarios clientes
     *
     * @return Builder                          Los usuarios que poseen un plan de susbscripción
     *                                          de pago
     */
    public static function clients(): Builder
    {
        /**
         * Los clientes son usuarios cuya susbscripción cumple con que
         * 1) NO ES EL PLAN FREE
         * 2) NO ES EL PLAN FIKREA
         */
        return User::whereHas(
            'subscription',
            fn($query) => $query->where('plan_id', '!=', Plan::TRIAL)
                                ->where('plan_id', '!=', Plan::FIKREA)
        )->where('active', true);
    }

    /**
     * Obtiene un usuario por su dirección de correo
     *
     * @param string $email La dirección de correo
     *
     * @return self                             El usuario
     * @throws ModelNotFoundException           El usuario no existe
     */
    public static function getUserByEmail(string $email): self
    {
        return User::where('email', $email)->firstOrFail();
    }

    /**
     * Obtiene un usuario por su código de validación
     *
     * @param string $validationCode El código de validación del usuario
     *
     * @return self                             El usuario
     * @throws ModelNotFoundException           El usuario no existe
     */
    public static function getUserByValidationCode(string $validationCode): self
    {
        return User::where('validation_code', $validationCode)->firstOrFail();
    }

    /**
     * Verifica la cuenta de usuario
     *
     * @return bool                             true si el proceso se ha realizado con éxito
     *                                          false en caso contrario
     */
    public function verifyAccount(): bool
    {
        // Elimina el código de validación para evitar que sea reutilizado
        $this->validation_code = null;

        // Fija el momento en que la cuenta ha sido verificada
        $this->email_verified_at = new \DateTime;

        // Guarda el usuario
        return $this->save();
    }

    /**
     * Desactiva un usuario
     *
     * @return bool                             true si el usuario se ha desactivado con éxito
     *                                          false en caso contratio
     */
    public function disable(): bool
    {
        $this->active = false;
        return $this->save();
    }

    /**
     * Actuva un usuario
     *
     * @return bool                             true si el usuario ha sido activado con éxito
     *                                          false en caso contratio
     */
    public function enable(): bool
    {
        $this->active = true;
        return $this->save();
    }

    /**
     * Cambia la contraseña de un usuario
     *
     * @param string $password La contraseña
     *
     * @return bool                             true si se ha guardado el usuario con éxito
     */
    public function changePassword(string $password): bool
    {
        // Cambia la contraseña del usuario
        $this->password = Hash::make($password);

        // Elimina el token de recuerdo de contraseña para evitar su reutilización
        $this->remember_token = null;

        // Si la cuenta no estuviese validada con anterioridad, se valida
        // Por ejemplo, el usuario ha utilizado la aplicación como usuario invitado
        // y ahora quiere hacer su cuenta definitiva
        $this->email_verified_at = new \DateTime;

        // Guarda la nueva contraseña
        return $this->save();
    }

    /**
     * Crea el token o código de validación que permite resetar la contraseña
     *
     * @param User $user El usuario
     *
     * @return void
     */
    protected static function createValidationToken(User &$user): void
    {
        $user->validation_code = Str::random(64);

        $user->save();
    }

    /**
     * Si el usuario, autor de un documento, debe realizar una validación determinada sobre un documento o no
     *
     * @param Document $document   El documento
     * @param int      $validation La validación a realizar
     *
     * @return bool                             true si el firmamente debe realizar la validación
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
     * Comprueba si el usuario posee el rol de administrador
     *
     * @return bool                             true si es administrador
     *                                          false en caso contrario
     */
    public function isAdmin(): bool
    {
        return $this->role == Role::ADMIN;
    }

    /**
     * Comprueba si el usuario no posee el rol de administrador
     *
     * @return bool                             true si no es administrador
     *                                          false en caso contrario
     */
    public function isNotAdmin(): bool
    {
        return $this->role != Role::ADMIN;
    }

    /**
     * Comprueba si el usuario es cliente
     *
     * Un cliente es un usuario con una subscripción que no es gratuita
     *
     * @return bool                             true si el usuario es cliente
     *                                          false en caso contrario
     */
    public function isClient(): bool
    {
        return !$this->subscription->plan->isTrial();
    }

    /**
     * Comprueba si el usuario no es cliente
     *
     * Un cliente es un usuario que disfruta de la subscripción gratuita
     *
     * @return bool                             true si el usuario es no cliente
     *                                          false en caso contrario
     */
    public function isNotClient(): bool
    {
        return $this->subscription->plan->isTrial();
    }

    /**
     * Comprueba si el usuario invitado ha cambiado su perfil
     *
     * @return bool                             true si el usuario ha cambiado su perfil
     *                                          false en caso contrario
     */
    public function guestHasChangedProfile(): bool
    {
        // Si el usuario invitado ha cambiado su perfil por defecto, la cookie "user-has-changed-profile"
        // está establecida al valor "true"
        return Cookie::get('user-has-changed-profile') == 'true';
    }

    /**
     * Devuelve la estructura de carpetas para archivos definida por el usuario
     *
     * @return array                    El array con la estructura de carpetas
     */
    public function getFoldersStructure() : array
    {
        // Retornar las carpetas en el orden indicado, para mostrar una estructura de árbol en el componente SELECT
        $folders = [];

        // la busqueda por el usuario ordenada por el nombre
        $rootFolders = File::where('user_id', $this->id)->where('is_folder', true)
            ->whereNull('parent_id')->orderBy('name')->get();

        // carpeta root, incluir todas las carpetas al array
        foreach ($rootFolders as $rootFolder) {
            $nestedFolders = $this->getNestedFolders($rootFolder)->map->only(
                ['id', 'name', 'parent_id', 'full_path']
            )->toArray();

            foreach ($nestedFolders as $item) {
                $folders[] = (object)$item;
            }
        }

        return $folders;
    }

    /**
     * Retorna todas las carpetas contenidas en una carpeta, descendiendo de manera recursiva todos los posibles niveles
     *
     * @param File            $folder        La carpeta a partir de la cual se inicia la búsqueda
     * @param int             $level         El nivel por el que se navega (parámetro uso interno de recursión)
     * @param Collection|null $nestedFolders Los ficheros anidados (parámetro uso interno de recursión)
     * @return Collection                    La colección de carpetas anidados
     * @throws FileIsNotAFolderException
     */
    public function getNestedFolders(File $folder, int $level = 0, Collection $nestedFolders = null): Collection
    {
        // El nivel 0 es la carpeta que se indicó borrar
        if (0 === $level) {
            // Crear la colección inicializada con el primer fichero(carpeta) que se indicó borrar
            // No es necesario hacerlo en los niveles subsiguientes porque todos los ficheros(carpetas) en el árbol
            // descendente será incluido en la colección de ficheros a borrar
            $nestedFolders = collect([$folder]);
        }

        foreach ($folder->files()->where('is_folder', true)->get() as /** @var File */ $nestedFolder) {
            // Incluir este fichero en la colección
            $nestedFolders->add($nestedFolder);

            // Si es una carpeta, hacer llamada recursiva para incluir a continuación todos los ficheros/carpetas
            // que contiene
            $this->getNestedFolders($nestedFolder, $level + 1, $nestedFolders);
        }

        return $nestedFolders;
    }

    /**
     * Obtiene la relación con Screen
     *
     * Cada usuario puede tener muchas grabaciones de pantalla
     *
     * @return HasMany                           Las grabaciones guardadas del usuario
     */
    public function screens(): HasMany
    {
        return $this->hasMany(Screen::class)
            ->where('saved', true)
            ->orderBy('created_at', 'desc');
    }
}
