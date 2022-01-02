{{--
	Detalles de la solicitud de documentos
	@param DocumentRequest $request La solicitud de documentos
--}}

@desktop
<div class="text-secondary small mb-4 ml-5 text-justify">
@else
<div class="text-secondary small mb-4 text-justify">
@enddesktop

    <p>
        @lang('Detalles de la solicitud de documentos')
    </p>

    <p>
        <div class="table-responsive">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <td class="bold">@lang('Nombre'):</td>
                        <td>{{$request->name ?? Lang::get('Solicitud de documentos')}}</td>
                    </tr>

                    {{--No muestro el comentario cuando es una validacion porque coincide con el nombre--}}
                    @isset ($nocomment)
                    @else
                    <tr>
                        <td class="bold">@lang('Comentarios'):</td>
                        <td>{{$request->comment ?? Lang::get('Solicitud de documentos')}}</td>
                    </tr>
                    @endif
                    {{--/ No muestro el comentario cuando es una validacion porque coincide con el nombre--}}
                    
                </tbody>
            </table>
        </div>
    </p>

    {{-- Si tiene documentos la solicitud --}}
    @if ($request && $request->documents)
        <p class="bold">
            @lang('A continuación se listan los documentos que deberá aportar'):
        </p>
        <div class="table-responsive">
            <table class="table table-striped mt-2">
                <thead>
                    <th>@lang('Documento')</th>
                    <th>@lang('Tipo')</th>
                    <th>@lang('Tamaño Máximo')</th>
                    <th>@lang('Validez')</th>
                    <th class="text-left">@lang('Fecha de Vencimiento')</th>
                </thead>
                <tbody>
                    @foreach($request->documents as $document)
                        <tr>
                            <td data-label="@lang('Documento')">
                                {{$document->name}}
                            </td>
                            <td data-label="@lang('Tipo')">
                                {{-- Muestra el tipo del archivo requerido --}}
                                @if ($document->type)
                                    @lang( $document->mimeType->description ?? $document->type )
                                @else
                                    @lang('Cualquiera')
                                @endif
                            </td>
                            <td data-label="@lang('Tamaño')">
                                {{-- Muestra el tamaño máximo admisible para el archivo requerido --}}
                                @if ($document->maxsize)
                                    @filesize($document->maxsize)
                                @else
                                    @lang('Cualquiera')
                                @endif
                            </td>
                            <td data-label="@lang('Validez')">
                                {{-- Muestra la fecha de expedición máxima admisible para el archivo requerido --}}
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
                                    <div class="text-danger text-nowrap">
                                        <i class="fas fa-greater-than"></i>
                                        @date($document->issued_to)
                                    </div>
                                @else
                                    @lang('Cualquiera')
                                @endif
                            </td>

                            <td data-label="@lang('Fecha de Vencimiento')">
                                @if ($document->has_expiration_date)
                                    @if ($document->file() && $document->file()->expiration_date)
                                    <span class="@if($document->file()->isNearToExpire()) text-danger @else text-success @endif text-white p-2">
                                        @date($document->file()->expiration_date)
                                    </span>
                                    @else
                                    <span class="text-warning">@lang('Solicitada')</span>
                                    @endif
                                @else
                                <span class="text-success">@lang('No solicitada')</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
    
</div>
{{-- / Detalles de la solicitud de documentos --}}
