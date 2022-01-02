@extends('backend.layouts.main')

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
    @lang('Actualiza los Planes para la Suscripcion')
    <div class="page-title-subheading">
         @lang('Puede actualizar los planes')
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')
{{-- Datos del plan --}}
<div class="col-md-12">
    <div class="main-card card">
        <div class="card-body">
            <h5 class="card-title">@lang('Editar Plan')</h5>

            <form action="@route('backend.plans.updatePlans', ['id' => $plan->id])" method="POST">
            	@csrf

            	<div class="form-row">
            		<div class="col-md-6">
	                    <div class="form-group">
	                        <label for="name">@lang('Nombre')</label>
	                        <input id="name" name="name" class="form-control" type="text" value="{{$plan->name}}" required/>
	                    </div>
	                </div>

                    <div class="col-md-6">
	                    <div class="form-group">
	                        <label for="disk_space">@lang('Espacio (MB)')</label>
	                        <input id="disk_space" name="disk_space" class="form-control" type="number" value="{{$plan->disk_space}}" required/>
	                    </div>
                    </div>

                </div>

                <div class="form-row">

                	<div class="col-md-4">
	                    <div class="form-group">
	                        <label for="signers">@lang('Firmantes')</label>
	                        <input id="signers" name="signers" class="form-control" type="number" value="{{$plan->signers}}" required/>
	                    </div>
	                </div>

	                <div class="col-md-4">
		                <div class="form-group">
		                    <label for="monthly_price">@lang('Precio Mensual')</label>
		                    <input id="monthly_price" name="monthly_price" class="form-control" type="number" step="any" value="{{$plan->monthly_price}}" required/>
		                </div>
		            </div>
		       
		            <div class="col-md-4">
		                <div class="form-group">
		                    <label for="yearly_price">@lang('Precio Anual')</label>
		                    <input id="yearly_price" name="yearly_price" class="form-control" type="number" step="any" value="{{$plan->yearly_price}}" required/>
		                </div>
		            </div>

                </div>

                <div class="form-row">

                	<div class="col-md-4">
		                <div class="form-group">
		                    <label for="change_price">@lang('Cambio de Precio')</label>
		                    <input id="change_price" name="change_price" class="form-control" type="number" step="any" value="{{$plan->change_price}}" required/>
		                </div>
		            </div>

	                <div class="col-md-4">
		                <div class="form-group">
		                    <label for="tax">@lang('IVA')</label>
		                    <input id="tax" name="tax" class="form-control" type="number" step="any" value="{{$plan->tax}}" required/>
		                </div>
		            </div>

	                <div class="col-md-4">
		                <div class="form-group">
		                    <label for="trial_period">@lang('Periodo de Prueba')</label>
		                    <input id="trial_period" name="trial_period" class="form-control" type="number" value="{{$plan->trial_period}}" required/>
		                </div>
		            </div>
		        </div>

		        <button type="submit" class="btn btn-lg btn-success mr-1">@lang('Editar')</button>

            </form>
        </div>
    </div>
</div>
{{--/Datos del cliente --}}
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
<script src="@mix('assets/js/backend/subscription.js')"></script>
@stop