<template>
    <b-modal id="share-billing-data-via-link">

        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <h5 class="modal-title">
                <i class="fas fa-info-circle text-info"></i>
                <span class="bold">@lang('Compartir Datos')</span>
            </h5>
        </template>

        {{-- El contenido de la modal --}}
        <div>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">@lang('Cerrar')</span>
                </button>
                <span>
                    <i class="fas fa-info-circle"></i>
                    @lang('Puede enviar un título y comentario opcional indicando el motivo de su compartición')
                </span>
            </div>
            <div class="form-group">
                <label for="shareTitle">@lang('Título')</label>
                <input type="text" name="title" class="form-control" id="shareTitle" v-model="billingData.title">
            </div>
            <div class="form-group">
                <label for="shareComment">@lang('Comentario')</label>
                <textarea name="comment" class="form-control" id="shareComment" rows="5"
                    v-model="billingData.comment"></textarea>
            </div>
        </div>

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="cancel" variant="danger">
                @lang('Cancelar')
            </b-button>
            <button class="btn btn-success" data-url="@route('dashboard.profile.shareBillingForLink')"
                @click="getLinkShareBillingData">
                @lang('Compartir')
            </button>
        </template>

    </b-modal>
</template>
