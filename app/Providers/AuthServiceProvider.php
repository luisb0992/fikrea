<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Las políticas de la aplicación
     *
     * @var array
     */
    protected $policies = [
        // Política de contactos
        \App\Models\Contact::class              => \App\Policies\ContactPolicy::class,
        // Política de documentos
        \App\Models\Document::class             => \App\Policies\DocumentPolicy::class,
        // Política de sellos que se pueden estampar sobre los documentos
        \App\Models\Stamp::class                => \App\Policies\StampPolicy::class,
        // Política de solicitudes de documentos
        \App\Models\DocumentRequest::class      => \App\Policies\DocumentRequestPolicy::class,
        // Política de los archivos de las solicitudes de documentos
        \App\Models\DocumentRequestFile::class  => \App\Policies\DocumentRequestFilePolicy::class,
        // Política de archivos subidos
        \App\Models\File::class                 => \App\Policies\FilePolicy::class,
        // Política de archivos compartidos
        \App\Models\FileSharing::class          => \App\Policies\FileSharingPolicy::class,
        // Política de grabaciones de audio
        \App\Models\Audio::class                => \App\Policies\AudioPolicy::class,
        // Política de grabaciones de video
        \App\Models\Video::class                => \App\Policies\VideoPolicy::class,
        // Política de capturas de pantalla
        \App\Models\Capture::class              => \App\Policies\CapturePolicy::class,
        // Política de notificaciones
        \App\Models\Notification::class         => \App\Policies\NotificationPolicy::class,
        // Política de los pedidos (órdenes de pago)
        \App\Models\Order::class                => \App\Policies\OrderPolicy::class,
        // Política de las subscripciones
        \App\Models\Subscription::class         => \App\Policies\SubscriptionPolicy::class,
        // Politica para la verificación del formulario de datos
        \App\Models\VerificationForm::class     => \App\Policies\VerificationFormPolicy::class,
        // Politica para la gestion de eventos
        \App\Models\Event::class                => \App\Policies\EventPolicy::class,
    ];

    /**
     * Registra los servicios de autenticación y autorización
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
