<template>
    <b-modal id="help-for-document-requests">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header="{ok}">
            
            <span class="bold">@lang('¿Qué es es esto?')</span>

            <b-button @click="ok()" size="sm" variant="outline-danger">
                <i class="fas fa-times"></i>
            </b-button>

        </template>
        
        {{-- El contenido de la modal --}}

        <div class="bold">
            @lang('Puede solicitar cómodamente a sus clientes, proveedores, trabajadores o amigos los documentos que precise para realizar una gestión')
        </div>

        <div class="mt-2">
            @lang('Cada usuario recibirá un correo electrónico con un enlace que le permitirá subir los documentos que usted les pida')
        </div>

        <div class="mt-2 bold">
            @lang('Introduzca un nombre para su solicitud e indique que documentos les solicita (DNI, Carné de Conducir, Curriculum Vitae, etc)')
        </div>

        <div class="mt-2">
             @lang('Luego tendrá que seleccionar las direcciones de correo o los contactos a los que envía la solicitud')
        </div>
        
        {{-- El pié de la modal --}}
        <template #modal-footer="{ok}">
            <b-button @click="ok()" variant="primary">@lang('Aceptar')</b-button>
        </template>

    </b-modal>
</template>