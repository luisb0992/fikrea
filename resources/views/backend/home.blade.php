@extends('backend.layouts.main')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
<link rel="stylesheet" href="/assets/css/backend/home.css" />
@stop

{{--
    El encabezado con la ayuda para la página
--}}
@section('help')
<div>
    @lang('Bienvenido a la herramienta de administración de :app', ['app' => config('app.name')])
    <div class="page-title-subheading">
        @lang('Se muestran las estadísticas para el día de hoy')
        @date
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')

{{-- Contadores de usuarios para el día de hoy --}}
<div class="row col-md-12 ml-2">
    <div class="col-md-4">
        <div class="card mb-3 widget-content bg-midnight-bloom">
            <div class="widget-content-wrapper text-white">
                <div class="widget-content-left">
                    <div class="widget-heading">@lang('Usuarios Nuevos')</div>
                    <div class="widget-subheading">@lang('Usuarios sin registro que han accedido')</div>
                </div>
                <div class="widget-content-right">
                    <div class="widget-numbers text-white"><span>{{$analytical->usersToday->count()}}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-3 widget-content bg-arielle-smile">
            <div class="widget-content-wrapper text-white">
                <div class="widget-content-left">
                    <div class="widget-heading">@lang('Usuarios Registrados Nuevos')</div>
                    <div class="widget-subheading">@lang('Usuarios que han creado una nueva cuenta')</div>
                </div>
                <div class="widget-content-right">
                    <div class="widget-numbers text-white"><span>{{$analytical->registeredToday->count()}}</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card mb-3 widget-content bg-grow-early">
            <div class="widget-content-wrapper text-white">
                <div class="widget-content-left">
                    <div class="widget-heading">@lang('Pagos Realizados')</div>
                    <div class="widget-subheading">@lang('Usuarios que han realizado un pago (Clientes)')</div>
                </div>
                <div class="widget-content-right">
                    <div class="widget-numbers text-white"><span>{{$analytical->payedToday->count()}}</span></div>
                </div>
            </div>
            <div class="widget-content-right">
                <div class="widget-numbers text-white">
                    <span>{{$analytical->billingToday}}</span> @config('app.currency')
                </div>
            </div>
        </div>
    </div>
</div>
{{--/Contadores de usuarios para el día de hoy --}}

{{-- Lista de usuarios invitados hoy --}}
<div class="col-md-12 table-responsive">
    <div class="main-card mb-3 card">
        <div class="card-header">
            <h5 class="m-0 p-0">
                @lang('Usuarios sin Registro')
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">    
                <table class="mb-0 table table-striped">
                    <thead>
                        <tr>
                            <th>@lang('Dirección de Correo')</th>
                            <th>@lang('Fecha')</th>
                            <th>@lang('Espacio en Disco')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($analytical->usersToday as $user)
                        <tr>
                            <td data-label="@lang('Usuario')">
                                <a href="mailto: {{$user->email}}">
                                    {{$user->email}}
                                </a>
                            </td>
                            <td data-label="@lang('Creado')">
                                @datetime($user->created_at)
                            </td>
                            <td  data-label="@lang('Tamaño')">
                                @filesize($user->diskSpace->used)
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">@lang('No hay registros')</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{--/Lista de usuarios invitados hoy --}}


{{-- Lista de usuarios registrados hoy --}}
<div class="col-md-12 table-responsive">
    <div class="main-card mb-3 card">
        <div class="card-header">
            <h5 class="m-0 p-0">
                @lang('Usuarios Registrados')
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">    
                <table class="mb-0 table table-striped">
                    <thead>
                        <tr>
                            <th>@lang('Usuario')</th>
                            <th>@lang('Dirección de Correo')</th>
                            <th>@lang('Fecha')</th>
                            <th>@lang('Espacio Usado')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($analytical->registeredToday as $user)
                        <tr data-label="@lang('Usuario')">
                            <td>
                                {{$user->name}} {{$user->lastname}}
                            </td>
                            <td data-label="@lang('Dirección de Correo')">
                                <a href="mailto: {{$user->email}}">
                                    {{$user->email}}
                                </a>
                            </td>
                            <td data-label="@lang('Creado')">
                                @datetime($user->created_at)
                            </td>
                            <td data-label="@lang('Espacio Usado')">
                                @filesize($user->diskSpace->used)
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">@lang('No hay registros')</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{--/Lista de usuarios registrados hoy --}}


{{-- Lista de pagos realizados hoy --}}
<div class="col-md-12">
    <div class="main-card mb-3 card">
        <div class="card-header">
            <h5 class="m-0 p-0">
                @lang('Pagos Realizados')
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">    
                <table class="mb-0 table table-striped">
                    <thead>
                        <tr>
                            <th>@lang('Usuario')</th>
                            <th>@lang('Dirección de Correo')</th>
                            <th>@lang('Fecha')</th>
                            <th>@lang('Plan')</th>
                            <th>@lang('Importe')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($analytical->payedToday as $user)
                        <tr>
                            <td data-label="@lang('Usuario')">
                                {{$user->name}} {{$user->lastname}}
                            </td>
                            <td data-label="@lang('Email')">
                                <a href="mailto: {{$user->email}}">
                                    {{$user->email}}
                                </a>
                            </td>
                            <td data-label="@lang('Creado')">
                                @datetime($user->created_at)
                            </td>
                            <td data-label="@lang('Plan')">
                                {{$user->subscription->plan->name}}
                            </td>
                            <td data-label="@lang('Importe')">
                                {{$user->subscription->payment}} @config('app.currency')
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">@lang('No hay registros')</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{--/Lista de pagos realizados hoy --}}

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
@stop