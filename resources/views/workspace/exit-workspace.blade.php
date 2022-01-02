@extends('workspace.layouts.no-menu-no-navbar')

{{-- Título de la Página --}}
@section('title', 'WorkSpace')

{{-- El contenido de la página --}}
@section('content')
    <div class="col-md-12">
        <div class="offset-md-3 col-md-6 col-s-12 main-card mb-3 card">
            <div class="card-body">
                <div class="container">
                    <div class="text-center">
                        <i class="fas fa-info-circle fa-9x text-info"></i>
                    </div>
                    <div class="text-info bold text-center">
                       <h1>@lang('Ha salido de su espacio de trabajo')</h1>
                    </div>
                    <div class="text-center bold mt-4 mb-4">
                        @lang('Gracias por visitar :app', ['app' => config('app.name')]).
                        <p>@lang('Puede ver más de nosotros en <a href=":url">:url</a>', ['url' => config('app.url')])</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop