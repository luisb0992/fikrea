{{-- Una tabla con el listado de archivos aportados --}}

@desktop
<div class="text-secondary small mb-4 ml-5 text-justify">
@else
<div class="text-secondary small mb-4 text-justify">
@enddesktop

	<p class="bold">
        @lang('Documentos aportados')
    </p>

    <div class="table-responsive w-100">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>@lang('Documento')</th>
                    <th>@lang('Tamaño') kB</th>
                    <th>@lang('Tipo')</th>
                    <th class="text-center">@lang('Fecha Subida')</th>
                    <th class="text-center">@lang('Fecha Expedición')</th>
                    <th class="text-center">@lang('Fecha Vencimiento')</th>
                    <th>@lang('Ip')</th>
                    <th>@lang('Sistema')</th>
                    <th>@lang('Ubicación')</th>
                    <th>@lang('Descargar')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($request->files as $file)
                {{-- Muestro los archivos que yo como firmante aporté --}}
                @if ($file->signer_id === $signer->id)
                <tr>
                    <td data-label="@lang('Documento')">
                        {{$file->requiredDocument->name}}
                    </td>
                    <td data-label="@lang('Tamaño')">
                        @filesize($file->size)
                    </td>
                    <td data-label="@lang('Tipo')" class="align-middle">
                        @include('dashboard.partials.file-icon', ['type' => $file->type])
                    </td>
                    <td data-label="@lang('Fecha Subida')" class="text-center">
                        @date($file->created_at)
                    </td>
                    <td data-label="@lang('Fecha de Expedición')" class="text-center">
                        @if ($file->requiredDocument->issued_to)
                            {{-- Si se ha exigido una fecha de validez para el documento --}}
                            @if ($file->issued_to)
                                @if ($file->requiredDocument->issued_to && $file->issued_to < $file->requiredDocument->issued_to)
                                    <span>@date($file->issued_to)</span>
                                @elseif ($file->requiredDocument->issued_to && $file->issued_to >= $file->requiredDocument->issued_to) 
                                    <span class="text-success">@date($file->issued_to)</span>
                                @else 
                                    @date($file->issued_to)
                                @endif
                            @else
                                <span class="text-success">@lang('No proporcionada')</span>
                            @endif
                        @else
                           {{-- Si no se ha exigido una fecha de validez para el documento --}}
                            @if ($file->issued_to)
                                @date($file->issued_to)
                            @endif
                        @endif
                    </td>
                    <td data-label="@lang('Fecha de Vencimiento')" class="text-center">
                    	@if ($file->requiredDocument->has_expiration_date)
                    		@if ($file->expiration_date)
                    		<span class="@if($file->isNearToExpire()) text-danger bold @else text-success @endif">
                    			@date($file->expiration_date)
                    		</span>
                            @else
                                 
                            @endif
                        @else
                        <span>@lang('No requerida')</span>
                    	@endif
                    </td>
                    <td data-label="@lang('Ip')">
                        {{$file->ip}}
                    </td>
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
                    <td data-label="@lang('Ubicación')" class="text-center">
                        @if ($file->latitude && $file->longitude)
                            <a target="_blank" class="btn btn-primary square" href="https://www.google.com/maps/search/?api=1&query={{$file->latitude}},{{$file->longitude}}">
                                <i class="fas fa-map-marker-alt"></i>
                            </a>
                        @endif
                    </td>
                    <td class="text-center">
                        <a class="btn btn-success square" href="@route('dashboard.document.request.download.file', ['id' => $file->id])">
                            <i class="fas fa-download"></i>
                        </a>
                    </td>
                </tr>
                @endif
                @empty
                <tr>
                    <td colspan="10" class="text-center">
                        @lang('No hay archivos')
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
     
</div>
{{--/Una tabla con el listado de archivos aportados --}}