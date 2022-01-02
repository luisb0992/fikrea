<template>
    <b-modal id="help-for-guest-user">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            
            <span class="bold">@lang('Configure su perfil de usuario')</span>

            <b-button size="sm" variant="outline-danger" @click="hideHelpModalForGuestUser">
                <i class="fas fa-times"></i>
            </b-button>

        </template>
        
        {{-- El contenido de la modal --}}

        <div class="bold">
            @lang('Recuerde introducir su nombre y su dirección de correo')
        </div>

        <div class="mt-2">
            @lang('De este modo, sus usuarios sabrán que usted les ha remitido los documentos para su revisión y aprobación')
        </div>

        <div class="text-center mt-2">
            <img src="@asset('/assets/images/dashboard/images/girl-at-work.png')" alt="" />
        </div>

        <div class="mt-2 bold">
            @lang('¿Quiere recuperar la información de una sesión anterior?')
        </div>

        <div class="mt-2">
             @lang('Para ello puede pulsar en "Recuperar Sesión". Se le pedirá el correo electrónico que utilizó en esa ocasión para identificarse.')
        </div>
        
        {{-- El pié de la modal --}}
        <template #modal-footer>
            <b-button @click="recoverySession" data-session-recovery-request="@route('dashboard.profile.session')" variant="secondary">@lang('Recuperar Sesión')</b-button>
            <b-button @click="hideHelpModalForGuestUser" variant="primary">@lang('Aceptar')</b-button>
        </template>

    </b-modal>
</template>