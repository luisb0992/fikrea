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
    @lang('Puede Almacenar aquí sus contactos más habituales')
    <div class="page-title-subheading">
        @lang('Posteriormente le será más cómodo y rápido compartir sus documentos')
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
</div>
{{--/El mensaje flash que se muestra cuando la oepracion ha tenido éxito --}}

<div class="col-md-12">

    {{-- La lista de contactos --}}
    <div class="main-card mb-3 card">
        <div class="card-body">
            <h5 class="card-title">@lang('Lista de Contactos')</h5>

            {{-- Los botones con las acciones --}}
            <div class="mb-4">
                <div class="input-group" role="group">
                    <a href="@route('dashboard.contact.edit')" class="btn btn-lg btn-primary">
                        <i class="fas fa-user-plus"></i>
                        @lang('Nuevo Contacto')
                    </a>     
                </div>
            </div>
            {{-- /Los botones con las acciones --}}

            <div class="table-responsive">    
                <table class="mb-0 table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('Apellidos')</th>
                            <th>@lang('Nombre')</th>
                            <th>@lang('Dirección de Correo')</th>
                            <th>@lang('Teléfono')</th>
                            <th>@lang('Compañía')</th>
                            <th>@lang('Cargo')</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contacts as $contact)
                        <tr>
                            <th scope="row" data-label="@lang('Contacto') #">{{$loop->iteration}}</th>
                            <td data-label="@lang('Apellidos')">{{$contact->lastname}}</td>
                            <td data-label="@lang('Nom,bre')">{{$contact->name}}</td>
                            <td data-label="@lang('Email')">
                                <a href="mailto:{{$contact->email}}">{{$contact->email}}</a>
                            </td>
                            <td data-label="@lang('Teléfono')">
                                <a href="tel:{{$contact->phone}}">{{$contact->phone}}</a>
                            </td>
                            <td data-label="@lang('Compañía')">{{$contact->company}}</td>
                            <td data-label="@lang('Cargo')">{{$contact->position}}</td>
                            <td class="text-center">
                                <a href="@route('dashboard.contact.edit',  ['id' => $contact->id])" class="btn btn-warning square"
                                    data-toggle="tooltip" data-placement="top" 
                                    data-original-title="@lang('Editar el Contacto')">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <a href="@route('dashboard.contact.delete',['id' => $contact->id])" class="btn btn-danger square"
                                    data-toggle="tooltip" data-placement="top" 
                                    data-original-title="@lang('Eliminar el Contacto')">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">@lang('Ningún contacto registrado')</td>
                        </tr>
                        @endforelse
                    </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>#</th>
                            <th>@lang('Apellidos')</th>
                            <th>@lang('Nombre')</th>
                            <th>@lang('Dirección de Correo')</th>
                            <th>@lang('Teléfono')</th>
                            <th>@lang('Compañía')</th>
                            <th>@lang('Cargo')</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    {{--/ Ls lista de contactos --}}

    {{-- Control de la Tabla --}}
    <div class="control-wrapper">

        {{--Paginador --}}
        <div class="paginator-wrapper">
            {{$contacts->links()}}

            @lang('Se muestran :files de un total de :total contactos', [
                'files' => $contacts->count(),
                'total' => $contacts->total(),
            ])

        </div>
        {{--/Paginador --}}
 
    </div>
    {{--/Control de la Tabla --}}

</div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
@stop