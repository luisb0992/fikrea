{{-- El marcador donde se debe situar una caja de texto en el documento --}}
<div data-box-id=""
    data-message-for-creator="@lang('Debe completar los datos en la caja de texto')"
    class="box-placeholder template"
>
    <div class="box-placeholder-header">
        {{-- El id o código identificador de la caja --}}
        <span class="box-placeholder-id d-none d-md-inline"></span>

        {{-- Botones de opciones en la caja de texto --}}
        <span class="btn-group" role="group" aria-label="@lang('Opciones de caja de texto')">
            {{-- Botón para limpiar el texto introducido en el contenedor de la caja de texto --}}
            <button data-box-id="" class="remove-box-placeholder btn-transition">
                <i class="fas fa-broom"></i>
            </button>
            {{-- /Botón para limpiar el texto introducido en el contenedor de la caja de texto --}}
        </span>
        {{-- Botones de opciones en la caja de texto --}}

    </div>
    <div class="box-placeholder-body">
        <div class="box">
            
        </div>
        <div class="signer-name"></div>
    </div>
</div>
{{--/ El marcador donde se debe situar una caja de texto en el documento --}}