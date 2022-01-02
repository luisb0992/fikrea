<?php

/**
 * ServiceProvider que proporciona lam organización de las rutas de la aplicación
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Providers;

use Illuminate\Support\Facades\Route;

class CustomRouteServiceProvider extends RouteServiceProvider
{
    /**
     * Define las rutas de la aplicación
     *
     * @return void
     */
    public function map():void
    {
        // Llama al Service Provider padre utilizado por defecto
        parent::map();

        // Rutas landing page
        $this->mapLandingRoutes();

        // Rutas de autenticación
        $this->mapAuthRoutes();

        // Rutas de contacto con el cliente
        $this->mapCustomerContactRoutes();

        // Rutas de dashboard page
        $this->mapDashboardRoutes();

        // Rutas para el manejo de la subscripción del usuario
        $this->mapSubscriptionRoutes();

        // Rutas para el manejo de documentos
        $this->mapDocumentRoutes();

        // Rutas para el mamejo de solicitudes de documentos
        $this->mapDocumentRequestRoutes();

        // Rutas para el manejo de archivos
        $this->mapFilesRoutes();

        // Rutas para el manejo del espacio de trabajo de los usuarios firmantes
        $this->mapWorkspaceRoutes();

        // Rutas para el manejo del backend del usuario administrador del sitio
        $this->mapBackendRoutes();

        // Rutas para verificación y certificación del formulario de datos
        $this->mapVerificationFormRoutes();

        // Rutas para la gestions de eventos del usuario
        $this->mapEventRoutes();
    }

    /**
     * Las rutas de la página de "landig" de la aplicación
     *
     * Prefijo: landing/
     *
     * @return void
     */
    protected function mapLandingRoutes():void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->prefix('landing')
            ->group(base_path('routes/landing.php'));
    }

     /**
     * Las rutas de autenticación de la aplicación
     *
     * Prefijo: /landing
     *
     * @return void
     */
    protected function mapAuthRoutes():void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->prefix('dashboard')
            ->group(base_path('routes/auth.php'));
    }

     /**
     * Las rutas de contacto con el cliente de la aplicación
     *
     * Prefijo: /contact
     *
     * @return void
     */
    protected function mapCustomerContactRoutes():void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->prefix('contact')
            ->group(base_path('routes/contact.php'));
    }

    /**
     * Las rutas de autenticación de la aplicación
     *
     * Prefijo: /dashboard
     *
     * @return void
     */
    protected function mapDashboardRoutes():void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->prefix('dashboard')
            ->group(base_path('routes/dashboard.php'));
    }

    /**
     * Las rutas de manipulación y control de la subscripción del usuario
     *
     * Prefijo: /subscription
     *
     * @return void
     */
    protected function mapSubscriptionRoutes():void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->prefix('subscription')
            ->group(base_path('routes/subscription.php'));
    }

    /**
     * Las rutas para la manipulación de los documentos
     *
     * Prefijo: document/
     *
     * @return void
     */
    protected function mapDocumentRoutes():void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->prefix('dashboard/document')
            ->group(base_path('routes/document.php'));
    }

    /**
     * Las rutas para la manipulación de las solicitudes de documentos
     *
     * Prefijo: document/request/
     *
     * @return void
     */
    protected function mapDocumentRequestRoutes():void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->prefix('dashboard/document/request')
            ->group(base_path('routes/request.php'));
    }

    /**
     * Las rutas para la verificación y certificación de un formulario de datos
     *
     * @return void
     */
    protected function mapVerificationFormRoutes():void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->prefix('dashboard/verificationform')
            ->group(base_path('routes/verificationform.php'));
    }

    /**
     * Las rutas para la gestion de eventos
     *
     * @return void
     */
    protected function mapEventRoutes():void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->prefix('dashboard/event')
            ->group(base_path('routes/event.php'));
    }

    /**
     * Las rutas para la subida de archivos
     *
     * Prefijo: file/
     *
     * @return void
     */
    protected function mapFilesRoutes():void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->prefix('dashboard/file')
            ->group(base_path('routes/file.php'));
    }

    /**
     * Las rutas para la manipulación del espacio de trabajo del usuario
     *
     * Prefijo: document/
     *
     * @return void
     */
    protected function mapWorkspaceRoutes():void
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->prefix('workspace')
            ->group(base_path('routes/workspace.php'));
    }

    /**
     * Las rutas para el backend del usuario administardor del sitio
     *
     * Prefijo: admin/
     *
     * @return void
     */
    protected function mapBackendRoutes():void
    {
        Route::middleware('admin')
            ->namespace($this->namespace)
            ->prefix('admin')
            ->group(base_path('routes/backend.php'));
    }
}
