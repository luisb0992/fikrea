@extends('dashboard.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
    <div>
        @lang('Crear Carpeta')
        <div class="page-title-subheading">
            @lang('Crear una carpeta para organizar sus archivos')
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

                <form id="folders-form" method="post" action="@route('dashboard.folders.store')">
                    @csrf
                    <div class="form-row">
                        <div class="col-md-12">
                            <div class="position-relative form-group">
                                <label for="parent_id">@lang('Carpeta Dentro De')</label>
                                <select class="form-control @error('parent_id') is-invalid @enderror" id="parent_id"
                                        name="parent_id">
                                    <option value="">-- @lang('PRINCIPAL') --</option>
                                    @foreach( $folders as $parent )
                                        <option value="{{ $parent->id }}"
                                                @if( (int) $file->parent_id === $parent->id ) selected @endif>
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
                    <div class="text-right">
                        <button class="mt-2 btn btn-success">@lang('Guardar')</button>
                    </div>
                </form>
            </div>
        </div>
@stop