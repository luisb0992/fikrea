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
    @lang('Cambie los datos de la suscripción')
    <div class="page-title-subheading">
         @lang('Puede fijar un espacio de almacenamiento personalizado, cambiar la fecha límite o el plan')
    </div>
</div>
@stop

{{-- 
    El contenido de la página
--}}
@section('content')
<div v-cloak id="app" class="col-md-12 pr-0">

    <form id="subscription" data-subscription="@json($subscription)" data-user="@json($subscription->user)" action="@route('backend.subscription.save', ['id' => $subscription->id])" method="post">

        @csrf

        <input type="hidden" id="id" v-model="subscription.id" />

        {{-- Los botones de Acción --}}
        <div class="col-md-12 mb-4">
            <div class="input-group">
                <button type="submit" class="btn btn-lg btn-success mr-1">@lang('Guardar')</button>
                <a href="@route('backend.subscriptions.list')" class="btn btn-lg btn-danger">@lang('Cancelar')</a>
            </div>
        </div>
        {{--/Los botones de Acción --}}

        <div class="row col-md-12 mb-4 pr-0">

            {{-- Datos de la suscripción --}}
            <div class="col-md-6">
                <div class="main-card card">
                    <div class="card-body">
                        <h5 class="card-title">@lang('Datos de la Subscripción')</h5>

                            <div class="form-group">
                                <label for="plan">@lang('Plan')</label>
                                <select name="plan_id" id="plan_id" class="form-control" v-model="subscription.plan_id">
                                    @foreach ($plans as $plan)
                                    <option value="{{$plan->id}}">{{$plan->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="custom_disk_space">@lang('Almacenamiento Personalizado') Mb</label>
                                
                                @error('custom_disk_space')
                                    <input id="custom_disk_space" v-model="user.custom_disk_space" name="custom_disk_space" class="form-control is-invalid" type="text" />
                                    <div class="invalid-feedback">
                                        @lang('El almacenamiento debe ser un número en Mb')
                                    </div>
                                @else
                                    <input id="custom_disk_space" v-model="user.custom_disk_space" name="custom_disk_space" class="form-control" type="text" />
                                @enderror

                                <div class="small text-info">
                                    @lang('Si se deja vacio, el espacio de almacenamiento es el que corresponde al plan actual (:diskspace Mb)', ['diskspace' => $subscription->plan->disk_space])
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="starts_at">@lang('Fecha Inicio')</label>
                                <vuejs-datepicker 
                                    :language="datepickerLanguage"
                                    format="dd-MM-yyyy" input-class="datepicker" placeholder="@lang('Ejemplo: 01-01-2010')"
                                    id="starts_at" v-model="subscription.starts_at" name="starts_at">
                                </vuejs-datepicker>
                            </div>

                            <div class="form-group">
                                <label for="ends_at">@lang('Fecha Fin')</label>
                                <vuejs-datepicker 
                                    :language="datepickerLanguage"
                                    format="dd-MM-yyyy" input-class="datepicker" placeholder="@lang('Ejemplo: 01-01-2010')"
                                    id="ends_at" v-model="subscription.ends_at" name="ends_at">
                                </vuejs-datepicker>
                                @error('ends_at')
                                    <div class="invalid-feedback d-block">
                                        @lang('La fecha de finalización de la suscripción no es válida')
                                    </div>
                                @enderror        
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            {{--/Datos de la suscripción --}}
            
            {{-- Datos del cliente --}}
            <div class="col-md-6">
                <div class="main-card card">
                    <div class="card-body">
                        <h5 class="card-title">@lang('Datos del Cliente')</h5>

                        <form>

                            <div class="form-group">
                                <label for="name">@lang('Nombre')</label>
                                <input readonly id="name" name="name" class="form-control" type="text" value="{{$subscription->user->name}}" />
                            </div>

                            <div class="form-group">
                                <label for="lastname">@lang('Apellidos')</label>
                                <input readonly id="lastname" name="lastname" class="form-control" type="text" value="{{$subscription->user->lastname}}" />
                            </div>

                            <div class="form-group">
                                <label for="email">@lang('Email')</label>
                                <input readonly id="email" name="email" class="form-control" type="text" value="{{$subscription->user->email}}" />
                            </div>

                            <div class="form-group">
                                <label for="phone">@lang('Teléfono')</label>
                                <input readonly id="phone" name="phone" class="form-control" type="text" value="{{$subscription->user->phone}}" />
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            {{--/Datos del cliente --}}
        </div>
    </form>
@stop

{{-- Los scripts personalizados --}}
@section('scripts')
<script src="@mix('assets/js/backend/subscription.js')"></script>
@stop