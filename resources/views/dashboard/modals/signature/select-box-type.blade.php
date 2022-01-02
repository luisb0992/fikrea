<template>
    <b-modal id="select-box-type">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-signature fa-2x"></i>
            <span class="bold">@lang('Seleccionar el tipo de caja de texto')</span>
        </template>
        
        {{-- El contenido de la modal --}}
        <div class="text-center">
            <table>
                <tbody>
                    <tr>
                        <td>
                            <b-form-checkbox size="lg" v-model="box.type" value="1">
                            </b-form-checkbox>
                        </td>
                        <td>
                            <div draggable="true" id="box-initials" data-type="1" class="box">@lang('Iniciales')</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b-form-checkbox size="lg" v-model="box.type" value="2">
                            </b-form-checkbox>
                        </td>
                        <td>
                            <div draggable="true" id="box-fullname" data-type="2" class="box">@lang('Nombre completo')</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b-form-checkbox size="lg" v-model="box.type" value="3">
                            </b-form-checkbox>
                        </td>
                        <td>
                            <div draggable="true" id="box-id"       data-type="3" class="box">@lang('# Identificación')</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b-form-checkbox size="lg" v-model="box.type" value="4">
                            </b-form-checkbox>
                        </td>
                        <td>
                            <div draggable="true" id="box-free"     data-type="4" class="box">@lang('Texto libre')</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b-form-checkbox size="lg" v-model="box.type" value="5">
                            </b-form-checkbox>
                        </td>
                        <td>
                            <div draggable="true" id="box-check"    data-type="5" class="box">@lang('Casilla de verificación')</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b-form-checkbox size="lg" v-model="box.type" value="6">
                            </b-form-checkbox>
                        </td>
                        <td>
                            <div draggable="true" id="box-select"   data-type="6" class="box">@lang('Lista de opciones')</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="insertBox" variant="success" autofocus>@lang('Aceptar')</b-button>
            <b-button @click.prevent="cancel" variant="danger">@lang('Cancelar')</b-button>
        </template>

    </b-modal>
</template>