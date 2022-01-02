<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Nombre de la aplicación
    |--------------------------------------------------------------------------
    | Veasé .env para fijar un valor para cada entorno
    */
    'name'              => env('APP_NAME', 'Fikrea'),

    /*
    |--------------------------------------------------------------------------
    | Dirección de contacto del administrador de la aplicación
    |--------------------------------------------------------------------------
    | Veasé .env para fijar un valor para cada entorno
    */
    'contact'           => env('APP_ADMIN', 'info@retailexternal.com'),

    /*
    |--------------------------------------------------------------------------
    | Dirección desde la que se envían los correos de la aplicación
    |--------------------------------------------------------------------------
    |
    */
    'app_mail'          => env('APP_MAIL', 'info@fikrea.com'),

    /*
    |--------------------------------------------------------------------------
    | Entorno de la aplicación
    |--------------------------------------------------------------------------
    | Veasé .env para fijar un valor para cata entorno
    | En producción es production
    */
    'env'               => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Depuración
    |--------------------------------------------------------------------------
    | Veasé .env para fijar un valor para cata entorno
    | En producción es false
    */
    'debug'             => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | NLa dirección URL
    |--------------------------------------------------------------------------
    |
    */
    'url'               => env('APP_URL', 'http://localhost'),
    'asset_url'         => env('ASSET_URL', null),

    /*
    |--------------------------------------------------------------------------
    | Zona horaria
    |--------------------------------------------------------------------------
    |
    */
    'timezone'          => env('APP_TIMEZONE', 'Europe/Madrid'),

    /*
    |--------------------------------------------------------------------------
    | Moneda de uso
    |--------------------------------------------------------------------------
    |
    */
    'currency'          => env('APP_CURRENCY', 'EUR'),

    /*
    |--------------------------------------------------------------------------
    | Configuración local
    |--------------------------------------------------------------------------
    |
    */
    'locale'            => 'es',
    'fallback_locale'   => 'es',
    'faker_locale'      => 'es_ES',

    /*
    |--------------------------------------------------------------------------
    | Clave de la aplicación
    |--------------------------------------------------------------------------
    |
    */
    'key'               => env('APP_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Algortimo de cifrado de la aplicación
    |--------------------------------------------------------------------------
    |
    */
    'cipher'            => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | The service providers listed here will be automatically loaded on the
    | request to your application. Feel free to add your own services to
    | this array to grant expanded functionality to your applications.
    |
    */

    'providers' => [

        /*
         * Laravel Framework Service Providers...
         */
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Collective\Html\HtmlServiceProvider::class,

        /*
         * Package Service Providers...
         */

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventSubscriptionServiceProvider::class,
        App\Providers\EventLogingServiceProvider::class,
        App\Providers\EventSignatureServiceProvider::class,
        App\Providers\EventDocumentRequestServiceProvider::class,
        App\Providers\CustomRouteServiceProvider::class,
        App\Providers\BladeDirectivesServiceProvider::class,
        App\Providers\ViewSharedServiceProvider::class,

        // provider para la los evento que emite la verificación de datos
        App\Providers\EventVerificationFormProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | This array of class aliases will be registered when this application
    | is started. However, feel free to register as many as you wish as
    | the aliases are "lazy" loaded so they don't hinder performance.
    |
    */

    'aliases' => [

        'App' => Illuminate\Support\Facades\App::class,
        'Arr' => Illuminate\Support\Arr::class,
        'Artisan' => Illuminate\Support\Facades\Artisan::class,
        'Auth' => Illuminate\Support\Facades\Auth::class,
        'Blade' => Illuminate\Support\Facades\Blade::class,
        'Broadcast' => Illuminate\Support\Facades\Broadcast::class,
        'Bus' => Illuminate\Support\Facades\Bus::class,
        'Cache' => Illuminate\Support\Facades\Cache::class,
        'Config' => Illuminate\Support\Facades\Config::class,
        'Cookie' => Illuminate\Support\Facades\Cookie::class,
        'Crypt' => Illuminate\Support\Facades\Crypt::class,
        'DB' => Illuminate\Support\Facades\DB::class,
        'Eloquent' => Illuminate\Database\Eloquent\Model::class,
        'Event' => Illuminate\Support\Facades\Event::class,
        'File' => Illuminate\Support\Facades\File::class,
        'Gate' => Illuminate\Support\Facades\Gate::class,
        'Hash' => Illuminate\Support\Facades\Hash::class,
        'Http' => Illuminate\Support\Facades\Http::class,
        'Lang' => Illuminate\Support\Facades\Lang::class,
        'Log' => Illuminate\Support\Facades\Log::class,
        'Mail' => Illuminate\Support\Facades\Mail::class,
        'Notification' => Illuminate\Support\Facades\Notification::class,
        'Password' => Illuminate\Support\Facades\Password::class,
        'Queue' => Illuminate\Support\Facades\Queue::class,
        'Redirect' => Illuminate\Support\Facades\Redirect::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
        'Request' => Illuminate\Support\Facades\Request::class,
        'Response' => Illuminate\Support\Facades\Response::class,
        'Route' => Illuminate\Support\Facades\Route::class,
        'Schema' => Illuminate\Support\Facades\Schema::class,
        'Session' => Illuminate\Support\Facades\Session::class,
        'Storage' => Illuminate\Support\Facades\Storage::class,
        'Str' => Illuminate\Support\Str::class,
        'URL' => Illuminate\Support\Facades\URL::class,
        'Validator' => Illuminate\Support\Facades\Validator::class,
        'View' => Illuminate\Support\Facades\View::class,
        'Form' => Collective\Html\FormFacade::class,
        'Html' => Collective\Html\HtmlFacade::class,
    ],
];
