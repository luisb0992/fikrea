{{-- Lista de documentos proporcionados --}}
@forelse ($request->files as $file)
<p>
	<table>
		<thead>
			<tr>
	            <th colspan="2">@lang('Documento Proporcionado')</th>
	        </tr>
		</thead>
	</table>	
</p>

<p>
	<table>
		<thead>
			<tr>
				<th>@lang('Documento')</th>
				<th>{{$file->requiredDocument->name}}</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>@lang('Usuario')</td>
				<td data-label="@lang('Usuario')">
                    {{$file->signer->name}} {{$file->signer->lastname}}
                    @if ($file->signer->email)
                    <div>
                        <a href="mailto: {{$file->signer->email}}">
                            {{$file->signer->email}}
                        </a>
                    </div>
                    @elseif ($file->signer->phone)
                    <div>
                        <a href="tel: {{$file->signer->phone}}">
                            {{$file->signer->phone}}
                        </a>
                    </div>
                    @endif
                </td>
			</tr>

			<tr>
				<td>@lang('Almacenamiento')</td>
	            <td>
	                {{-- Si el archivo se almacena en el servicio S3 de Amazon --}}
	                @if (\Fikrea\AppStorage::isS3())
	                    <a href="https://aws.amazon.com/es/s3/">Amazon Simple Storage Service (Amazon S3)</a>
	                @else
	                {{-- Almacenamiento en servidor local (UFS, Unix File System) --}}
	                    UFS
	                @endif
	            </td>
			</tr>

			<tr>
				<td>@lang('Nombre del archivo')</td>
				<td data-label="@lang('Nombre del archivo')">
					{{$file->name}}
                </td>
			</tr>

			<tr>
				<td>@lang('Tipo')</td>
				<td data-label="@lang('Tipo')">
					{{$file->type}}
                </td>
			</tr>

			<tr>
				<td>@lang('Tamaño')</td>
				<td data-label="@lang('Tamaño')">
                    @filesize($file->size)
                </td>
			</tr>

			<tr>
				<td>@lang('Fecha Subida')</td>
				<td data-label="@lang('Fecha Subida')">
                    @date($file->created_at)
                </td>
			</tr>

			<tr>
				<td>@lang('Fecha de Expedición')</td>
				<td data-label="@lang('Fecha de Expedición')">
                    @if ($file->requiredDocument->issued_to)
                        {{-- Si se ha exigido una fecha de validez para el documento --}}
                        @if ($file->issued_to)
                            @if ($file->requiredDocument->issued_to && $file->issued_to < $file->requiredDocument->issued_to)
                                <span class="p-2">@date($file->issued_to)</span>
                            @elseif ($file->requiredDocument->issued_to && $file->issued_to >= $file->requiredDocument->issued_to) 
                                <span class="p-2">@date($file->issued_to)</span>
                            @else 
                                @date($file->issued_to)
                            @endif
                        @else
                            <span class="p-2">@lang('No proporcionada')</span>
                        @endif
                    @else
                       {{-- Si no se ha exigido una fecha de validez para el documento --}}
                        @if ($file->issued_to)
                            @date($file->issued_to)
                        @endif
                    @endif
                </td>
			</tr>

			<tr>
				<td>@lang('Fecha de vencimiento')</td>
				<td data-label="@lang('Fecha de Vencimiento')">
                	@if ($file->requiredDocument->has_expiration_date)
                		@if ($file->expiration_date)
                		<div>
                			<span class="p-2">
	                			@date($file->expiration_date)
	                		</span>
                		</div>
	                		@if($file->isNearToExpire())
	                		<div>
	                			<span class="p-2">
		                			@lang('*Al expirar*')
		                		</span>
	                		</div>
	                		@endif
                		 
                        @else
                        @endif
                    @else
                    <span class="p-2">@lang('No requerida')</span>
                	@endif
                </td>
			</tr>

			<tr>
				<td>@lang('IP')</td>
				<td data-label="@lang('Ip')">
                    {{$file->ip}}
                </td>
			</tr>

			<tr>
				<td>@lang('Sistema')</td>
				<td data-label="@lang('Sistema')">
					@if ($file->device)
					<div class="text-info bold">
						@userdevice($file->device)
					</div>
					@endif
					<div>
						@useragent($file->user_agent)
					</div>
                </td>
			</tr>

			<tr>
				<td>@lang('Ubicación')</td>
				<td data-label="@lang('Ubicación')">
				    @if ($file->latitude && $file->longitude)
				        <a target="_blank" class="btn btn-primary square" href="https://www.google.com/maps/search/?api=1&query={{$file->latitude}},{{$file->longitude}}">
				            @lang('Ubicación')
				        </a>
				    @endif
				</td>
			</tr>

			<tr>
				<td>@lang('Descarga')</td>
				<td>
				    <a class="btn btn-success square" href="@route('dashboard.document.request.download.file', ['id' => $file->id])">
				        @lang('Descargar')
				    </a>
				</td>
			</tr>

		</tbody>
	</table>
</p>

{{-- Si no es el último elemento salto de página --}}
@if (!$loop->last)
    {{-- Próxima página--}}
	<div class="break"></div>
	{{-- / Próxima página--}}
@endif
{{-- /Si no es el último elemento salto de página --}}

@empty
@isset ($fromSign)
@else
<p>
	<table>
		<tbody>
			<tr>
			    <td colspan="2">
			        @lang('No se han proporcionado archivos')
			    </td>
			</tr>
		</tbody>
	</table>
</p>
@endisset
@endforelse

{{-- Si hay archivos ofrece la descarga de todos los archivos de la solicitud en un zip --}}
@if ($request->files->count() > 0)
<p>
	<table>
		<thead>
			<tr>
				<th colspan="2">@lang('Descargar todos los documentos proporcionados')</th>
			</tr>
		</thead>
		<tbody>
			<tr>
			    <td colspan="2">
					<a class="btn btn-success square" href="@route('dashboard.document.request.download.files', ['id' => $request->id])">
				        @lang('Descargar Todos')
				    </a>
			    </td>
			</tr>
		</tbody>
	</table>
</p>

@isset ($fromSign)
{{-- Próxima página--}}
<div class="break"></div>
{{-- / Próxima página--}}
@else
@endisset

@endif
{{--/Descarga de todos los archivos de la solicitud en un zip --}}

{{-- Lista de documentos proporcionados --}}