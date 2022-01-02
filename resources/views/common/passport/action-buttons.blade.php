{{-- Los botones de Acción --}}
<div class="row col-md-12 mb-4">
    <div class="input-group">
        
        @isset($token)
            <a href="#" 
                :class="!canFinish ? 'disabled': ''"
                @click.prevent="save" class="btn btn-lg btn-success">
                @lang('Finalizar')
            </a>

            <a href="@route('workspace.home', ['token' => $token])" class="btn btn-lg btn-danger ml-2">
                @lang('Atrás')
            </a>
        @else
            <a href="#" 
                :class="!canFinish ? 'disabled': ''"
                @click.prevent="save" class="btn btn-lg btn-success">
                @lang('Finalizar')
            </a>

            <a href="@route('dashboard.document.status', ['id' => $signer->document->id])" class="btn btn-lg btn-danger ml-2">
                @lang('Atrás')
            </a>
        @endisset

        {{--  modal para agregar o ver un comentario  --}}
        @include('common.comments.button-add-comment-modal', [
            'validationType' => config('validations.document-validations.passport')
        ])
    </div>
</div>
{{--/Los botones de Acción --}}