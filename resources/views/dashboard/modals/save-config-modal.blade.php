<template>
    <b-modal id="save-config-modal"> 
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-exclamation-triangle fa-2x"></i>
            <span class="bold">@lang('Cambio Realizado de Manera Satisfactoria')</span>
        </template>
        
        {{-- El contenido de la modal --}}
        
        <div class="mt-2">
            @lang('Su cambio ha sido Guardado, le redirigimos a notificaciones.')
        </div>
        
        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <a href="@route('dashboard.document.list')" @click.prevent="saveConfig" class="btn btn-lg btn-success mr-1">@lang('Aceptar')</a>
        </template>

    </b-modal>
</template>