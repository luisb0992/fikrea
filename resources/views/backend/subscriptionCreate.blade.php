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
    @lang('Cree las suscripciones')
    <div class="page-title-subheading">
         @lang('Aquí podrá crear las suscripciones')
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
            <h5 class="card-title">@lang('Crear Suscripciones')</h5>

            <form action="@route('backend.subscriptions.subscriptionStore')" method="POST">
            	@csrf

            	<div class="form-row">
            		<div class="col-md-6">
	                    <div class="form-group">
	                        <label for="user_id">@lang('Usuario')</label>
			                <select type="text" class="form-control" name="user_id" id="user_id" required="">
			                    <option value="">@lang('Seleccione una opción...')</option>
			                    @foreach($users as $user)
			                        <option value="{{$user->id}}">{{$user->name}} {{$user->lastname}} ({{$user->email}})</option>   
			                    @endforeach
			                </select> 
	                    </div>
	                </div>


                    <div class="col-md-6">
	                    <div class="form-group">
	                        <label for="plan_id">@lang('Plan')</label>
	                        <select type="text" class="form-control" name="plan_id" id="plan_id" required="">
			                    <option value="">@lang('Seleccione una opción...')</option>
			                    @foreach($plans as $plan)
			                        <option value="{{$plan->id}}">{{$plan->name}}</option>   
			                    @endforeach
			                </select>
	                    </div>
                    </div>

                </div>

                <div class="form-row">

	                <div class="col-md-6">
		                <div class="form-group">
		                    <label for="payment">@lang('Pagado')</label>
		                    <input id="payment" name="payment" class="form-control" type="number" step="any" required/>
		                </div>
		            </div>

	                <div class="col-md-6">
		                <div class="form-group">
		                    <label for="payed_at">@lang('Fecha de Pago')</label>
		                    <input id="payed_at" name="payed_at" class="form-control" type="date" required/>
		                </div>
		            </div>
		        </div>


                <div class="form-row">

                	<div class="col-md-4">
	                    <div class="form-group">
	                        <label for="months">@lang('Meses')</label>
	                        <input id="months" name="months" class="form-control" type="number" required/>
	                    </div>
	                </div>

	                <div class="col-md-4">
		                <div class="form-group">
		                    <label for="starts_at">@lang('Fecha de Inicio')</label>
		                    <input id="starts_at" name="starts_at" class="form-control" type="date"required/>
		                </div>
		            </div>
		       
		            <div class="col-md-4">
		                <div class="form-group">
		                    <label for="ends_at">@lang('Fecha de Finalizacion')</label>
		                    <input id="ends_at" name="ends_at" class="form-control" type="date" required/>
		                </div>
		            </div>

                </div>

		        <button type="submit" class="btn btn-lg btn-success mr-1">@lang('Crear')</button>
		        <a href="@route('backend.subscriptions.list')" class="btn btn-lg btn-danger mr-1">@lang('Cancelar')</a>
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