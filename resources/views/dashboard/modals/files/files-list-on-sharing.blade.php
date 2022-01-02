{{--
	Muestra el listado de todos los archivos que se van a compartir en caso de que
	sean más de 10
--}}
<template>
    <b-modal
        size="xl"
        scrollable
        id="files-list-on-sharing">
        
        {{-- El encabezado de la modal --}}
        <template #modal-header>
            <i class="fas fa-list fa-2x"></i>
            <span class="bold">@lang('Listado de archivos que se han seleccionado')</span>
        </template>
        
        {{-- El contenido de la modal --}}
        
        <div class="mt-2">
            {{-- Lista de archivos que se van a compartir --}}
            <div class="col-md-12">
                <div class="main-card mb-3 card">
                    <div class="card-body"><h5 class="card-title">@lang('Lista de Archivos')</h5>
                        <div class="table-responsive">    
                            <table class="mb-0 table table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('Nombre')</th>
                                        <th>@lang('Tipo')</th>
                                        <th class="text-center">@lang('Tamaño')</th>
                                        <th>@lang('Carpeta')</th>
                                        <th>@lang('Creado')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach( $files ?? [] as $file )
                                    <tr>
                                        <th data-label="@lang('Archivo') #">{{ $loop->iteration }}</th>
                                        <td>
                                            <a href="#">{{ $file->name }}</a>
                                        </td>
                                        <td data-label="@lang('Tipo')">
                                            @include('dashboard.partials.file-icon', ['type' => $file->type])
                                        </td>
                                        <td class="text-center" data-label="@lang('Tamaño')">@filesize($file->size)</td>
                                        <td>{{ implode('/', $file->full_path ?? []) }}</td>
                                        <td class="align-middle text-nowrap" data-label="@lang('Creado')">
                                            @date($file->created_at)
                                            <div class="text-info">
                                                @time($file->created_at)
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>@lang('Nombre')</th>
                                        <th>@lang('Tipo')</th>
                                        <th class="text-center">@lang('Tamaño')</th>
                                        <th>@lang('Carpeta')</th>
                                        <th>@lang('Creado')</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{--/ Lista de archivos que se van a compartir --}}
        </div>
        
        {{-- El pié de la modal --}}
        <template #modal-footer="{cancel}">
            <b-button @click.prevent="cancel()" variant="success">@lang('Cerrar')</b-button>
        </template>

    </b-modal>
</template>