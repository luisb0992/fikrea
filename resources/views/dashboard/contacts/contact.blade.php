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
    @lang('Crear un Contacto')
    <div class="page-title-subheading">
        @lang('Crear y almacenar sus contactos más habituales le ahorra trabajo a la hora de compartir sus documentos')
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

    {{-- Datos del Contacto --}}
    <div class="main-card mb-3 card offset-md-3 col-md-6">
        <div class="card-body">
            <h5 class="card-title">@lang('Datos del Contacto')</h5>
            
            <form id="form-contact" method="post" action="@route('dashboard.contact.save')">

                @csrf
                
                <input type="hidden" id="id" name="id" value="@exists($contact->id)" />

                <div class="form-row">
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <label for="name">@lang('Nombre')</label>
                            @error('name')
                            <input value="@old('name')" name="name" id="name" type="text" class="form-control is-invalid" />
                            <div class="invalid-feedback">{{$message}}</div>
                            @else
                                @if ($contact)
                                <input value="@exists($contact->name)" name="name" id="name" type="text" class="form-control" autocomplete="off" autofocus />
                                @else
                                <input value="@old('name')" name="name" id="name" type="text" class="form-control" autocomplete="off" autofocus />
                                @endif
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="position-relative form-group">
                            <label for="lastname">@lang('Apellidos')</label>
                            @error('lastname')
                            <input value="@old('lastname')" name="lastname" id="lastname" type="text" class="form-control is-invalid" autofocus />
                            <div class="invalid-feedback">{{$message}}</div>
                            @else
                                @if ($contact)
                                <input value="@exists($contact->lastname)" name="lastname" id="lastname" type="text" class="form-control" />
                                @else
                                <input value="@old('lastname')" name="lastname" id="lastname" type="text" class="form-control" />
                                @endif
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-8">
                        <div class="position-relative form-group">
                            <label for="email">@lang('Dirección de Correo')</label>
                            @error('email')
                            <input value="@old('email')" name="email" id="email" type="text" class="form-control is-invalid" />
                            <div class="invalid-feedback">{{$message}}</div>
                            @else
                                @if ($contact)
                                <input value="@exists($contact->email)" name="email" id="email" type="text" class="form-control" />
                                @else
                                <input value="@old('email')" name="email" id="email" type="text" class="form-control" />               
                                @endif
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <label for="phone">@lang('Teléfono')</label>
                            @error('phone')
                            <input value="@old('phone')" name="phone" id="phone" type="text" class="form-control is-invalid" />
                            <div class="invalid-feedback">{{$message}}</div>
                            @else

                                @if ($contact)
                                <input value="@exists($contact->phone)" name="phone" id="phone" type="text" class="form-control" />
                                @else
                                <input value="@old('phone')" name="phone" id="phone" type="text" class="form-control" />
                                @endif
                            
                            @enderror
                        </div>
                    </div>

                </div>

                <div class="form-row">

                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <label for="dni">@lang('DNI')</label>
                            @error('dni')
                            <input value="@old('dni')" name="dni" id="dni" type="text" class="form-control is-invalid" />
                            <div class="invalid-feedback">{{$message}}</div>
                            @else
                            
                                @if ($contact)
                                <input value="@exists($contact->dni)" name="dni" id="dni" type="text" class="form-control" />
                                @else
                                <input value="@old('dni')" name="dni" id="dni" type="text" class="form-control" />
                                @endif
                                
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <label for="company">@lang('Compañía')</label>
                            @error('company')
                            <input value="@old('company')" name="company" id="company" type="text" class="form-control is-invalid" />
                            <div class="invalid-feedback">{{$message}}</div>
                            @else
                            
                                @if ($contact)
                                <input value="@exists($contact->company)" name="company" id="company" type="text" class="form-control" />
                                @else
                                <input value="@old('company')" name="company" id="company" type="text" class="form-control" />
                                @endif
                                
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="position-relative form-group">
                            <label for="phone">@lang('Cargo')</label>
                            @error('position')
                            <input value="@old('position')" name="position" id="position" type="text" class="form-control is-invalid" />
                            <div class="invalid-feedback">{{$message}}</div>
                            @else
                            
                                @if ($contact)
                                <input value="@exists($contact->position)"name="position" id="position" type="text" class="form-control" />
                                @else
                                <input value="@old('position')"name="position" id="position" type="text" class="form-control" />
                                @endif
                            
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
    {{-- Datos del Contacto --}}


@stop

{{-- Los scripts personalizados --}}
@section('scripts')
@stop