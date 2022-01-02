{{-- Botones de accion para carousel y header del landing --}}
@auth

    {{-- visible a partir de sm --}}
    <span class="d-none d-sm-block mr-2">
        <a href="@route('dashboard.profile')" class="btn btn-warning p-3">@lang('Mí Perfil')</a>
    </span>

    {{-- visible en xs --}}
    <span class="d-block d-sm-none mt-2">
        <a href="@route('dashboard.profile')" class="btn btn-warning btn-block btn-lg">@lang('Mí Perfil')</a>
    </span>
@else

    {{-- visible a partir de sm --}}
    <span class="d-none d-sm-block mr-2">
        <a href="@route('dashboard.home')" class="btn btn-warning p-3">@lang('Úsalo Gratis')</a>
    </span>

    {{-- visible en xs --}}
    <span class="d-block d-sm-none mt-2">
        <a href="@route('dashboard.home')" class="btn btn-warning btn-block btn-lg">@lang('Úsalo Gratis')</a>
    </span>

@endauth

{{-- visible en a partir de sm --}}
<span class="d-none d-sm-block mr-2">
    <a href="#more-info" class="btn btn-info p-3">@lang('Más Información')</a>
</span>

{{-- visible en xs --}}
<span class="d-block d-sm-none mt-2">
    <a href="#more-info" class="btn btn-info btn-block btn-lg">@lang('Más Información')</a>
</span>
