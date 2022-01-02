{{--
	Se notifica al firmante que uno de los documentos que ha aportado
	está cerca de vencer	
 --}}
@extends('dashboard.layouts.mail')

@section('content')

	<p>
        @lang(':app le notifica que tiene documentos al vencer aportados en la siguiente Solicitud de Documentos', 
	        [
	        	'app' => @config('app.name')
	        ]
        )
    </p>

	<p>
		<div>
			<span>
				<h3>@lang('Los detalles a continuación')</h3>
			</span>
		</div>

		<div>
		    <table class="table">
		    	<thead>
		    		<tr>
		    			<th colspan="2">
		    				@lang('Solicitud de documento')
		    			</th>
		    		</tr>
		    	</thead>
		        <tbody>
		            <tr>
		                <td class="text-bold">@lang('Nombre')</td>
		                <td class="text">
		                	{{ $documentRequest->name }}
		                </td>
		            </tr>

		            <tr>
		                <td class="text-bold">@lang('Comentario')</td>
		                <td class="text">
		                	{{ $documentRequest->comment }}
		                </td>
		            </tr>
		             
		            <tr>
		                <td class="text-bold">@lang('Fecha')</td>
		                <td class="text">@date($documentRequest->created_at)</td>
		            </tr>
		        </tbody>
		    </table>
		</div>
	</p>

    <p>
    	<div>
    		<span>
    			<h3>@lang('Documentos que usted aportó')</h3>
    		</span>
    	</div>

    	<div>
		    <table class="table">
		    	<thead>
		    		
		    		<tr>
		    			<th>@lang('Documento')</th>
                        <th>@lang('Tamaño') kB</th>
                        <th>@lang('Tipo')</th>
                        <th class="text-center">@lang('Fecha Subida')</th>
                        <th class="text-center">@lang('Fecha Expedición')</th>
                        <th class="text-center">@lang('Fecha Vencimiento')</th>
		    		</tr>
		    	</thead>
		        <tbody>
		              @foreach($documentRequest->documents as $document)
		              <tr>
		              	<td>{{$document->name}}</td>
		              	<td>@filesize($document->file()->size)</td>
		              	<td>@include('dashboard.partials.file-icon', ['type' => $document->file()->type])</td>
		              	<td>@date($document->file()->created_at)</td>
		              	<td data-label="@lang('Fecha de Expedición')" class="text-center">
                            @if ($document->file()->requiredDocument->issued_to)
                                {{-- Si se ha exigido una fecha de validez para el documento --}}
                                @if ($document->file()->issued_to)
                                    @if ($document->file()->requiredDocument->issued_to && $document->file()->issued_to < $document->file()->requiredDocument->issued_to)
                                        <span class="bg-danger text-white p-2">@date($document->file()->issued_to)</span>
                                    @elseif ($document->file()->requiredDocument->issued_to && $document->file()->issued_to >= $document->file()->requiredDocument->issued_to) 
                                        <span class="bg-success text-white p-2">@date($document->file()->issued_to)</span>
                                    @else 
                                        @date($document->file()->issued_to)
                                    @endif
                                @else
                                    <span class="bg-danger text-white p-2">@lang('No proporcionada')</span>
                                @endif
                            @else
                               {{-- Si no se ha exigido una fecha de validez para el documento --}}
                                @if ($document->file()->issued_to)
                                    @date($document->file()->issued_to)
                                @endif
                            @endif
                        </td>
                        <td data-label="@lang('Fecha de Vencimiento')" class="text-center">
                        	@if ($document->file()->requiredDocument->has_expiration_date)
                        		@if ($document->file()->expiration_date)
                        		<span class="@if($document->file()->isNearToExpire()) text-bold @endif text-white p-2">
                        			@date($document->file()->expiration_date)
                        		</span>
                                @else
                                     
                                @endif
                            @else
                            <span class="bg-success text-white p-2">@lang('No requerida')</span>
                        	@endif
                        </td>
		              	
		              </tr>
		              @endforeach
		        </tbody>
		    </table>
		</div>
    </p>

	

	<p>@lang('Puede proceder a renovar el documento mediante el siguiente enlace'):</p>

	<p class="text-center">

		<a class="btn btn-primary square"
			href="@route('workspace.document.request.renew', ['token'=>$signer->token])"
		>
			@lang('Renovar documentos')
		</a>
    </p>

@stop
