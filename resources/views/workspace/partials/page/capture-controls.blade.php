{{--
    Control de la captura de pantalla 
    sólo disponible desde equipos de escritorio    
--}}
<div id="capture"
    data-max-record-time="@config('validations.capture.recordtime')"
    data-must-be-validated-by-screen-capture="{{$signer->mustBeValidateByScreenCapture()}}"
></div>

{{-- Si debo validar con captura de pantalla --}}
{{-- Verifico que este en pc sino muestro mensaje de alerta --}}

@if ($signer->mustBeValidateByScreenCapture())

    @desktop
        <div class="screen-capture">
            {{-- La ventana que muestra la captura de pantalla --}}
            <div class="col-md-12 mt-2">
                <video id="video-screen" ref="videoScreen" autoplay></video>
            </div>  
            {{--/La ventana que muestra la captura de pantalla --}}
        </div>
    @else
        <div class="alert alert-warning bold mt-2">
            <i class="fas fa-exclamation-triangle"></i>
            @lang('El usuario quiere que realice una grabación de la pantalla, pero su dispositivo móvil no lo permite').
            @lang('Puede realizar esta validación desde un equipo de escritorio para cumplir este requerimiento').
        </div>
    @enddesktop

@endif
{{-- /Control de la captura de pantalla --}}