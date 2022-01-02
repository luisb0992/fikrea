{{-- Lista de documentos proporcionados --}}
<div class="col-md-12 mt-4 mb-4">
    <h5>
        <i class="far text-success fa-folder-open"></i>
        @lang('Documentos Proporcionados')
    </h5>
    <div class="main-card card">

        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>@lang('Usuario')</th>
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
                    <tr>
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
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-danger bold">
                            @lang('No hay archivos')
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th>@lang('Usuario')</th>
                        <th>@lang('Documento')</th>
                        <th>@lang('Tamaño') kB</th>
                        <th>@lang('Tipo')</th>
                        <th class="text-center">@lang('Fecha Subida')</th>
                        <th class="text-center">@lang('Fecha Expedición')</th>
                        <th class="text-center">@lang('Fecha Vencimiento')</th>
                        <th>@lang('Ip')</th>
                        <th>@lang('Sistema')</th>
                        <th>@lang('Ubicación')</th>
                        <th class="text-center">@lang('Descargar')</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Si hay archivos ofrece la descarga de todos los archivos de la solicitud en un zip --}}
        @if ($request->files->count() > 0)
        <div class="col-md-12 text-right mb-4">
            <a class="btn btn-success square" href="@route('dashboard.document.request.download.files', ['id' => $request->id])">
                <i class="fas fa-download"></i>
                @lang('Descargar Todos')
            </a>
        </div>
        @endif
        {{--/Descarga de todos los archivos de la solicitud en un zip --}}

    </div>
</div>
{{-- Lista de documentos proporcionados --}}