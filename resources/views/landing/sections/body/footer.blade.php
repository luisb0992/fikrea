<footer class="ftco-footer ftco-bg-dark ftco-section">
    <div class="container mt-4">

        {{-- Contenido --}}
        <div class="row mb-5">
            <div class="col-md">
                <div class="ftco-footer-widget mb-4">

                    <div class="text-center">
                        <img aria-hiiden="true" src="@asset('/assets/images/dashboard/logos/fikrea-medium-logo.png')" alt="">
                    </div>

                    <p class="mt-4 bold text-center">
                        @lang('La herramienta más avanzada de firma digital')
                    </p>

                    <ul class="ftco-footer-social list-unstyled mt-5 text-center">
                        <li class="ftco-animate"><a href="#"><span class="icon-twitter"></span></a></li>
                        <li class="ftco-animate"><a href="#"><span class="icon-facebook"></span></a></li>
                        <li class="ftco-animate"><a href="#"><span class="icon-instagram"></span></a></li>
                        <li class="ftco-animate"><a href="#"><span class="icon-linkedin"></span></a></li>
                    </ul>

                </div>
            </div>

            <div class="col-md">

            </div>

            <div class="col-md">
                <div class="ftco-footer-widget mb-4">
                    <h2 class="ftco-heading-2">@lang('Menu')</h2>
                    <ul class="list-unstyled">
                        <li><a href="@route('dashboard.profile')" class="py-2 d-block">@lang('Úsalo gratis')</a></li>
                        <li><a href="@route('landing.home')#more-info" class="py-2 d-block">@lang('¿Qué es :app?', ['app' => config('app.name')])</a>
                        </li>
                        <li><a href="@route('dashboard.login')" class="py-2 d-block">@lang('Acceso')</a></li>
                        <li><a href="@route('dashboard.register')" class="py-2 d-block">@lang('Registrarse')</a></li>
                        <li><a href="@route('contact.show')" class="py-2 d-block">@lang('Contacto')</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="col-md">
                <div class="ftco-footer-widget mb-4">
                    <h2 class="ftco-heading-2">@lang('Contacto')</h2>
                    <div class="block-23 mb-3">
                        <ul>
                            <li><span class="icon icon-map-marker"></span>
                                <span class="text">
                                    @config('company.address.street')
                                    @config('company.address.zip')
                                    @config('company.address.city')
                                    @config('company.address.country')
                                </span>
                            </li>
                            <li>
                                <a href="tel:@config('company.contact.phone')">
                                    <span class="icon icon-phone"></span>
                                    <span class="text">@config('company.contact.phone')</span>
                                </a>
                            </li>
                            <li>
                                <a href="mailto:@config('app.app_mail')">
                                    <span class="icon icon-envelope"></span>
                                    <span class="text">@config('app.app_mail')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        {{-- /Contenido --}}

        {{-- Copyright --}}
        <div class="row">
            <div class="col-md-12 text-center">
                <p>
                    Copyright &copy; <span>@year()</span> @config('company.name')
                    <i class="icon-heart text-warning" aria-hidden="true"></i>
                </p>
            </div>
        </div>
        {{--/Copyright --}}

        {{-- Descarga de la app desde las tiendas correspondientes --}}
        <div class="row mb-2">
            <div class="col-md-12 text-center">
                <a href="https://play.google.com/store/apps/details?id=com.fikrea.app&gl=@locale">
                    <img class="max-width-300" src="@asset('/assets/images/landing/stores/android-google-play-icon.png')" alt="" />
                </a>
            </div>
            <div class="col-md-12 text-center mt-2">
                <a href="#!">
                    <img  class="max-width-300" src="@asset('/assets/images/landing/stores/ios-apple-store-icon.png')" alt="" />
                </a>
            </div> 
        </div>
        {{--/Descarga de la app desde las tiendas correspondientes --}}

        {{-- Condiciones de uso y políticas aplicables al sitio web --}}
        @include('landing.sections.body.legal')
        {{--/Condiciones de uso y políticas aplicacables al sitio web --}}

    </div>

</footer>

{{-- Modales con los textos legales --}}
@include('landing.modals.legal.legal-warning')
@include('landing.modals.legal.privacity-policy')
@include('landing.modals.legal.cookies-policy')
@include('landing.modals.legal.return-policy')
@include('landing.modals.legal.use-conditions')
{{-- /Modales con los textos legales --}}
