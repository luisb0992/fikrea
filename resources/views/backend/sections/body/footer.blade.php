<footer class="footer-dashboard">
    <div class="container">

        {{-- Contenido --}}

        {{-- Título --}}
        <div class="row">
            <h2 class="text-center col-md-12">
                <img class="img-fluid footer-logo" aria-hidden="true" target="_blank" src="@asset('/assets/images/dashboard/logos/fikrea-square-medium-logo.png')" alt="" />
                <div>@config('app.name')</div>
            </h2>
        </div>

        {{-- Social --}}
        <div class="row mt-4">
            <div class="text-center social col-md-12">
                <a href="https://www.instagram.com"><i class="icon ion-social-instagram"></i></a>
                <a href="https://www.whatsapp.com"><i class="icon ion-social-whatsapp"></i></a>
                <a href="https://www.twitter.com"><i class="icon ion-social-twitter"></i></a>
                <a href="https://www.facebook.com"><i class="icon ion-social-facebook"></i></a>
            </div>
        </div>

        {{-- Enlaces --}}
        <ul class="list-inline">
            <li class="list-inline-item"><a href="@route('landing.home')">@lang('Inicio')</a></li>
            <li class="list-inline-item"><a href="@route('dashboard.register')">@lang('Registrarse')</a></li>
            <li class="list-inline-item"><a href="@route('dashboard.login')">@lang('Acceder')</a></li>
        </ul>

        {{-- Copyright --}}
        <div class="row mt-4">
            <div class="col-md-12 text-center">
                <p>
                    Copyright &copy; <span>@year()</span> @config('company.name')
                    <i class="icon-heart" aria-hidden="true"></i>
                </p>
            </div>
        </div>

        {{-- Condiciones de Uso y Políticas Aplicacables al sitio web --}}
        <div class="row">

            <div class="col-md-12 text-center">
                <a href="#" data-toggle="modal" data-target="#modal-legal-warning">
                    @lang('Aviso Legal')
                </a>
                |
                <a href="#" data-toggle="modal" data-target="#modal-privacity-policy">
                    @lang('Política de Privacidad')
                </a>
                |
                <a href="#" data-toggle="modal" data-target="#modal-cookies-policy">
                    @lang('Política de Cookies')
                </a>
                |
                <a href="#" data-toggle="modal" data-target="#modal-return-policy">
                    @lang('Política de Devoluciones')
                </a>
                |
                <a href="#" data-toggle="modal" data-target="#modal-use-conditions">
                    @lang('Condiciones de Uso')
                </a>
            </div>
        </div>
    </div>

    <div class="mt-4"></div>
</footer>

{{-- Modales con los textos legales --}}

@include('landing.modals.legal.legal-warning')
@include('landing.modals.legal.privacity-policy')
@include('landing.modals.legal.cookies-policy')
@include('landing.modals.legal.return-policy')
@include('landing.modals.legal.use-conditions')

{{--/Modales con los textos legales --}}