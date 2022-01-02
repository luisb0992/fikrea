@extends('dashboard.layouts.no-menu')

{{-- Título de la Página --}}
@section('title', @config('app.name'))

{{-- Css Personalizado --}}
@section('css')
<link rel="stylesheet" href="@mix('/assets/css/dashboard/subscription/select.css')" />
@stop

{{--
    El contenido de la página
--}}
@section('content')

<div v-cloak id="app" class="col-md-12 pr-0">

    <form action="@route('subscription.payment')" method="post">

        @csrf

        <input type="hidden" id="plan" name="plan" :value="plan ? plan.id : 1"
            data-current-plan="{{$user->subscription->plan->id}}"
            data-remaining-days="{{$user->subscription->remainingDays}}"
            data-change-plan-price="{{$user->subscription->plan->change_price}}" />
        <div class="row col-md-12 pr-0">

            {{-- Muestra cada plan --}}
            @foreach($plans as $plan)
            <div class="col-md-6 plan p-0">
                <div class="card mb-3 widget-content">

                    {{-- El Plan actualmente elegido --}}
                    @if ($user->subscription->plan->id == $plan->id)
                    <div class="ribbon ribbon-top-left">
                        <span>@lang('Plan actual')</span>
                    </div>
                    @endif
                    {{--/El Plan actualmente elegido --}}

                    <div class="text-center">

                        {{-- El nombre del Plan --}}
                        <h2 class="heading">{{$plan->name}}</h2>
                        {{--/El nombre del Plan --}}

                        {{-- Texto descriptivo --}}
                        @switch ($plan->id)

                            @case (\App\Models\Plan::PREMIUM)
                                <span class="excerpt d-block">@lang('La mejor solución para Pymes')</span>
                                <span class="excerpt d-block">@lang('Autónomos, Startups')</span>
                                @break

                            @case (\App\Models\Plan::ENTERPRISE)
                                <span class="excerpt d-block">@lang('La mejor solución definitiva')</span>
                                <span class="excerpt d-block">@lang('para tu empresa')</span>
                                @break
                        @endswitch
                        {{--/Texto descriptivo --}}

                        {{-- Precios --}}
                        <span class="price">
                            <sup>@config('app.currency')</sup> 
                            <span class="number">{{$plan->monthly_price}}</span>
                        </span>

                        @if ($plan->monthly_price > 0)
                            <span class="excerpt d-block">
                                @lang('Pago Mensual')
                            </span>
                        @else
                            <span class="excerpt d-block">
                                @lang('Periodo de prueba de :trial dias', ['trial' => $plan->trial_period])
                            </span>

                            <span class="excerpt d-block pt-1">
                                @lang('Durante ese periodo podrá disfrutar de todas las características de la aplicación,
                                       sin restricciones y sin compromiso de permanencia')
                            </span>
                        @endif

                        @if ($plan->yearly_price > 0)
                            <span class="price">
                                <sup>@config('app.currency')</sup> 
                                <span class="number">{{$plan->yearly_price}}</span>
                            </span>
                            <span class="excerpt d-block">@lang('Pago Anual')</span>
                        @endif
                        {{--/Precios --}}

                        {{-- Características --}}
                        <h3 class="heading-2 mb-3">@lang('Todas las características')</h3>

                        <ul class="pricing-text mb-4">
                            <li><strong>{{$plan->disk_space}} MB</strong> @lang('Espacio de Usuario')</li>

                            @if ($plan->signers)
                                <li><strong>{{$plan->signers}}</strong> @lang('Firmantes/Documento')</li>
                            @else
                                <li><strong>&infin;</strong> @lang('Firmantes/Documento')</li>
                            @endif
                        </ul>

                        <ul class="text-left text-secondary">
                            <li>
                                @lang('Acceso ilimitado a procesos de compartición de archivos')  
                            </li>
                            <li>
                                @lang('Acceso ilimitado a procesos de requerimiento de documentos')
                            </li>
                            <li>
                                @lang('Acceso ilimitado a verificación de datos personales o empresariales')
                            </li>
                            <li>
                                @lang('Validaciones por Reconocimiento Facial, Grabación de Audio o Video a la persona que le solicites')
                            </li>
                        </ul>

                        {{--/Características --}}

                        {{-- Seleccionar el número de meses --}}
                        <select name="months" v-model="months" class="form-control">
                            <option value="1">
                                @lang('Un mes')
                            </option>
                            @for ($month = 2; $month <= 9; ++$month) 
                                <option value="{{$month}}">
                                    @lang(':month meses', ['month' => $month])
                                </option>
                            @endfor
                            <option value="12">
                                @lang('Un año')
                            </option>
                        </select>
                        {{--/Seleccionar el número de meses --}}

                        {{-- Seleccionar el Plan --}}

                        {{-- Si el plan actual es ENTERPRISE, sólo se permite el cambio a PREMIUM
                             cuando quedan menos de 30 días para la finalización de la suscripción,
                             con objeto de no perder sus prestaciones con anterioridad
                        --}}
                       
                        @if ($plan->id == \App\Models\Plan::PREMIUM 
                                                && 
                            $user->subscription->plan->id == \App\Models\Plan::ENTERPRISE
                                                &&
                            $user->subscription->remainingDays >= 30
                        )
                            <a href="#"
                                class="btn btn-danger disabled d-block px-3 py-3 mb-4 mt-4">
                                <i class="fas fa-ban"></i>
                                @lang('Seleccionar Plan')
                            </a>
                            <div class="text-danger">@lang('Podrá cambiar de plan 30 días antes de terminar su suscripción')</div>
                        @else
                            <a @click.prevent="select({{$plan->toJson()}})" href="#"
                                :class="plan && plan.id == {{$plan->id}} ? 'active' : ''"
                                class="btn btn-primary d-block px-3 py-3 mb-4 mt-4">
                                <i class="fas fa-check-double"></i>
                                @lang('Seleccionar Plan')
                            </a>
                            <div>
                                &nbsp;
                            </div>
                        @endif

                        {{--/Seleccionar el Plan --}}

                    </div>
                </div>
            </div>
            @endforeach
            {{--/Muestra cada plan --}}

        </div>

        {{-- Resumen de la Compra efectuada --}}
        <div v-show="amount != 0" class="offset-md-3 col-md-6 mb-3">
            <div class="card col-md-12 pt-2">

                <div class="col-md-12 text-center bold mt-2 mb-4">
                    <i class="fas fa-shopping-cart"></i>
                    @lang('Resumen de tu compra')
                </div>

                <div class="col-md-12">
                    <table class="table">
                        <thead>
                            <tr class="text-info bold">
                                <td>@lang('Precio Unidad')</td>
                                <td class="text-center">@lang('Unidades')</td>
                                <td class="text-right">@lang('Importe')</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    {{-- El coste unitario por la suscripción --}}
                                    <div>
                                        @{{price ? price : '0.00'}} @config('app.currency')
                                    </div>
                                    {{-- El coste unitario por ampliación de la suscripción --}}
                                    <div v-show="current_plan && current_plan.changeMonths">
                                        @{{current_plan ? current_plan.changePlanPrice : '0.00'}}
                                        @config('app.currency')
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div> x @{{units}} </div>
                                    <div v-show="current_plan && current_plan.changeMonths">
                                        x @{{current_plan ? current_plan.changeMonths : '0'}}
                                        @lang('cambio de plan')
                                    </div>
                                </td>
                                <td class="text-right">@{{amountExcludedTax}} @config('app.currency')</td>
                            </tr>
                            <tr>
                                <td class="text-uppercase">@lang('iva') ({{$plan->tax}}%)</td>
                                <td>&nbsp;</td>
                                <td class="text-right">@{{tax}} @config('app.currency')</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="text-uppercase">@lang('Total')</td>
                                <td></td>
                                <td class="text-right bold">@{{amount}} @config('app.currency')</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        {{--/Resumen de la Compra efectuada --}}

        <div class="row col-md-12">
            <div class="offset-md-2 col-md-8 mb-3">
                <button @click="pay" :disabled="amount == 0"
                    data-waiting-message="@lang('Conectando con la plataforma de pago')"
                    class="btn btn-warning btn-lg btn-select col-md-12">
                    <i class="fas fa-shopping-cart"></i>
                    @lang('Pagar')
                </button>
            </div>
        </div>
    </form>

    {{-- Botón de ayuda --}}
    <a href="@route('contact.show')" class="btn btn-info btn-contact" data-title="@lang('¿Necesitas ayuda? Haz click aquí') &nearr;">
        <i class="fas fa-headset fa-2x"></i>
    </a>
    {{--/Botón de ayuda --}}
    
</div>

@stop

{{-- Los scripts personalizados --}}
@section('scripts')
<script src="@mix('assets/js/dashboard/subscriptions/select.js')"></script>
@stop