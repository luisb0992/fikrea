<?php

/**
 * ServiceProvider que proporciona directivas personalizada de blade
 *
 * Permite definir nuevas directivas que extienden Blade
 *
 * @link https://laravel.com/docs/8.x/blade#extending-blade
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

use Fikrea\Mobile;

class BladeDirectivesServiceProvider extends ServiceProvider
{
    /**
     * Registra el Service Provider
     *
     * @return void
     */
    public function register():void
    {
        //
    }

    /**
     * Punto de entrada del Service Provider
     *
     * @return void
     */
    public function boot():void
    {
        //
        // @datetime($datetime)
        //
        // @param \DateTime $datetime  Una fecha. Si no se indica se pone la fecha-hora actual.
        //
        // Muestra la fecha en formato d-m-Y H:i:s
        Blade::directive('datetime', function ($datetime = null) {
            return "<?php echo (new \DateTime($datetime))->format('d-m-Y H:i'); ?>";
        });

        //
        // @date($datetime)
        //
        // @param \DateTime $datetime  Una fecha. Si no se indica se pone la fecha actual.
        //
        // Muestra la fecha en formato d-m-Y
        Blade::directive('date', function ($datetime = null) {
            return "<?php echo (new \DateTime($datetime))->format('d-m-Y'); ?>";
        });

        //
        // @time($datetime)
        //
        // @param \DateTime $datetime  Una fecha. Si no se indica se pone la fecha actual.
        //
        // Muestra la hora en formato H:i
        Blade::directive('time', function ($datetime = null) {
            return "<?php echo (new \DateTime($datetime))->format('H:i'); ?>";
        });

        //
        // @year()
        //
        // Muestra el año actual
        Blade::directive('year', function () {
            return "<?php echo (new \DateTime)->format('Y'); ?>";
        });

        //
        // @locale
        //
        // Muestra el idioma actual de la página
        Blade::directive('locale', function () {
            return "<?php echo app()->getLocale(); ?>";
        });

        //
        // @asset($file)
        //
        // @param string $file      Un archivo
        //
        // Devuelve la ruta de accesoa a un archivo de recursos (asset)
        Blade::directive('asset', function ($file) {
            return "<?php echo asset($file); ?>";
        });

        //
        // @mix(file)
        //
        // @param string $file       Un archivo
        //
        // Devuelve la ruta de acceso a un archivo de recursos obtenida por mix
        Blade::directive('mix', function ($file) {
            return "<?php echo mix($file); ?>";
        });

        //
        // @route($route, $param)
        //
        // @param string $route     El nombre de la ruta
        // @param mixed  $param     El parámetro opcional puede ser un valor o un array
        //
        // Devuelve la ruta de acceso a un archivo de recursos (asset)
        Blade::directive('route', function ($route, $param = null) {
            return "<?php echo route($route, $param); ?>";
        });

        //
        // @url($path)
        //
        // @param string $path     Una ruta relativa de un archivo
        //
        // Devuelve la URL correspondiente a un archivo
        Blade::directive('url', function ($path) {
            return "<?php echo url($path); ?>";
        });

        //
        // @current
        //
        // Devuelve la URL actual
        Blade::directive('current', function () {
            $path = url(Request::path());
            return "<?php echo '$path'; ?>";
        });

        //
        // @path($path)
        //
        // @param string $path      Una ruta absoluta de un archivo
        //
        // Devuelve la ruta absoluta de un archivo, útil al trabajar con imágenes
        // en la generación de archivos PDF
        Blade::directive('path', function ($path) {
            return "<?php echo public_path($path); ?>";
        });

        //
        // @hostname($ip)
        //
        // @param string $ip       Una dirección ip
        //
        // Devuelve el nombre DNS del host cuya dirección IP se suministra
        Blade::directive('hostname', function ($ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                return "<?php echo gethostbyaddr($ip); ?>";
            } else {
                return "";
            }
        });

        //
        // @useragent($user_agent)
        //
        // @param string $user_agent      El agente de usuario
        //
        // Devuelve el agente de usuario en un formato simple
        // expersando el sistema operativo y el navegador y versión
        Blade::directive('useragent', function ($user_agent) {
            return "<?php 
                        echo (new  \Fikrea\Browser({$user_agent}))->getOs();
                        echo ' ';
                        echo (new  \Fikrea\Browser({$user_agent}))->getBrowser(); 
                    ?>";
        });

        //
        // @userdevice($device)
        //
        // @param DeviceType $device     El tipo de dispositivo que utilizó el usuario en su conexión
        //
        // Devuelve el texto en formato humano del dispositivo que utilizó el usuario en su conexión
        Blade::directive('userdevice', function ($device) {
            return "<?php echo \App\Enums\DeviceType::fromValue({$device}); ?>";
        });

        //
        // @old($field)
        //
        // @param string $field    El nombre de un campo de formulario
        //
        // Devuelve el valor del campo indicado anterior al envío del formulario
        Blade::directive('old', function ($field) {
            return "<?php echo old($field); ?>";
        });

        //
        // @exists($var)
        //
        // param string $var        El nombre de la variable
        //
        // Si existe la variable devuelve su valor
        Blade::directive('exists', function ($var) {
            return "<?php echo $var ?? ''; ?>";
        });

        //
        // @config($option)
        //
        // @param string $option    Obtiene una opción de configuración. Ej: app.name
        //
        // Devuelve el valor de la configuración
        Blade::directive('config', function ($option) {
            return "<?php echo config($option); ?>";
        });

        //
        // @stripext($file)
        //
        // @param string $file      Un nombre de archivo
        //
        // Devuelve el nombre de un archivo sin su extensión
        Blade::directive('stripext', function ($filename) {
            return "<?php echo preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename) ?>";
        });

        //
        // @filesize($size)
        //
        // @param float $size       El tamaño de un archivo en bytes
        //
        // Devuelve el tamaño formateado del archivo
        // Ejemplo: 2,364 kB; 18,204 MB.
        Blade::directive('filesize', function ($size) {
            return "<?php
                switch(intval(log($size)/log(2)/10)) {
                    case 0:    
                    case 1:
                        echo number_format($size/1024, 1, ',', '');
                        echo ' kB';
                        break;
                    case 2:
                        echo number_format($size/(1024*1024), 1, ',', '');
                        echo ' MB';
                        break;
                    case 3:
                        echo number_format($size/(1024*1024*1024), 1, ',', '');
                        echo ' GB';
                        break;
                }
            ?>";
        });

        //
        // @number($value)
        //
        // @param Float $value      Un valor numérico a representar
        //
        // Representa el valor numérico con dos decimales
        Blade::directive('number', function ($value) {
            return "<?php
                echo number_format($value, 2);
            ?>";
        });

        //
        // @validation($validation)
        //
        // @param Validation $validation    Una validación
        //
        // Devuelve el texto decriptivo de la validación
        Blade::directive('validation', function ($validation) {
            return "<?php 
                echo \App\Enums\ValidationType::fromValue({$validation}->validation)
            ?>";
        });

        //
        // @json($object)
        //
        // @param object $object            El objeto a representar como json
        //
        // Representa un objeto en forma de json
        Blade::directive('json', function ($object) {
            return "<?php
                echo htmlentities(json_encode($object));
            ?>";
        });


        //
        // @ip($ip)
        //
        // @param string $ip            El ip a mostrar
        //
        // Muestra el ip o localhost si es 127.0.0.1
        Blade::directive('ip', function ($ip) {
            return "<?php
                echo $ip == '127.0.0.1' ? 'localhost' : $ip;
            ?>";
        });

        //
        // @admin
        // ...
        // @else
        // ...
        // @endadmin
        //
        // Si el usuario es administrador
        Blade::if('admin', function () {
            $user = Auth::user();
            return $user && $user->isAdmin();
        });

        //
        // @landing
        // ...
        // @else
        // ...
        // @endlanding
        //
        // Si estamos en el landing
        Blade::if('landing', function () {
            return Str::of(url()->current())->contains('landing');
        });

        //
        // @dashboard
        // ...
        // @else
        // ...
        // @enddashboard
        //
        // Si estamos en el dashboard
        Blade::if('dashboard', function () {
            return Str::of(url()->current())->contains('dashboard');
        });

        //
        // @workspace
        // ...
        // @else
        // ...
        // @endworkspace
        //
        // Si estamos en el workspace
        Blade::if('workspace', function () {
            return Str::of(url()->current())->contains('workspace');
        });

        //
        // @backend
        // ...
        // @else
        // ...
        // @endbackend
        //
        // Si estamos en el backend
        Blade::if('backend', function () {
            return Str::of(url()->current())->contains('backend');
        });

        //
        // @mobile
        // ...
        // @else
        // ...
        // @endmobile
        //
        // Si el dispositivo es un móvil o una tablet
        Blade::if('mobile', function () {
            return Mobile::isMobile();
        });

        //
        // @desktop
        // ...
        // @else
        // ...
        // @enddesktop
        //
        // Si el dispositivo es de escritorio como un PC
        Blade::if('desktop', function () {
            return !Mobile::isMobile();
        });
    }
}
