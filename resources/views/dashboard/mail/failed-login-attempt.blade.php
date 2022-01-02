{{--
	Se notifica al usuario de un intento fallido de acceso a la app
 --}}
@extends('dashboard.layouts.mail')

@section('content')

    <p>
        @lang(':app le notifica que en fecha :date ha ocurrido un intento de acceso fallido a nuestra aplicación', 
	        [
	        	'app' => @config('app.name'),
	        	'date' => (new Carbon\Carbon(now()))->format("d-m-Y H:i:s"),
	        ]
        ).
    </p>

    <p>
        @lang('A continuación los detalles del intento de acceso').
    </p>

    <p>
        <table class="table">
        	<tbody>
        		<tr>
        			<td class="text-bold">@lang('Fecha y hora')</td>
        			<td>{{ (new Carbon\Carbon(now()))->format("d-m-Y H:i:s") }}</td>
        		</tr>

        		<tr>
        			<td class="text-bold">@lang('IP')</td>
        			<td>{{ $request->ip() }}</td>
        		</tr>

                <tr>
                    <td class="text-bold">@lang('Dirección aproximada según IP')</td>
                    <td>{{ $geoip->country }}, {{ $geoip->region }}, {{ $geoip->city }}</td>
                </tr>

        		<tr>
        			<td class="text-bold">@lang('Sistema')</td>
        			<td>
                        <div>
                            @useragent($request->server('HTTP_USER_AGENT'))
                        </div>
                    </td>
        		</tr>

        	</tbody>
        </table>
    </p>

    <p>
        @lang('Si ha sido usted, suponemos que ha olvidado la contraseña, por lo que puede restablecer la misma accediendo al siguiente enlace:')
    </p>

    <p class="text-center">

		<a class="btn btn-primary square"
			href="@route('dashboard.rememberme')"
		>
			@lang('Restablecer contraseña')
		</a>
    </p>

@stop
