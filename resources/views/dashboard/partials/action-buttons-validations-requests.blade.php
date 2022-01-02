{{-- 
    Los botones de Acción en la vista donde se crean las solicitudes de documentos
    a los firmantes con ese tipo de validación seleccionada previamente
--}}

{{-- Los botones de Acción --}}
<div class="col-md-12 mb-4">
    <div class="btn-group" role="group">

        {{-- Botón para continuar o finalizar el proceso de config --}}
        <a href="@route('dashboard.document.validations', ['id' => $document->id])" 
            data-redirect-to-save="@route('dashboard.document.request.validations', ['id' => $document->id])"
            data-redirect-to-list="@route('dashboard.document.list')"
            @click.prevent="saveRequests"
            class="btn btn-lg btn-success mr-1">
            @lang('Finalizar')
        </a>
        {{-- /Botón para continuar o finalizar el proceso de config --}}

        {{-- Botón para cancelar o ir atrás en el proceso de config --}}
        <a href="@route('dashboard.document.validations', ['id' => $document->id])" class="btn btn-lg btn-danger">
            @lang('Atrás')
        </a>
        {{-- /Botón para cancelar o ir atrás en el proceso de config --}}

    </div>
</div>
{{--/Los botones de Acción --}}