<div class="col-md-12 mb-3" v-show="dataFormValidatedOnView.length">
    <div class="card border-success mb-2">

        {{-- Titulo de la caja --}}
        <h5 class="card-header bg-success text-white">
            <i class="far fa-id-card mr-1"></i>

            {{-- si es un proceso dentro de un documento --}}
            @if (isset($document))

                @lang('Aquí se mostrarán los usuarios con su respectiva verificación de datos asignada')

            {{-- Para un proceso fuera del documento (independiente) --}}
            @else

                @lang('Aquí se mostrará la vista previa de la verificación de datos asignada')

            @endif
        </h5>
        {{-- /Titulo de la caja --}}

        {{-- Body principal que contiene todos firmantes validados --}}
        <div class="card-body">

            {{-- si es un proceso dentro de un documento --}}
            @if (isset($document))

                @include('dashboard.partials.form-data.partials.assignment-data-signer.data-to-signer')

            {{-- Para un proceso fuera del documento (independiente) --}}
            @else

                @include('dashboard.verificationform.partials.view-edit-delete-load-data-form')

            @endif

        </div>
        {{-- /Body principal que contiene todos firmantes validados --}}
    </div>
</div>
