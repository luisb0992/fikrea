@extends('dashboard.layouts.no-menu')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
<link rel="stylesheet" href="@mix('assets/css/dashboard/subscription/mustberenew.css')" />
@stop

{{-- 
    El contenido de la página
--}}
@section('content')
<video autoplay muted loop>
    <source src="@asset('assets/media/landing/video/office-work.mp4')" />
</video>
<div class="subscription">
    <div class="alert alert-danger col-md-12 text-center">
        <div>
            @lang('No se preocupe sus datos están a salvo, pero su suscripción a :app ha terminado',
                [
                    'app'  => config('app.name'),
                ]
            )
        </div>
        <div>
            @lang('Para continuar utilizando la aplicación debe proceder a la renovación de su suscripción actual')
        </div>
        <div class="mt-4">
            <a href="@route('subscription.select')" class="btn btn-lg btn-warning">
                <i class="fas fa-shopping-cart"></i>
                @if ($user->subscription->plan->isTrial())
                    @lang('Contratar Ahora')
                @else
                    @lang('Renovar Ahora')
                @endif
            </a>
        </div>
    </div>

    {{-- Logo --}}
    <div class="col-s-12 col-md-12 text-right">
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