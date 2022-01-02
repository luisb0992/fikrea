{{-- Los botones de acción --}}
<div class="col-md-12 mt-2">
    <div class="btn-group" role="group">
        
        {{-- Si se está en un pc, y debe grabarse la pantalla --}}
        @desktop
        @if ($signer->mustBeValidateByScreenCapture())
            {{-- Botón iniciar proceso de firma--}}
            <button
                {{-- Visible cuando se debe grabar pantalla y no se esta grabando --}}
            	v-show="!recording"

                {{-- Se habilita cuando se cargan las firmas --}}
                :disabled="!signs"

            	@click.prevent="recordCapture" class="btn mr-1" 
                :class="recording ? 'btn-danger' : 'btn-primary'">  
                <span v-if="recording">
                	<i class="fas fa-stop"></i>
                    @lang('Detener') @{{showTimer}}
                </span>
                <span v-else>
                    <i class="fas fa-desktop"></i>
                    @lang('Iniciar grabación de pantalla')
                </span>
            </button>
            {{-- /Botón iniciar proceso de firma--}}
        @endif
        @enddesktop
        {{-- / Si se está en un pc, y debe grabarse la pantalla --}}

        {{-- Se muestra cuando 
            1 ) Se debe hacer captura de pantalla y se ha comenzado a grabar
        --}}
        @if ($signer->mustBeValidateByScreenCapture())
            {{-- Botón finalizar proceso de firma--}}
            <button type="button"
                v-show="recording"
                {{-- Habilitado cuando se han realizado todas las firmas --}}
                :disabled="!allSignaturesHaveBeenMade"
            	@click.prevent="saveSignedDocument" class="btn btn-lg btn-success mr-1"
            >
                @lang('Finalizar') @{{showTimer}}
            </button>
            {{-- /Botón finalizar proceso de firma--}}
        @else
            {{-- Botón finalizar proceso de firma--}}
            {{-- Se muestra cuando 
                1 ) Cuando no debe hacer captura de pantalla y no se está grabando 
            --}}
            <button type="button"
                v-show="!recording"
                {{-- Habilitado cuando se han realizado todas las firmas --}}
                :disabled="!allSignaturesHaveBeenMade"
                @click.prevent="saveSignedDocument" class="btn btn-lg btn-success mr-1"
            >
                @lang('Finalizar')
            </button>
            {{-- /Botón finalizar proceso de firma--}}

        @endif

        {{-- Botón cancelar proceso de firma--}}
        <a href="@route('workspace.validate.signature', ['token' => $token])"
            class="btn btn-lg btn-danger"
        >
            @lang('Cancelar')
        </a>
        {{-- /Botón cancelar proceso de firma--}}

        {{--  modal para agregar o ver un comentario  --}}
        @include('common.comments.button-add-comment-modal', [
            'validationType' => config('validations.document-validations.handWrittenSignature')
        ])

    </div>
</div>
{{--/Los botones de acción --}}