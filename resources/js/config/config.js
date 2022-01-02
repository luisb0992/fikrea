/**
 * Configuración de las librerías javascript externas
 *
 * @author javieru <javi@gestoy.com>
 * @copyright 2021 Retail Servicios Externos
 */

 /**
  * Configura toastr
  * 
  * @link https://github.com/CodeSeven/toastr
  */
toastr.options = {
    closeButton: true,                          // Mostrar botón de cierre
    debug: false,                               // Modo depuración
    newestOnTop: false,                         // Mostrar los nuevos mensajes por arriba (true) o por debajo (false)
    progressBar: false,                         // Mostrar la barra de progreso
    positionClass: 'toast-bottom-right',        // La posición del mensaje
    preventDuplicates: false,                   // Evitar los mensajes duplicados
    onclick: null,                              // Evento click
    showDuration: 300,                          // Tiempo antes de mostrarse
    hideDuration: 1000,                         // Tiempo empleado en ocultarse
    timeOut: 10000,                             // Tiempo de duración del mensaje en milisegundos
    extendedTimeOut: 1000,                      // Tiempo de duración extendido al pasar el ratón sobre el mensaje
    showEasing: 'swing',                        // Efecto de alivio de entrada
    hideEasing: 'linear',                       // Efecto de alivio de salida
    showMethod: 'fadeIn',                       // Animación al entrar
    hideMethod: 'fadeOut',                      // Animación al salir
};
