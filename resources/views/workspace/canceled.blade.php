@extends('workspace.layouts.no-menu-no-navbar')

{{-- Título de la Página --}}
@section('title', 'WorkSpace')

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

                <div class="text-center">
                    <i class="fas fa-exclamation-triangle fa-9x text-danger"></i>
                </div>

                <div class="text-danger bold text-center">@lang('El proceso ha sido cancelado')</div>

                <div class="text-center bold mt-4 mb-4">
                    @lang('Sentimos que haya rechazado participar en este proceso').
                    @lang('Puede ponerse en contacto con su creador si desea realizarlo de nuevo')
                </div>
            </div>
            
        </div>
    </div>

</div>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
@stop