<template>
    <b-modal id="select-signer">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-signature fa-2x"></i>
            <span class="bold">@lang('Seleccionar el Firmante')</span>
        </template>
        
        {{-- El contenido de la modal --}}
        <div>
            <label for="signer" class="bold">
                @lang('Lista de firmantes del documento')
            </label>
            <select id="signer" class="form-control">
                @foreach ($signers as $signer)
                    {{-- El creador del documento no tiene token de acceso porque es innecesario --}}
                    @if ($signer->token)
                    <option value="{{$signer->id}}" data-name="{{$signer->name}} {{$signer->lastname}}" 
                        data-email="{{$signer->email}}" data-phone="{{$signer->phone}}" data-is-creator="0">
                    @else
                    <option value="{{$signer->id}}" data-name="{{$signer->name}} {{$signer->lastname}}" 
                        data-email="{{$signer->email}}" data-phone="{{$signer->phone}}" data-is-creator="1">
                    @endif
                        @if ($signer->lastname || $signer->name)
                        {{$signer->lastname}} {{$signer->name}}
                        @elseif ($signer->email)
                        {{$signer->email}} 
                        @elseif ($signer->phone)
                        {{$signer->phone}}
                        @endif
                    </option>
                @endforeach
            </select>
        </div>
        
        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="insertSign" variant="success" autofocus>@lang('Aceptar')</b-button>
            <b-button @click.prevent="cancel" variant="danger">@lang('Cancelar')</b-button>
        </template>

    </b-modal>
</template>