<!doctype html>
<html lang="@locale">

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />

	<title>@config('app.name') → Error 404 → @lang('No Encontrado')</title>

    {{-- Fuente de la página --}}
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,700" rel="stylesheet" />
    
    {{-- Hoja de estilos  de la pagina de error --}}
	<link rel="stylesheet" href="@mix('assets/css/error/style.css')" />

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

</head>

<body>
	<div id="notfound">
		<div class="notfound">
			<div class="notfound-404"></div>
			<h1>404</h1>
			<h2>@lang('Se ha producido un error en la solicitud')</h2>
			<p>
                @lang('Página no encontrada')
            </p>
			<a href="@route('dashboard.home')">
                @lang('Volver')
                <div class="logo">
                    <a href="@route('dashboard.home')">
                        <img src="@asset('/assets/images/dashboard/logos/fikrea-medium-logo.png')" alt="" />
                    </a> 
                </div>
            </a>
		</div>
    </div>
</body>

</html>
