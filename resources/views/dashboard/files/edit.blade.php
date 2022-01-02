@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
    <div>
        @lang('Editar datos del archivo o carpeta')
        <div class="page-title-subheading">
            @lang('Edite los datos del archivo o carpeta')
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
                <h5 class="card-title">@lang('Datos del Archivo o Carpeta')</h5>

                <form id="folders-form" method="post" action="@route('dashboard.files.update', ['id' => $file->id])">
                    @csrf
                    @method('put')
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label for="name">@lang('Nombre')</label>
                                <input autocomplete="off" autofocus
                                       class="form-control @error('name') is-invalid @enderror" id="name"
                                       name="name" type="text" value="{{ old('name', $file->name ?? '') }}"/>
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @if( $file->is_folder )
                        <div class="form-row">
                            <div class="col-md-12">
                                <div class="position-relative form-group">
                                    <label for="notes">@lang('Notas')</label>
                                    <input autocomplete="off" class="form-control @error('notes') is-invalid @enderror"
                                           id="notes" name="notes" type="text"
                                           value="{{ old('notes', $file->notes ?? '') }}"/>
                                    @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif
                    <div class="text-right">
                        <button class="mt-2 btn btn-success">@lang('Guardar')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop