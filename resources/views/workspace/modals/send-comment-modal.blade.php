<template>
    <b-modal id="send-comment-modal">

        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <h5 class="modal-title">
                <i class="fas fa-info-circle"></i> @lang('Enviar un comentario al solicitante')
            </h5>
        </template>

        {{-- El contenido de la modal --}}
        <div class="text-justify">
            <div class="form-group">
                <textarea form="comment_workspace" type="text" rows="3" class="form-control" name="comment" placeholder="@lang('Comentario')"></textarea>
            </div>
        </div>

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="cancel" variant="secondary">
                @lang('Volver')
            </b-button>
            <form id="comment_workspace" action="@route('workspace.store.comment', ['token' => $token])" method="POST">
                @csrf
            </form>
            <button form="comment_workspace" type="submit" class="btn btn-primary">@lang('Enviar comentario')</button>
        </template>

    </b-modal>
</template>
