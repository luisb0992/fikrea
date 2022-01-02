<template>
    <b-modal id="share-billing-data">

        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <h5 class="modal-title">
                <i class="fas fa-info-circle text-info"></i>
                <span class="bold">@lang('Compartir Datos')</span>
            </h5>
        </template>

        {{-- El contenido de la modal --}}
        <div>
            <div class="alert alert-info alert-dismissible fade show" role="alert" v-if="shareWithSignature">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">@lang('Cerrar')</span>
                </button>
                <span>
                    <i class="fas fa-info-circle"></i>
                    @lang('Puede enviar un título y comentario opcional indicando el motivo de su compartición')
                </span>
            </div>
            <form id="shareEmail" action="@route('dashboard.profile.shareBilling')" method="POST">
                @csrf

                <div v-if="shareWithSignature">
                    <div class="form-group">
                        <label for="shareTitle">@lang('Título')</label>
                        <input type="text" name="title" class="form-control" form="shareEmail" id="shareTitle">
                    </div>
                    <div class="form-group">
                        <label for="shareComment">@lang('Comentario')</label>
                        <textarea name="comment" class="form-control" form="shareEmail" id="shareComment" rows="5"></textarea>
                    </div>
                    <input type="hidden" name="signature" value="true">
                </div>

                <div class="form-group">
                    <label for="shareEmail">@lang('Escriba el correo con el cual compartirá sus datos de facturación')</label>
                    <input type="email" name="shareEmail" class="form-control" form="shareEmail" required id="shareEmail">
                </div>
            </form>
        </div>

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="cancel" variant="danger">
                @lang('Cancelar')
            </b-button>
            <button type="submit" class="btn btn-success" form="shareEmail">
                @lang('Compartir')
            </button>
        </template>

    </b-modal>
</template>
