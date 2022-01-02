/**
 * Añade el token CSRF a las peticiones AJAX realizadas con Axios
 * 
 * La eqtiqueta debe estar presente en la página
 *
 * <meta name="csrf-token" content="" />
 * 
 * @link https://laravel.com/docs/8.x/csrf
 */

 $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});