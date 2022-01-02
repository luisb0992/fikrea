@extends('dashboard.layouts.no-menu')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
@stop

{{-- 
    El contenido de la página
--}}
@section('content')
<div class="col-md-12">
    
    <div class="offset-md-3 col-md-6 col-s-12 main-card mb-3 card">

        <div class="card-body">

            <div class="container">

                {{-- El checkmark animado --}}
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
                {{--/El checkmark animado --}}

                <div class="text-success bold text-center">
                    @lang('El proceso de renovación de la suscripción se ha desarrollado con éxito')
                </div>
         
                <div class="text-center mt-4">
                    @lang('¡Ahora podrá seguir utilizando :app durante más tiempo!', 
                        ['app' => @config('app.name')]
                    )
                </div>
        
                <div class="text-center mt-4">
                    <a class="btn btn-lg btn-success" href="@route('dashboard.home')">@lang('Continuar')</a>
                </div>
            </div>

        </div>

    </div>

    {{-- Los detalles de la orden de pedido --}}
    <div class="offset-md-3 col-md-6 col-s-12 main-card mb-3 card">

        <div class="card-body">

            <div class="container">

                <ul class="list-group">

                    <li class="list-group-item list-group-item-primary">
                        <div class="row col-md-12">
                            <div class="col-md-12 bold text-center">
                                @lang('Subscripción por :months meses al plan <u>:plan</u>', 
                                    [
                                        'months' => $order->months,
                                        'plan'   => $order->plan->name,
                                    ]
                                )
                            </div>
                        </div>
                    </li>

                    <li class="list-group-item list-group-item-light">
                        <div class="row col-md-12">
                            <div class="col-md-6 bold text-right">@lang('Pedido') : </div>
                            <div class="col-md-6">{{$order->order}}</div>
                        </div>
                    </li>

                    <li class="list-group-item list-group-item-primary">
                        <div class="row col-md-12">
                            <div class="col-md-6 bold text-right">@lang('Importe Pagado') : </div>
                            <div class="col-md-6">{{$order->subscription->payment}} @config('app.currency')</div>
                        </div>
                    </li>

                    <li class="list-group-item list-group-item-light">
                        <div class="row col-md-12">
                            <div class="col-md-6 bold text-right">@lang('Fecha Fin') : </div>
                            <div class="col-md-6">{{$order->subscription->ends_at->format('d-m-Y')}}</div>
                        </div>
                    </li>
                </ul>
            </div>

            @if ($order->isApproved())
            <div class="col-md-12 col-s-12 text-right text-primary mt-1">
                <i class="fab fa-cc-paypal fa-2x"></i>
                <span class="fa-2x-text">                
                    @lang('La transacción ha sido verificada')
                </span>
            </div>
            @else
            <div class="col-md-12 col-s-12 text-right text-danger mt-1">
                <i class="fa fa-times fa-2x"></i>
                <span class="fa-2x-text">                
                    @lang('La transacción no ha sido verificada')
                </span>
            </div>
            @endif

        </div>
        {{--/Los detalles de la orden de pedido --}}

    </div>

    {{-- Logo --}}
    <div class="col-s-12 col-md-9 text-right mb-3">
        <a href="@route('landing.home')">
            <img aria-hidden="true" target="_blank" src="@asset('/assets/images/dashboard/logos/fikrea-medium-logo.png')" alt="">
        </a>
    </div>
    {{--/Logo --}}

</div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
@stop