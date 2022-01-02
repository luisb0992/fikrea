{{-- Los botones de Acción --}}
<div class="col-md-12 mb-4">

    <div class="btn-group" role="group">
        <a href="#" :class="requestIsNotValid ? 'disabled' : ''"
            @click.prevent="save" class="btn btn-lg btn-success mr-1"
            data-save-request="@route('workspace.document.request.save', ['token' => $token])"

            {{-- Según dónde estoy redirijo al workspace o al estado del documento --}}
            @dashboard
            data-redirect-request="@route('dashboard.document.status', ['id' => $validation->document->id])">
            @else
            data-redirect-request="@route('workspace.home', ['token' => $token])">
            @enddashboard
            {{-- / Según dónde estoy redirijo al workspace o al estado del documento --}}

            @lang('Finalizar')
        </a>

        {{-- Según dónde estoy redirijo al workspace o al estado del documento --}}
        @dashboard
        {{-- @route('dashboard.document.status', ['id' => $document->id]) --}}
        <a href="@route('dashboard.document.status', ['id' => $validation->document->id])" class="btn btn-lg btn-danger">
            @lang('Atrás')
        </a>
        @else
        <a href="@route('workspace.home', ['token' => $token])" class="btn btn-lg btn-danger">
            @lang('Atrás')
        </a>
        @enddashboard
        {{-- / Según dónde estoy redirijo al workspace o al estado del documento --}}

        {{--  modal para agregar o ver un comentario  --}}
        @includeWhen($signer->document, 'common.comments.button-add-comment-modal', [
            'validationType' => config('validations.document-validations.documentRequest')
        ])
    </div>

</div>
{{--/Los botones de Acción --}}