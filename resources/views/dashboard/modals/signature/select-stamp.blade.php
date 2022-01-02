<template>
    <b-modal id="select-stamp">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-stamp fa-2x"></i>
            <span class="bold">@lang('Seleccionar el sello a utilizar')</span>
        </template>
        {{--/El encabezado de la modal --}}

        {{-- El contenido de la modal --}}      
        <b-tabs content-class="mt-3">
            
            {{-- Los sellos que ha subido el usuario --}}
            <b-tab title="@lang('Mis Sellos')" active>
                {{-- Si no hay sellos del usuarios se muestra una ayuda --}}
                <div v-if="!config.stamps.length">
                    <div class="text-info ml-2">
                        <em>
                            @lang('Puede subir y utilizar sus propios sellos o escoger uno de la biblioteca')
                        </em>
                    </div>
                </div>
                {{--/Si no hay sellos del usuarios se muestra una ayuda --}}

                <ul v-for="stamp in config.stamps" class="list-group">
                    <li class="list-group-item mt-1">
                        <div class="text-primary bold text-truncate">
                            <i class="fas fa-stamp text-info"></i>
                            @{{stamp.name}}
                        </div>
                        <div class="text-secondary small">
                            @{{stamp.width}}x@{{stamp.height}}
                            <span class="bold">@{{stamp.type}}</span>
                            <span class="text-info">@{{stamp.created_at|date}}</span>
                        </div>
                        <div class="text-center m-1">
                            <img :src="stamp.thumb" :alt="stamp.name" class="thumb" />
                        </div>
                        <div class="text-right mt-2">
                            {{-- Eliminar el sello --}}
                            <button type="button" @click.prevent="removeStamp(stamp)" class="btn btn-sm btn-danger">
                                <i class="fa fa-trash"></i>
                                @lang('Eliminar')
                            </button>
                            {{--/Eliminar el sello --}}
                            {{-- Insertar el sello --}}
                            <button type="button" @click.prevent="insertStamp(stamp)" class="btn btn-sm btn-success">
                                <i class="fas fa-stamp"></i>
                                @lang('Insertar')
                            </button>
                            {{--/Insertar el sello --}}
                        </div>
                    </li>
                </ul>
            </b-tab>
            {{--/Los sellos que ha subido el usuario --}}

            {{-- Los sellos que están incluidos en la biblioteca de forma predeterminada --}}
            <b-tab title="@lang('Biblioteca')">

                <ul class="list-group">
                    @foreach ($stamps as $stamp)
                    <li class="list-group-item mt-1">
                        <div class="text-primary bold text-truncate">
                            <i class="fas fa-stamp text-info"></i>
                            {{$stamp->name}}
                        </div>
                        <div class="text-secondary small">
                            {{-- El tipo mime de los sellos --}}
                            <span class="bold">{{$stamp->type}}</span>
                        </div>
                        <div class="text-center m-1">
                            <img class="stamp-library thumb" data-stamp-id="{{$stamp->id}}" src="@asset($stamp->path)" 
                                alt="{{$stamp->name}}" />
                        </div>
                        <div class="text-right mt-2">
                            {{-- Insertar el sello --}}
                            <button type="button" @click.prevent="selectStampFromLibrary({{$stamp->id}})" class="btn btn-sm btn-success">
                                <i class="fas fa-stamp"></i>
                                @lang('Insertar')
                            </button>
                            {{--/Insertar el sello --}}
                        </div>
                    </li>
                    @endforeach
                </ul>
            </b-tab>
            {{--/Los sellos que están incluidos en la biblioteca de forma predeterminada --}}

        </b-tabs>
        {{--/El contenido de la modal --}}   
        
        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <input id="stamp" type="file" accept="image/*" @change.prevent="uploadStamp" class="d-none" />
            <b-button @click.prevent="selectFileStamp" variant="primary">@lang('Subir Sello')</b-button>
            <b-button @click.prevent="cancel" variant="danger">@lang('Cancelar')</b-button>
        </template>

    </b-modal>
</template>