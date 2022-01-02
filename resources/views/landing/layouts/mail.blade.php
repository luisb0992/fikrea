{{--
    Layout para los mensajes de correo

	Aparecen algunas propiedades como mso-table-lspace, propietarias de Microsoft y que
	se emplean en Microsoft Outlook

    @author javieru <javi@gestoy.com>
    @copyright 2021 Retail Servicios Externos    
--}}
<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
	xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="x-apple-disable-message-reformatting" />
	<title>@config('app.name')</title>

	<link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" rel="stylesheet" />

	<!-- CSS Reset : BEGIN -->
	<style>
		html,
		body {
			margin: 0 auto !important;
			padding: 0 !important;
			height: 100% !important;
			width: 100% !important;
			background: #f1f1f1;
		}

		/* What it does: Stops email clients resizing small text. */
		* {
			-ms-text-size-adjust: 100%;
			-webkit-text-size-adjust: 100%;
		}

		/* What it does: Centers email on Android 4.4 */
		div[style*="margin: 16px 0"] {
			margin: 0 !important;
		}

		/**
		 * Textos
		 */
		.text-bold {
			font-weight: bold;
		}

		.text-success {
			color:#368536;
		}

		.text-warning {
			color:#d8a518;
		}

		.text-danger {
			color:#a83967;
		}

		.text-info {
			color:#3161bb;
		}

		/**
		 * Alineación
		 */
		.text-left {
			text-align: left !important;
		}

		.text-center {
			text-align: center !important;
		}

		.text-right {
			text-align: right !important;
		}

		.text-justify {
			text-align: justify !important;
		}

		.mt-50 {
			margin-top: 50px;
		}

		.max-width-300 {
			width: 100%;
			max-width: 300px;
		}

		/**
		  Estilo de tablas
		*/
		.table {
			table-layout: fixed !important;
			margin: 0 auto !important;
			border-collapse: collapse;
			margin: 25px 0;
			font-size: 0.9em;
			font-family: sans-serif;
			min-width: 400px;
			box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
		}

		.table thead tr {
			background-color: #3161bb;
			color: #ffffff;
			text-align: left;
		}
		
		.table th, .table td {
			padding: 12px 15px;
		}
		
		.table tbody tr {
			border-bottom: 1px solid #dddddd;
		}
		
		.table tbody tr:nth-of-type(even) {
			background-color: #f3f3f3;
		}
		
		.table tbody tr:last-of-type {
			border-bottom: 2px solid #3161bb;
		}
		
		.table tbody tr.active-row {
			font-weight: bold;
			color: #3161bb;
		}
		
		/* What it does: Uses a better rendering method when resizing images in IE. */
		img {
			-ms-interpolation-mode: bicubic;
		}

		/* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
		a {
			text-decoration: none;
		}

		/* What it does: A work-around for email clients meddling in triggered links. */
		*[x-apple-data-detectors],
		/* iOS */
		.unstyle-auto-detected-links *,
		.aBn {
			border-bottom: 0 !important;
			cursor: default !important;
			color: inherit !important;
			text-decoration: none !important;
			font-size: inherit !important;
			font-family: inherit !important;
			font-weight: inherit !important;
			line-height: inherit !important;
		}

		/* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */
		.a6S {
			display: none !important;
			opacity: 0.01 !important;
		}

		/* What it does: Prevents Gmail from changing the text color in conversation threads. */
		.im {
			color: inherit !important;
		}

		/* If the above doesn't work, add a .g-img class to any image in question. */
		img.g-img+div {
			display: none !important;
		}

		
		/* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
		/* Create one of these media queries for each additional viewport size you'd like to fix */

		/* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
		@media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
			u~div .email-container {
				min-width: 320px !important;
			}
		}

		/* iPhone 6, 6S, 7, 8, and X */
		@media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
			u~div .email-container {
				min-width: 375px !important;
			}
		}

		/* iPhone 6+, 7+, and 8+ */
		@media only screen and (min-device-width: 414px) {
			u~div .email-container {
				min-width: 414px !important;
			}
		}
	</style>

	<!-- CSS Reset : END -->

	<!-- Progressive Enhancements : BEGIN -->
	<style>

		.primary {
			background: #0d0cb5;
		}

		.bg_white {
			background: #ffffff;
		}

		.bg_light {
			background: #fafafa;
		}

		.bg_black {
			background: #000000;
		}

		.bg_dark {
			background: rgba(0, 0, 0, .8);
		}

		.email-section {
			padding: 2.5em;
		}

		/*BUTTON*/
		.btn {
			padding: 5px 15px;
			display: inline-block;
		}

		.btn.btn-primary {
			border-radius: 5px;
			background: #0d0cb5;
			color: #ffffff;
		}

		.btn.btn-white {
			border-radius: 5px;
			background: #ffffff;
			color: #000000;
		}

		.btn.btn-white-outline {
			border-radius: 5px;
			background: transparent;
			border: 1px solid #fff;
			color: #fff;
		}

		h1,
		h2,
		h3,
		h4,
		h5,
		h6 {
			font-family: 'Poppins', sans-serif;
			color: #000000;
			margin-top: 0;
		}

		body {
			font-family: 'Poppins', sans-serif;
			font-weight: 400;
			font-size: 15px;
			line-height: 1.8;
			color: rgba(0, 0, 0, .4);
		}

		a {
			color: #0d0cb5;
		}

		table {
			border: none;
		}

		/*LOGO*/

		.logo h1 {
			margin: 0;
		}

		.logo h1 a {
			color: #000000;
			font-size: 20px;
			font-weight: 700;
			text-transform: uppercase;
			font-family: 'Poppins', sans-serif;
		}

		.navigation {
			padding: 0;
		}

		.navigation li {
			list-style: none;
			display: inline-block;
			;
			margin-left: 5px;
			font-size: 13px;
			font-weight: 500;
		}

		.navigation li a {
			color: rgba(0, 0, 0, .4);
		}

		/*HERO*/
		.hero {
			position: relative;
			z-index: 0;
		}

		.hero .overlay {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			content: '';
			width: 100%;
			background: #000000;
			z-index: -1;
			opacity: .3;
		}

		.hero .icon a {
			display: block;
			width: 60px;
			margin: 0 auto;
		}

		.hero .text {
			color: rgba(255, 255, 255, .8);
		}

		.hero .text h2 {
			color: #ffffff;
			font-size: 30px;
			margin-bottom: 0;
		}


		/*HEADING SECTION*/

		.heading-section h2 {
			color: #000000;
			font-size: 20px;
			margin-top: 0;
			line-height: 1.4;
			font-weight: 700;
			text-transform: uppercase;
		}

		.heading-section .subheading {
			margin-bottom: 20px !important;
			display: inline-block;
			font-size: 13px;
			text-transform: uppercase;
			letter-spacing: 2px;
			color: rgba(0, 0, 0, .4);
			position: relative;
		}

		.heading-section .subheading::after {
			position: absolute;
			left: 0;
			right: 0;
			bottom: -10px;
			content: '';
			width: 100%;
			height: 2px;
			background: #0d0cb5;
			margin: 0 auto;
		}

		.heading-section-white {
			color: rgba(255, 255, 255, .8);
		}

		.heading-section-white h2 {
			line-height: 1;
			padding-bottom: 0;
		}

		.heading-section-white h2 {
			color: #ffffff;
		}

		.heading-section-white .subheading {
			margin-bottom: 0;
			display: inline-block;
			font-size: 13px;
			text-transform: uppercase;
			letter-spacing: 2px;
			color: rgba(255, 255, 255, .4);
		}


		.icon {
			text-align: center;
		}

		/*SERVICES*/
		.services {
			background: rgba(0, 0, 0, .03);
		}

		.text-services {
			padding: 10px 10px 0;
			text-align: center;
		}

		.text-services h3 {
			font-size: 16px;
			font-weight: 600;
		}

		.services-list {
			padding: 0;
			margin: 0 0 20px 0;
			width: 100%;
			float: left;
		}

		.services-list img {
			float: left;
		}

		.services-list .text {
			width: calc(100% - 60px);
			float: right;
		}

		.services-list h3 {
			margin-top: 0;
			margin-bottom: 0;
		}

		.services-list p {
			margin: 0;
		}

		/*BLOG*/
		.text-services .meta {
			text-transform: uppercase;
			font-size: 14px;
		}

		/*TESTIMONY*/
		.text-testimony .name {
			margin: 0;
		}

		.text-testimony .position {
			color: rgba(0, 0, 0, .3);

		}


		/*VIDEO*/
		.img {
			width: 100%;
			height: auto;
			position: relative;
		}

		.img .icon {
			position: absolute;
			top: 50%;
			left: 0;
			right: 0;
			bottom: 0;
			margin-top: -25px;
		}

		.img .icon a {
			display: block;
			width: 60px;
			position: absolute;
			top: 0;
			left: 50%;
			margin-left: -25px;
		}



		/*COUNTER*/
		.counter {
			width: 100%;
			position: relative;
			z-index: 0;
		}

		.counter .overlay {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			content: '';
			width: 100%;
			background: #000000;
			z-index: -1;
			opacity: .3;
		}

		.counter-text {
			text-align: center;
		}

		.counter-text .num {
			display: block;
			color: #ffffff;
			font-size: 34px;
			font-weight: 700;
		}

		.counter-text .name {
			display: block;
			color: rgba(255, 255, 255, .9);
			font-size: 13px;
		}


		/*FOOTER*/

		.footer {
			color: rgba(255, 255, 255, .5);

		}

		.footer .heading {
			color: #ffffff;
			font-size: 20px;
		}

		.footer ul {
			margin: 0;
			padding: 0;
		}

		.footer ul li {
			list-style: none;
			margin-bottom: 10px;
		}

		.footer ul li a {
			color: rgba(255, 255, 255, 1);
		}


		@media screen and (max-width: 500px) {

			.icon {
				text-align: left;
			}

			.text-services {
				padding-left: 0;
				padding-right: 20px;
				text-align: left;
			}

		}
	</style>


