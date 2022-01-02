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
            @lang('Seleccione la carpeta hacia la que desea mover los archivos')
        </div>
    </div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')

    <div class="col-md-12">
        <div class="main-card mb-3 card offset-md-3 col-md-6">
            <div class="card-body">
                <h5 class="card-title">@lang('Datos de la Carpeta')</h5>

                <hr>

                <form id="folders-form" method="post" action="@route('dashboard.files.multiple-do-move')">
                    @csrf
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label for="parent_id">@lang('Mover Hacia la Carpeta')</label>
                                <select class="form-control @error('parent_id') is-invalid @enderror" id="parent_id"
                                        name="parent_id">
                                    <option value="">-- @lang('PRINCIPAL') --</option>
                                    @foreach( $folders as $parent )
                                        <option value="{{ $parent->id }}"
                                                @if( in_array($parent->id, $excluded, true)) ) disabled @endif>
                                            @for ($i = 0,$levels = count($parent->full_path ?? []); $i < $levels; $i++)
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                            @endfor
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="btn text-center col-12"
                             style="color: #000000; background-color: #ffc107; border-color: #CF8D2E; margin-right: 0.25rem !important;">
                            <i class="fas fa-folder-plus"></i>
                            @lang('Puede también moverlos directamente dentro de una nueva subcarpeta')
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label for="folder_name">@lang('Nombre de la nueva carpeta')</label>
                                <input autocomplete="off" autofocus
                                       class="form-control @error('folder_name') is-invalid @enderror" id="folder_name"
                                       name="folder_name" type="text"
                                       value="{{ old('folder_name', $file->folder_name ?? '') }}"/>
                                @error('folder_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label for="folder_notes">@lang('Notas de la nueva carpeta')</label>
                                <input autocomplete="off"
                                       class="form-control @error('folder_notes') is-invalid @enderror"
                                       id="folder_notes" name="folder_notes" type="text"
                                       value="{{ old('folder_notes', $file->folder_notes ?? '') }}"/>
                                @error('folder_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @foreach( $files as $file)
                        <input type="hidden" name="file[]" value="{{ $file->id }}">
                    @endforeach
                    <div class="text-right">
                        <button type="submit" class="mt-2 btn btn-success">@lang('Mover a')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div v-cloak id="app" class="row no-margin col-md-12">

        {{-- Las modales necesarias --}}
        @include('dashboard.modals.files.files-list-on-sharing')
        {{-- /Las modales necesarias --}}

        {{-- El número máximo de firmantes que pueden ser seleccionados con el plan actual --}}
        <input type="hidden" id="max-users" value="{{$user->subscription->plan->signers}}"/>
        {{--/El número máximo de firmantes que pueden ser seleccionados con el plan actual --}}

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
                                <th>@lang('Creado')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($files->take(10) as $file)
                                <tr>
                                    <th data-label="@lang('Archivo') #">{{$loop->iteration}}</th>
                                    <td>
                                        <a href="#">{{$file->name}}</a>
                                    </td>
                                    <td data-label="@lang('Tipo')">
                                        @include('dashboard.partials.file-icon', ['type' => $file->type])
                                    </td>
                                    <td class="text-center" data-label="@lang('Tamaño')">@filesize($file->size)</td>

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
                                <th>@lang('Creado')</th>
                            </tr>
                            </thead>
                        </table>
                        @if ($files->count() > 10)
                            <div class="text-center mb-1 mt-1" @click.prevent="showAllFiles">
                                <button class="btn btn-primary">
                                    + ( {{$files->count()-10}} ) @lang('archivos')
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {{--/ Lista de archivos que se van a compartir --}}
    </div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
    <script src="@mix('assets/js/dashboard/files/file-share.js')"></script>
@stop