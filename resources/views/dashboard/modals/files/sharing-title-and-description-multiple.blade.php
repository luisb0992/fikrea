<template>
    <b-modal id="sharing-title-and-description-multiple">
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-info-circle fa-2x"></i>
            <span class="bold">@lang('Título y Descripción')</span>
        </template>

        {{-- El contenido de la modal --}}

        <div class="mt-2">
            <div class="mb-2">@lang('Indique un título y una descripción para los ficheros que se compartirán')</div>
            <div class="form-row">
                <div class="col-12">
                    <div class="position-relative form-group">
                        <label for="extra-data-title">@lang('Título')</label>
                        <input name="extra-data-title" id="extra-data-title" type="text" class="form-control"
                               maxlength="100" autofocus/>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="col-12">
                    <div class="position-relative form-group">
                        <label for="extra-data-description" class="">@lang('Descripción')</label>
                        <input name="extra-data-description" id="extra-data-description" type="text"
                               class="form-control" maxlength="255"/>
                    </div>
                </div>
            </div>
        </div>

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="shareFilesNoContacts()" variant="action-copy-url">@lang('Copiar URL')</b-button>
            <b-button @click.prevent="cancel()" variant="secondary">@lang('Cancelar')</b-button>
        </template>

    </b-modal>
</template>