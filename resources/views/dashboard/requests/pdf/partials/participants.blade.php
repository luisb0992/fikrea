{{-- Listado de Personas que forman parte --}}
<p>
	<table>
	    <thead>
	        <tr>
	            <th colspan="2">@lang('Participantes en el proceso')</th>
	        </tr>
	    </thead>
	    <tbody>
	    	{{-- Creador --}}
	    	<tr>
	    		<td>
	                {{$request->user->name}} {{$request->user->lastname}}
	                <div>
	                    @if ($request->user->email)
	                        <a href="mailto:{{$request->user->email}}">{{$request->user->email}}</a>
	                    @elseif ($request->user->phone)
	                        <a href="tel:{{$request->user->phone}}">{{$request->user->phone}}</a>
	                    @endif
	                </div>
	            </td>
	            <td>
                 	@lang('Solicitante')
	            </td>
	    	</tr>
	    	{{-- Creador --}}

	    	{{-- Firmantes --}}
	        @foreach ($request->signers as $signer)
	        <tr>
	            <td>
	                {{$signer->name}} {{$signer->lastname}}
	                <div>
	                    @if ($signer->email)
	                        <a href="mailto:{{$signer->email}}">{{$signer->email}}</a>
	                    @elseif ($signer->phone)
	                        <a href="tel:{{$signer->phone}}">{{$signer->phone}}</a>
	                    @endif
	                </div>
	            </td>
	            <td>
	                @lang('Solicitado')
	            </td>
	        </tr>
	        @endforeach
	    	{{-- /Firmantes --}}
	    </tbody>
	</table>
</p>
{{--/Listado de Personas que forman parte --}}