@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
    <div>
        @lang('Estos son sus archivos')
        <div class="page-title-subheading">
            @lang('Listado de archivos que ha seleccionado para firmarlos, revise que sean firmables.')
        </div>
    </div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')
<div id="app" v-cloak class="row no-margin col-md-12">

    {{--Data que le pasamos a Vue JS--}}
    <div id="data"
        data-files="@json($files)"
        data-request-sign-multiple="@route('dashboard.files.multiple.sign.save')"
        data-request-select-signers="@route('dashboard.document.signers', ['id'=>'X'])"
        data-request-save-file-info="@route('dashboard.files.multiple.info.save', ['id'=>'X'])"
        data-message-document-created="@lang('Se ha generado el documento a partir de todos los archivos que usted ha seleccionado')"
        data-message-invalid-form="@lang('Debe completar los datos requeridos en el formulario')"
    ></div>
    {{--/Data que le pasamos a Vue JS--}}

    {{-- Los botones de Acción --}}
    @include('dashboard.files-multiple.action-buttons')
    {{--/Los botones de Acción --}}

    {{--Formulario para completar el proceso--}}
    <div v-if="show" class="col-md-10">
        <div class="main-card mb-3 card">
            <div class="card-body">
                
                <h5 class="card-title">
                    @lang('Por favor complete los siguientes campos para completar este proceso')
                </h5>

                <hr>

                {{-- Formulario donde se debe meter nombre del archivo y carpeta donde se quiere ubicar --}}
                <b-form>

                    {{-- El nombre del archivo --}}
                    <b-form-group id="name-file" label="@lang('Nombre del archivo generado *')" label-for="name-file">
                        
                        <b-form-input 
                                id="name-file-input"
                                placeholder="@lang('Nombre del archivo')"
                                size="lg"
                                v-model="$v.file.name.$model"
                                :state="validateState('name')"
                                aria-describedby="input-1-live-feedback"
                        ></b-form-input>

                        <b-form-invalid-feedback id="input-1-live-feedback">
                            @lang('Debe introducir el nombre del archivo')
                        </b-form-invalid-feedback>

                    </b-form-group>
                    {{-- El nombre del archivo --}}

                    {{-- El location del archivo --}}
                    <b-form-group id="location-file" label="@lang('Mover archivo para')" label-for="location-file">

                        <select class="form-control"
                            size="lg"
                            id="parent_id"
                            v-model="file.location"
                        >
                            <option value="">-- @lang('PRINCIPAL') --</option>
                            @foreach( $folders as $parent )
                                <option value="{{ $parent->id }}">
                                    @for ($i = 0,$levels = count($parent->full_path ?? []); $i < $levels; $i++)
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                    @endfor
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                             
                    </b-form-group>
                    {{-- El location del archivo --}}

                </b-form>
                {{-- /Formulario donde se debe meter nombre del archivo y carpeta donde se quiere ubicar --}}

            </div>
        </div>
    </div>
    {{--/Formulario para completar el proceso--}}

    {{-- Lista de archivos que se van a firmar --}}
    <div v-else class="col-md-12">
        <div class="main-card mb-3 card">
            <div class="card-body">
                <h5 class="card-title">
                    @lang('Lista de archivos seleccionados')
                    @if ($files->filter(fn($file) => $file['signable'] == false)->count() > 0)
                    <div class="mt-2 p-2">
                        <span class="text-warning bold">
                            <i class="fa fa-error"></i>
                            @lang('Los archivos que no se pueden firmar no se tendrán en cuenta !')
                        </span>
                    </div>
                    @endif
                </h5>
                
                <div class="table-responsive">    
                    <table class="mb-0 table table-striped">
                        <thead>
                            @include('dashboard.files-multiple.header-files-table')
                        </thead>
                        <tbody>
                            @foreach($files->toArray() as $file)
                                <tr data-file-id="{{$file['id']}}">
                                    <th data-label="@lang('Archivo') #">{{$loop->iteration}}</th>
                                    <td>
                                        <a href="#">{{$file['name']}}</a>
                                    </td>
                                    <td data-label="@lang('Tipo')">
                                        @include('dashboard.partials.file-icon', ['type' => $file['type']])
                                    </td>
                                    <td class="text-center" data-label="@lang('Tamaño')">@filesize($file['size'])</td>
                                    <td>{{ implode('/', $file['full_path'] ?? []) }}</td>
                                    <td data-label="@lang('Creado')" class="text-nowrap">
                                        @date($file['created_at'])
                                        <div class="text-info">
                                            @time($file['created_at'])
                                        </div>
                                    </td>
                                    <td data-label="@lang('Firmable')">
                                        @if ($file['signable'])
                                        <i class="text-success fa fa-2x fa-signature"
                                            data-toggle="tooltip" data-placement="top" 
                                            data-original-title="@lang('Archivo firmable')"
                                        ></i>
                                        @else
                                        <i class="text-danger fa fa-2x fa-signature"
                                            data-toggle="tooltip" data-placement="top" 
                                            data-original-title="@lang('Este archivo no se puede firmar')&nbsp;!!"
                                        ></i>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- Elimina el archivo --}}
                                        <a @click.prevent="removeFile('{{$file['name']}}')" href="#!"
                                           class="btn btn-danger square"
                                           data-toggle="tooltip" data-placement="bottom"
                                           data-original-title="@lang('Eliminar')">
                                        <i class="fa fa-trash"></i>
                                        </a>
                                        {{-- /Elimina el archivo --}}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            @include('dashboard.files-multiple.header-files-table')
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{--/ Lista de archivos que se van a firmar --}}

    {{-- Los botones de Acción --}}
    @include('dashboard.files-multiple.action-buttons')
    {{--/Los botones de Acción --}}

</div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')

{{--
    Toastr 
    @link https://github.com/CodeSeven/toastr 
--}}
<script src="@asset('assets/js/dashboard/vendor/toastr.min.js')"></script>

{{--
    Configuración de librerías javascript utilizadas
    Por ejemplo: toastr
--}}
<script src="@mix('assets/js/config/config.js')"></script>

{{-- Vuelidate plugin for vue --}}
<script src="@asset('assets/js/libs/vuelidate.min.js')"></script>
{{-- The builtin validators is added by adding the following line. --}}
<script src="@asset('assets/js/libs/validators.min.js')"></script>

<!-- Load Vue followed by BootstrapVue -->
<script src="@asset('assets/js/vue/bootstrap-vue.js')"></script>

<script src="@mix('assets/js/dashboard/documents/sign-multiple.js')"></script>
@stop