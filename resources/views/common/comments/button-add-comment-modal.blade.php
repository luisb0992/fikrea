{{-- Boton y modal para agregar un comentario a algun proceso de validacion --}}

{{-- Prevenir algun tipo de error si no existe el firmante --}}
<section id="section-app-comment">
    @isset($signer)

        @isset($validationType)
            {{-- boton para activar modal para agregar o ver un comentario --}}
            <b-button v-b-modal.add-comment-modal variant="outline-primary" class="disabledCommentButton ml-1"
                size="{{ isset($size) ? ($size == 'normal' ? '' : $size) : 'lg' }}">
                @if ($signer->getIfCommentExists($validationType))
                    @lang('Ver comentario')
                @else
                    @lang('Dejar un comentario')
                @endif
            </b-button>

            {{-- modal para agregar o ver un comentario --}}
            @include('common.comments.add-comment-modal', [
            'validationType' => $validationType
            ])
        @else

            {{-- boton para activar modal para agregar o ver un comentario --}}
            <b-button v-b-modal.add-comment-modal variant="outline-primary" class="disabledCommentButton ml-1"
                size="{{ isset($size) ? ($size == 'normal' ? '' : $size) : 'lg' }}">
                @if ($process->getIfCommentExists())
                    @lang('Ver comentario')
                @else
                    @lang('Dejar un comentario')
                @endif
            </b-button>

            {{-- modal para agregar o ver un comentario --}}
            @include('common.comments.add-comment-modal', [
            'process' => $process,
            'nameProcess' => $nameProcess
            ])
        @endisset

        {{-- mensaje de la app --}}
        <div id="comment-messages"
            data-complete="@lang('Su comentario ha sido guardado!')"
            data-error="@lang('Ha ocurrido un error inesperado, intente de nuevo en unos minutos')"
            data-empty="@lang('El comentario no puede estar vacío')"
            data-alreadydone="@lang('Ya ha enviado un comentario previamente')"
            data-sendtext="@lang('Comentario enviado')"
            ></div>
    @else
        <b-button variant="outline-primary" class="ml-1" size="lg">
            @lang('Comentario no disponible')
        </b-button>
    @endisset
</section>
