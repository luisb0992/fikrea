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
                <div class="text-danger bold text-center">
                    @lang('El proceso de renovación de la suscripción ha fallado o ha sido cancelado')
                </div>
         
                <div class="text-center mt-4">
                    @lang('Puede volver a intentar el pago en unos minutos').
                    @lang('Si el problema persiste puede contactar con el administrador')
                </div>
        
                <div class="text-center mt-4">
                    <a class="btn btn-lg btn-danger" href="@route('dashboard.home')">@lang('Continuar')</a>
                </div>
            </div>

        </div>

    </div>

    <div class="col-s-12 col-md-9 text-right">
        <a href="mailto:@config('app.contact')">
            @lang('Contactar con :contact', ['contact' => @config('app.contact')])
        </a>
    </div>

    {{-- Logo --}}
    <div class="col-s-12 col-md-9 text-right mt-1">
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