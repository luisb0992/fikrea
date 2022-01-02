<!doctype html>
<html lang="@locale">

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<title>@config('app.name') → Error 404 → @lang('No Encontrado')</title>

	@include('dashboard.sections.head.favicon')

    {{-- Fuente de la página --}}
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,700" rel="stylesheet" />
    
    {{-- Hoja de estilos  de la pagina de error --}}
	<link href="@asset('assets/css/bootstrap.min.css')" rel="stylesheet" />

	<script src="@asset('assets/js/app.js')"></script>
 
</head>

<body>

	<div class="app-container app-theme-white body-tabs-shadow fixed-sidebar fixed-header">

		<div class="row m-5 text-center">

			<div class="col col-xs-12 col-sm-12 col-md-12 col-lg-6">
				<img src="@asset('assets/images/common/fikrea-large-logo.png')"
					alt="@lang('Imagen logo fikrea')"
					class="img-fluid"
				>
				<div class="text-warning text-center mt-2">
					<h2 class="">@lang('Upps!! algo ha salido mal') !</h2>
				</div>
			</div>
	
			<div class="col col-xs-12 col-sm-12 col-md-12 col-lg-6">
				<p>
					<h2>@lang('Usted no tiene acceso al recurso solicitado')</h2>
				</p>
				<p class="text-left">
					<ul class="text-justify">
						<li>
							@lang('Por favor, revise su conexión a internet, tal vez su dispositivo se haya desconectado en algún momento del proceso').
						</li>
						<li>
							@lang('En caso de que verifique que su conexión es correcta, intente de nuevo hacer la acción que intentaba,
							cerrando dicha ventana y entrando de nuevo'). 
						</li>
						<li>
							@lang('Si a pesar de realizar este paso, el error persiste, le rogamos que refresque su página pulsando 
							CONTROL +F5 de manera simultánea, de esta manera, procede a limpiar la caché de su dispositivo').
						</li>
                        <li>
                            @lang('Si está accediendo a un proceso de validación o solicitud de documentos puede que este haya sido atendido previamente por usted').
                        </li>
						<li>
							@lang('En el caso de estar navegando en nuestra versión móvil, le rogamos que vaya a su marketplace y 
							verifique que exista una versión actualizada de nuestra aplicación, lo cual tal vez elimine este error').
						</li>
						<li>
							@lang('Por último, si el error persiste y no le permite continuar con la acción que intenta,')
							@lang('contacte con nosotros en el siguiente enlace:')
							<a href="@route('contact.show')" target="_blank">Contacto</a>@lang(', donde le rogamos nos comparta 
							captura del error, URL y una explicación del proceso que está intentado realizar,')
							@lang('de esta manera nos pondremos a trabajar rápidamente para solucionar el problema').
						</li>
					</ul>
				</p>
	
				<a href="#!" onclick="javascript:history.go(-1)">
					@lang('Volver')
				</a>
	
			</div>
	
		</div>
	
		{{-- Publicidad de la app --}}
		<div class="container text-center">
			{{-- Título --}}
			<div class="row">
				<h2 class="text-center col-md-12">
					<img class="img-fluid" style="height: 150px;" aria-hidden="true" target="_blank" src="@asset('/assets/images/dashboard/logos/fikrea-square-medium-logo.png')" alt="" />
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
	
			{{-- Descarga de la app desde las tiendas correspondientes --}}
			<div class="row mt-4 mb-2">
				<div class="col-md-12 text-center">
					<a href="https://play.google.com/store/apps/details?id=com.fikrea.app&gl=@locale">
						<img style="height: 100px;" src="@asset('/assets/images/landing/stores/android-google-play-icon.png')" alt="" />
					</a>
				</div>
				<div class="col-md-12 text-center mt-2">
					<a href="#!">
						<img  style="height: 100px;" src="@asset('/assets/images/landing/stores/ios-apple-store-icon.png')" alt="" />
					</a>
				</div> 
			</div>
			{{--/Descarga de la app desde las tiendas correspondientes --}}
	
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

			<div class="mt-4"></div>

			{{-- Modales con los textos legales --}}
			@include('landing.modals.legal.legal-warning')
			@include('landing.modals.legal.privacity-policy')
			@include('landing.modals.legal.cookies-policy')
			@include('landing.modals.legal.return-policy')
			@include('landing.modals.legal.use-conditions')
			{{--/Modales con los textos legales --}}

		</div>
		{{-- /Publicidad de la app --}}
		
	</div>

</body>

</html>
