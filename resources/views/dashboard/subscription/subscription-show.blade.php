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
    @lang('Puede ver el estado de su suscripción a :app y proceder a su renovación', ['app' => @config('app.name')])
    <div class="page-title-subheading">
        @lang('Se muestran los periodos contratados y le ofrecemos la posibilidad de descargar sus facturas')
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')
<div class="col-md-12 mb-3">

    {{-- Botón de Renovación --}}
    <div class="col-md-12 text-right mb-3">
        <a href="@route('subscription.select')" class="btn btn-lg btn-warning">
            <i class="fas fa-shopping-cart"></i>
            @lang('Renovar Subscripción')
        </a>
    </div>    
    {{--/Botón de Renovación --}}

    {{-- Muestra el período de la suscripción --}}
    <div class="col-md-12 text-right bold">
        @lang('Su período de suscripción :plan finaliza', ['plan' => $user->subscription->plan->name])
        <span class="text-secondary">
            @date($user->subscription->ends_at)
        </span>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-body">
            <div class="row">
                <div class="col-6 bold">
                    <i class="fas fa-angle-double-right"></i>
                    @date($user->subscription->starts_at)
                </div>
                <div class="col-6 text-right bold">
                    <i class="fas fa-flag-checkered"></i>
                    @date($user->subscription->ends_at)
                </div>
            </div>
            <div class="progress">
                <div class="progress-bar progress-bar-animated bg-primary progress-bar-striped" role="progressbar" 
                    aria-valuenow="{{$user->subscription->percentageStatus}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$user->subscription->percentageStatus}}%;">
                </div>
            </div>
        </div>
    </div>
    {{--/Muestra el periodo de la suscripción --}}

    {{-- La tabla con los pedidos realizados --}}
    <div class="col-md-12">
        <h5>@lang('Lista de Pedidos')</h5>
    </div>

    <div class="main-card mb-3 card">
        <div class="card-body">

        <div class="table-responsive row col-md-12">    
            <table class="mb-0 table table-striped">
                <thead>
                    <tr>
                        <th>@lang('Pedido')</th>
                        <th>@lang('Plan')</th>
                        <th class="text-center">@lang('Meses')</th>
                        <th class="text-right">@lang('Importe')</th>
                        <th class="text-center">@lang('Pagado')</th>
                        <th class="text-center">@lang('Factura')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($user->orders as $order)
                    <tr>
                        <td data-label="@lang('Pedido')">{{$order->order}}</td>
                        <td data-label="@lang('Plan')">
                            {{$order->plan->name}}
                        </td>
                        <td class="text-center" data-label="@lang('Meses')">{{$order->months}}</td>
                        <td class="text-right" data-label="@lang('Importe')">
                            {{$order->amount}}
                            @config('app.currency')
                        </td>
                        <td class="text-center" data-label="@lang('Pagado')">
                            @if ($order->payed_at)
                                @datetime($order->payed_at)
                            @else
                                <i class="fas fa-times text-danger"></i>  
                            @endif
                        </td>
                        <td class="text-center" data-label="@lang('Factura')">
                            <a href="@route('subscription.bill', ['order' => $order->id])" class="btn btn-warning square">
                                <i class="fas fa-file-invoice"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            @lang('No hay pedidos registrados')
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th>@lang('Pedido')</th>
                        <th>@lang('Plan')</th>
                        <th class="text-center">@lang('Meses')</th>
                        <th class="text-right">@lang('Importe')</th>
                        <th class="text-center">@lang('Pagado')</th>
                        <th class="text-center">@lang('Factura')</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    {{--/La tabla con los pedidos realizados --}}
</div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
@stop