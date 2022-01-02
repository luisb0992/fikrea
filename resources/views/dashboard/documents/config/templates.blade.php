{{-- El marcador donde se debe situar una firma en el documento --}}
<div data-sign-id=""
    data-message-for-creator="@lang('Debe trazar su propia firma en el marcador')"
    class="sign-placeholder template"
    >
    <div class="sign-placeholder-header" data-sign-id="">
        {{-- El id o código identificador de la firma --}}
        <span class="sign-placeholder-id d-none d-md-inline"></span>

        {{-- Botones navegación por el documento --}}
        <span class="btn-group" role="group" aria-label="@lang('Opciones de firma')">
            {{-- Botón para mover el contenedor de firma --}}
            <button data-sign-id="" class="move-sign-placeholder btn-transition d-none">
                <i class="fas fa-arrows-alt"></i>
            </button>
            {{-- /Botón para mover el contenedor de firma --}}

            {{-- Botón para limpiar el contenedor de firma --}}
            <button data-sign-id="" class="clear-sign-placeholder btn-transition">
                <i class="fa fa-trash"></i>
            </button>
            {{-- /Botón para limpiar el contenedor de firma --}}

            {{-- Botón para eliminar el contenedor de firma --}}
            <button data-sign-id="" class="remove-sign-placeholder btn-transition">
                <i class="fas fa-times-circle"></i>
            </button>
            {{-- /Botón para eliminar el contenedor de firma --}}

        </span>
        {{-- /Botones navegación por el documento --}}

    </div>
    <div class="sign-placeholder-body">
        <canvas class="sign" data-sign-id="" width="100%" height="100%"></canvas>
        <div class="signer-name"></div>
    </div>
</div>
{{--/ El marcador donde se debe situar una firma en el documento --}}

{{-- El marcador de posición de un sello estampado en un documento --}}
<div data-stamp-id="" class="stamp-placeholder template">
    <button class="btn btn-danger rounded-circle remove-stamp-placeholder">
        <i class="fa fa-times"></i>
    </button>
    {{-- La imagen con el sello stampado --}}
    <img src="" alt="" class="stamp" />
</div>    
{{--/El botón que permite eliminar un sello estampado en un documento --}}

{{-- La firma por defecto que utiliza el creador para firmar
     si es que se ha definido una en la configuración del usuario
     y se ha autorizado para ser utilizada en los procesos de firma

     Se encuentra oculta a la vista del usuario
--}}
<div class="d-none">
    @if ($user->config->sign->useAsDefault && $user->config->sign->sign)
    <img id="user-sign" src="{{$user->config->sign->sign}}" alt="" />
    @else
    <img id="user-sign" src="" alt="" />
    @endif
</div>
{{--/La firma por defecto que utiliza el creador para firmar --}}