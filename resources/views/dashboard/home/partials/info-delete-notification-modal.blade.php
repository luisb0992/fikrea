<template>
    <b-modal id="delete-notification-{{ $notification->id }}">

        {{-- El encabezado de la modal --}}
        <template #modal-title>
            <h3>
                <i class="fas fa-info-circle text-info"></i>
                <span class="text-info">@lang('Aviso')</span>
            </h3>
        </template>

        {{-- El contenido de la modal --}}
        <div class="mt-2">
            <p>
                @lang('Esta opción marcará la notificación como leída.')
                <hr>
                @lang('Si desea continuar presione el botón <b>Marcar como leída</b>')
            </p>
        </div>

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click="cancel()" variant="success">@lang('Volver')</b-button>
            <b-button @click.prevent="read({{ $notification->id }})" variant="danger">
                @lang('Marcar como leída')
            </b-button>
        </template>

    </b-modal>
</template>
