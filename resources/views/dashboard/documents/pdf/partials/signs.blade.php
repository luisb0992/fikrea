{{-- Firmas manuscritas efectuadas
    Si este documento no tiene validaciones de firma manuscrita no se muestra nada

    REVISAR CON MIKEL EL TEMA DE LAS CAPTURAS DE PANTALLA
    QUE NO SON UN TIPO DE VALIDACION SINO UNA OPCION DENTRO DE X VALIDACION O PROCESO
    
    Validaciones de captura de pantalla efectuadas      
    @include('dashboard.documents.pdf.partials.screen')
    / Validaciones de captura de pantalla efectuadas --}}
@if ($document->mustBeValidateByHandWrittenSignature() && $document->signs->count())
    <p>
    <table>
        <thead>
            <tr>
                <th colspan="2">@lang('Validaciones de firma manuscrita')</th>
            </tr>
        </thead>
    </table>
    </p>

    @foreach ($document->signs as $sign)
        <p>
        <table>
            <thead>
                <tr>
                    <th colspan="2">
                        {{ $sign->signer }} [ @lang('Firma') {{ $sign->code }} ]
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>@lang('Firmante')</th>
                    <td>
                        {{ $sign->signer }}
                    </td>
                </tr>
                <tr>
                    <td>@lang('Firma')</td>
                    <td class="text-center">
                        <div>
                            <img src="{{ $sign->sign }}" alt="">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>@lang('Página')</td>
                    <td>
                        {{ $sign->page }}
                    </td>
                </tr>
                <tr>
                    <td>@lang('Dirección IP')</td>
                    <td>
                        <div>
                            {{ $sign->ip }}
                        </div>
                        <div>
                            @hostname($sign->ip)
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>@lang('Sistema Utilizado')</td>
                    <td>
                        @if ($sign->device)
                            <div>
                                @userdevice($sign->device)
                            </div>
                        @endif
                        @useragent($sign->user_agent)
                    </td>
                </tr>
                {{-- Para el creador/autor del documento no se recoge la ubicación --}}
                @if (!$sign->creator)
                    <tr>
                        <td>@lang('Ubicación')</td>
                        <td>
                            <div>
                                {{ $sign->latitude }} {{ $sign->longitude }}
                            </div>
                            <div>
                                @if ($sign->latitude && $sign->longitude)
                                    <a
                                        href="https://www.google.com/maps/search/?api=1&query={{ $sign->latitude }},{{ $sign->longitude }}">
                                        @lang('Ver en Google Maps')
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endif
                {{-- /Para el creador/autor del documento no se recoge la ubicación --}}
                <tr>
                    <td>@lang('Fecha de Firma')</td>
                    <td>@datetime($sign->signDate)</td>
                </tr>
            </tbody>
        </table>
        </p>

        {{-- Próxima página --}}
        <div class="break"></div>
        {{-- / Próxima página --}}

    @endforeach

    {{-- comentario de la validacion --}}
    @foreach ($document->signers as $signer)
        @if (!$signer->creator)
            @if ($signer->getIfCommentExists(\App\Enums\ValidationType::HAND_WRITTEN_SIGNATURE))
                <p>
                <table>
                    <thead>
                        <tr>
                            <th>@lang('Comentario')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <p>
                                    <small>"{{ $signer->getIfCommentExists(\App\Enums\ValidationType::HAND_WRITTEN_SIGNATURE) }}"</small>
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                </p>
            @endif
        @endif
    @endforeach
    {{-- /comentario de la validacion --}}

@endif
{{-- / Firmas manuscritas efectuadas --}}