</head>

<body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #222222;">
	<div style="width: 100%; background-color: #f1f1f1; text-align: center;">
		<div
			style="display: none; font-size: 1px;max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all; font-family: sans-serif;">
			&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;
		</div>
		<div style="max-width: 600px; margin: 0 auto;" class="email-container">
			<!-- BEGIN BODY -->
			<table style="text-align: center;" role="presentation" cellspacing="0" cellpadding="0"  width="100%"
				style="margin: auto;">
				<tr>
					<td valign="top" class="bg_white" style="padding: 1em 2.5em;">
						<table role="presentation"  cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td width="40%" class="logo" style="text-align: left;"></td>
								<td width="60%" class="logo" style="text-align: right;">
									<ul class="navigation">
										<li><a href="@url('/landing')">@lang('¿Qué es :app?', ['app' => config('app.name')])</a></li>
										<li><a href="@url('/dashboard/login')">@lang('Iniciar Sesión')</a></li>
										<li><a href="@url('/dashboard/register')">@lang('Registrarse')</a></li>
									</ul>
								</td>
							</tr>
						</table>
					</td>
				</tr><!-- end tr -->
				<tr>
					<td>
						<img src="@url('assets/images/common/fikrea-large-logo.png')" alt="" />
					</td>
				</tr><!-- end tr -->
				<tr>
					<td valign="middle" class="hero bg_white"
						style="background-image: url(@url('assets/images/landing/mail/bg_1.jpg')); background-size: cover; height: 400px;">
						<div class="overlay"></div>
						<table>
							<tr>
								<td>
									<div class="text" style="padding: 0 3em; text-align: center;">
										<h2>@lang('Comparta sus documentos firmados')</h2>
										<p>@lang('La firma digital de la nueva era')</p>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr><!-- end tr -->
				<tr>
					<td class="bg_white">

						<table role="presentation" cellspacing="0" cellpadding="0"  width="100%">
							<tr>
								<td class="bg_white email-section" style="text-align: justify;">

									{{-- Encabezado del Mensaje --}}

									{{--/Encabezado del Mensaje --}}

									{{-- El contenido variable del mensaje de correo --}}
									@section('content')
									@show
									{{--/ El contenido variable del mensaje de correo --}}

									{{-- Pie del mensaje --}}
									<p style="text-align: right; font-weight: 1000;">
										<a href="@config('app.url')">@config('app.url')</a>
									</p>

									<p style="text-align: right;">
										@datetime
									</p>
									{{--/Pie del mensaje --}}
									
								</td>
							</tr>
						</table>
					</td>
				</tr><!-- end: tr -->
				<tr>
					<td class="bg_light email-section" style="width: 100%;">
						<table role="presentation"  cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td valign="middle" width="50%">
									<table role="presentation" cellspacing="0" cellpadding="0"  width="100%">
										<tr>
											<td>
												<img src="@url('assets/images/landing/mail/info_1.jpg')" alt=""
													style="width: 100%; max-width: 600px; height: auto; margin: auto; display: block;">
											</td>
										</tr>
									</table>
								</td>
								<td valign="middle" width="50%">
									<table role="presentation" cellspacing="0" cellpadding="0"  width="100%">
										<tr>
											<td class="text-services" style="text-align: left; padding-left:25px;">
												<div class="heading-section">
													<h2>Almacenamiento en la nube</h2>
													<p>Sube cualquier archivo, hasta 2 Gb. </p>
												</div>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr><!-- end: tr -->
				<tr>
					<td class="bg_light email-section" style="width: 100%;">
						<table role="presentation"  cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td valign="middle" width="50%">
									<table role="presentation" cellspacing="0" cellpadding="0"  width="100%">
										<tr>
											<td>
												<img src="@url('assets/images/landing/mail/info_2.jpg')" alt=""
													style="width: 100%; max-width: 600px; height: auto; margin: auto; display: block;">
											</td>
										</tr>
									</table>
								</td>
								<td valign="middle" width="50%">
									<table role="presentation" cellspacing="0" cellpadding="0"  width="100%">
										<tr>
											<td class="text-services" style="text-align: left; padding-left:25px;">
												<div class="heading-section">
													<h2>Firma digitalmente desde cualquier lugar</h2>
													<p>Firma digitalmente tú o las personas que designes, de manera fácil y desde cualquier luga. </p>
												</div>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr><!-- end: tr -->
				<tr>
					<td class="primary email-section" style="text-align:center;">
						<div class="heading-section heading-section-white">
							<h2>Firma tus contratos, nóminas, documentos legales</h2>
							<p>Con la máxima garantía y fiabilidad</p>
							<p><a href="@url('/landing')" class="btn btn-white-outline">¡Empieza ya!</a></p>
						</div>
					</td>
				</tr><!-- end: tr -->
				<tr>
					<td>
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
					</td>
				</tr>
			</table>
			</td>
			</tr><!-- end:tr -->
			<!-- 1 Column Text + Button : END -->
			</table>
		</div>
        
	</div>
</body>

</html>