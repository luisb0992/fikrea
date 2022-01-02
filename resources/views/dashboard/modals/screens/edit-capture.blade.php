<template>
    <b-modal id="edit-capture-screen">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-edit fa-2x"></i>
            <span class="bold">@lang('Editando grabación de pantalla')</span>
        </template>
        {{-- / El encabezado de la modal --}}
        
        {{-- El contenido de la modal --}}
        <div>

            {{-- El nombre del archivo de video --}}
            <b-form-group id="screen-name" label="@lang('Nombre del archivo de video')"
                label-for="screen-name">
                
                <b-form-input 
                    id="screen-name-input"
                    placeholder="@lang('Nombre del archivo')"
                    v-model="$v.editing.filename.$model"
                    :state="validateCaptureState('filename')"
                    aria-describedby="input-1-live-feedback"
                ></b-form-input>

                <b-form-invalid-feedback id="input-1-live-feedback">
                    @lang('Debe introducir un nombre para el archivo de video de 3 caracteres como mínimo').
                </b-form-invalid-feedback>

            </b-form-group>
            {{-- El nombre del archivo de video --}}

            <b-form-group id="screen-path" label="@lang('Destino del archivo de video')"
                label-for="screen-path">

                {{-- Selector de carpeta destino en nube del usuario --}}
                <treeselect
                    {{--v-model="fromFikreaCloud"--}}
                    v-model="editing.path"
                    :flat="true"
                    :options="optionsFikreaCloud"
                    placeholder="@lang('Seleccione destino de este archivo')..."
                />
                {{-- / Selector de carpeta destino en nube del usuario --}}

            </b-form-group>
            
        </div>
        {{-- /El contenido de la modal --}}

        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="cancel()" variant="secondary">
                @lang('Cancel')
            </b-button>
            <b-button @click.prevent="refreshCapture" variant="success">
                @lang('Actualizar')
            </b-button>
        </template>
        {{-- /El pié de la modal --}}

    </b-modal>
</template>