<template>
    <b-modal id="cancel-signer-modal"> 
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-exclamation-triangle fa-2x"></i>
            <span class="bold">@lang('Cambio de Página')</span>
        </template>
        
        {{-- El contenido de la modal --}}
        
        <div class="mt-2">
            
            <p>
                @lang('Al salirse de este proceso se perderá toda la información')
            </p>

            <p class="mt-2 p-2">
                @lang('¿Está seguro que desea salir de este proceso?')
            </p>
            
        </div>
        
        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click="cancel()" variant="success">@lang('Cancelar')</b-button>

            <button @click.prevent="cancelProcess"
                data-request="@route('dashboard.document.delete')"
                data-redirect="@route('dashboard.document.list')"
                class="btn btn-danger"
            >
                @lang('Aceptar')
            </button>
            </form>
        </template>

    </b-modal>
</template>