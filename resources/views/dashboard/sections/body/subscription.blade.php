{{--
    Muestra el mensaje de alerta sobre una finalización próxima del periodo de suscripción
--}}

@if ($user->subscription->remainingDays <= 7)
{{-- Aviso que aparece hasta una semana antes --}}
<div class="alert alert-danger col-md-12 text-right">

    <div>
        <i class="fas fa-exclamation-circle"></i>
        @lang('Su período de suscripción <strong>:type</strong> está a punto de finalizar. Quedan sólo <strong>:days</strong> días para su renovación', [
            'type' => $user->subscription->plan->name,
            'days' => $user->subscription->remainingDays
        ])
    </div>

    @guest
        <div>
            @lang('Para realizar la renovación previamente deberá confirmar su cuenta')
        </div>

        <div class ="mt-1">
            <a href="@route('dashboard.register')" class="btn btn-lg btn-danger">
                <i class="fas fa-user-check"></i>
                @lang('Completar Registro')
            </a>
        </div>
    @else 
        <div class ="mt-1">
            <a href="@route('subscription.select')" class="btn btn-lg btn-danger">
                <i class="fas fa-shopping-cart"></i>
                @if ($user->subscription->plan->isTrial())
                    @lang('Contratar Ahora')
                @else
                    @lang('Renovar Ahora')
                @endif
            </a>
        </div>
    @endguest

</div>
@elseif ($user->subscription->remainingDays <= 30)
{{-- Aviso que aparece hasta 30 días antes --}}
<div class="alert alert-warning col-md-12 text-right">
    <div>
        <i class="fas fa-exclamation-circle"></i>
        @lang('Su período de suscripción <strong>:type</strong> finaliza en <strong>:days</strong> días', [
            'type' => $user->subscription->plan->name,
            'days' => $user->subscription->remainingDays
        ])
    </div>
    
    @guest
        <div>
            @lang('Para realizar la renovación previamente deberá confirmar su cuenta')
        </div>

        <div class ="mt-1">
            <a href="@route('dashboard.register')" class="btn btn-lg btn-warning">
                <i class="fas fa-user-check"></i>
                @lang('Completar Registro')
            </a>
        </div>
    @else
        <div class ="mt-1">
            <a href="@route('subscription.select')" class="btn btn-lg btn-warning">
                <i class="fas fa-shopping-cart"></i>
                @if ($user->subscription->plan->isTrial())
                    @lang('Contratar Ahora')
                @else
                    @lang('Renovar Ahora')
                @endif
            </a>
        </div>
    @endguest
</div>
@endif
