{{-- Lista de documentos requeridos en la solicitud --}}
@foreach ($request->documents as $document)
<p>
	<table>
		<thead>
			<tr>
	            <th colspan="2">@lang('Documento Requerido')</th>
	        </tr>
		</thead>
	</table>	
</p>

<p>
	<table>
		<thead>
			<tr>
				<th>@lang('Documento')</th>
				<th>{{$document->name}}</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>@lang('Comentarios')</td>
				<td>{{$document->comment}}</td>
			</tr>

			<tr>
				<td>@lang('Tipo')</td>
				<td>
					@if ($document->type)
		                <span class="p-2">
                            @lang( $document->mimeType->description ?? $document->type )
		                </span>
		            @else
		                <span>@lang('Cualquiera')</span>
		            @endif
				</td>
			</tr>

			<tr>
				<td>@lang('Tamaño')</td>
				<td>
					@if ($document->maxsize)
		                <span class="p-2">@filesize($document->maxsize)</span>
		            @else
		                <span>@lang('Cualquiera')</span>
		            @endif
				</td>
			</tr>

			<tr>
				<td>@lang('Validez')</td>
				<td>
					@if ($document->issued_to)
		                <div>
		                    {{$document->validity}}
		                    @switch ($document->validity_unit)
		                        @case (\App\Enums\TimePeriod::DAY)
		                            @lang('días')
		                            @break
		                        @case (\App\Enums\TimePeriod::MONTH)
		                            @lang('meses')
		                            @break
		                        @case (\App\Enums\TimePeriod::YEAR)
		                            @lang('años')
		                            @break
		                    @endswitch
		                </div>
		                <div>
		                    {{'>'}}
		                    @date($document->issued_to)
		                </div>
		            @else
		                <span class="text-success">@lang('Cualquiera')</span>
		            @endif
				</td>
			</tr>

			<tr>
				<td>@lang('Fecha de vencimiento')</td>
				<td>
					@if ($document->has_expiration_date)
		                <span class="p-2">
		                    @lang('Solicitada')
		                </span>
		            @else
		                <span class="p-2">
		                    @lang('Sin solicitar')
		                </span>
		            @endif
				</td>
			</tr>

			<tr>
				<td>@lang('Notificar al vencer')</td>
				<td>
					@if ($document->notify)
		                <span class="p-2">@lang('Notificar')</span>
		            @else
		            <span class="p-2">@lang('No notificar')</span>
		            @endif
				</td>
			</tr>

		</tbody>
	</table>
</p>

{{-- Próxima página--}}
<div class="break"></div>
{{-- / Próxima página--}}

@endforeach
{{--/Lista de documentos requeridos en la solicitud --}}