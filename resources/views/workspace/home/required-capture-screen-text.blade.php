{{--Si se debe validar con captura de pantalla--}}
@if ($signer->mustBeValidateByScreenCapture())
<div class="text-warning small mb-4 ml-5 bold">
    <p class="text-justify">
        @lang('El solicitante quiere que usted haga una captura de pantalla mientras completa el proceso de edición de documento').
    </p>
    <p class="text-justify">
        @lang('Al acceder estará de acuerdo con lo solicitado y de no estar de acuerdo puede rechazar esta validación').
    </p>
</div>
@endif
{{--/Si se debe validar con captura de pantalla--}}