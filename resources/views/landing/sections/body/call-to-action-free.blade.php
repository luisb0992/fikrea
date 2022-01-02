<div class="row justify-content-center align-self-baseline">
    <div class="col col-md-6">
        <div class="text-center w-100">
            <h4>@lang('Puedes usar :app gratis y sin registros', ['app' => config('app.name')])</h4>
            @guest
            {{-- Si es el usuario invitado --}}
            <a href="@route('dashboard.profile')" class="btn btn-warning btn-block p-4 font-weight-bold text-center">
            @else
            {{-- Si es un usuario registrado --}}
            <a href="@route('dashboard.home')" class="btn btn-warning btn-block p-4 font-weight-bold text-center">
            @endguest  
            <span class="text-btn-responsive">@lang('¡Pulsa aquí para comenzar!')</span>
            </a>
        </div>
    </div>
</div>