@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
    <div>
        @lang('Mover el Archivo o Carpeta')
        <div class="page-title-subheading">
            @lang('Mueva el archivo o la carpeta al interior de una carpeta diferente.')
        </div>
    </div>
@stop

{{--
    El contenido de la página
--}}
@section('content')
    {{-- El mensaje flash que se muestra cuando la operación ha tenido éxito --}}
    <div class="offset-md-3 col-md-6">
        @include('dashboard.sections.body.message-success')
        @include('dashboard.sections.body.message-error')
    </div>
    {{--/El mensaje flash que se muestra cuando la operacion ha tenido éxito --}}

    <div class="col-md-12">
        <div class="main-card mb-3 card offset-md-3 col-md-6">
            <div class="card-body">
                <h5 class="card-title">@lang('Datos de la Carpeta')</h5>

                <form id="folders-form" method="post" action="@route('dashboard.files.do-move', ['id' => $file->id])">
                    @csrf
                    @method('put')
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label for="parent_id">@lang('Mover Dentro De')</label>
                                <select class="form-control @error('parent_id') is-invalid @enderror" id="parent_id"
                                        name="parent_id">
                                    <option value="">-- @lang('PRINCIPAL') --</option>
                                    @foreach( $folders as $parent )
                                        <option value="{{ $parent->id }}"
                                                @if( (int) $file->parent_id === $parent->id ) selected @endif
                                                @if( (($parent->id  === $file->id) || array_key_exists($file->id, $parent->full_path ?? [])) ) disabled @endif>
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
                    <div class="text-right">
                        <button class="mt-2 btn btn-success">@lang('Guardar')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop