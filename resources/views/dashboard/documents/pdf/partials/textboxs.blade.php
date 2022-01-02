{{--
    Cajas de texto completadas
    Si este documento no tiene validaciones de cajas de texto no se muestra nada
 --}}
@if ($document->mustBeValidateByTextBoxs() && $document->boxs->count())
    <p>
    <table>
        <thead>
            <tr>
                <th colspan="2">@lang('Validaciones de edición de documento')</th>
            </tr>
        </thead>
    </table>
    </p>

    @foreach ($document->boxs as $box)
        <p>
        <table>
            <thead>
                <tr>
                    <th colspan="2">
                        {{ $box->signer }} [ @lang('Texto') {{ $box->code }} ]
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>@lang('Firmante')</th>
                    <td>
                        {{ $box->signer }}
                    </td>
                </tr>
                <tr>
                    <td>@lang('Texto')</td>
                    <td class="text-center">
                        {{ $box->text }}
                    </td>
                </tr>
                <tr>
                    <td>@lang('Página')</td>
                    <td>
                        {{ $box->page }}
                    </td>
                </tr>
                <tr>
                    <td>@lang('Dirección IP')</td>
                    <td>
                        <div>
                            {{ $box->ip }}
                        </div>
                        <div>
                            @hostname($box->ip)
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>@lang('Sistema Utilizado')</td>
                    <td>
                        @if ($box->device)
                            <div>
                                @userdevice($box->device)
                            </div>
                        @endif
                        @useragent($box->user_agent)
                    </td>
                </tr>
                {{-- Para el creador/autor del documento no se recoge la ubicación --}}
                @if (!$box->creator)
                    <tr>
                        <td>@lang('Ubicación')</td>
                        <td>
                            <div>
                                {{ $box->latitude }} {{ $box->longitude }}
                            </div>
                            <div>
                                @if ($box->latitude && $box->longitude)
                                    <a
                                        href="https://www.google.com/maps/search/?api=1&query={{ $box->latitude }},{{ $box->longitude }}">
                                        @lang('Ver en Google Maps')
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endif
                {{-- /Para el creador/autor del documento no se recoge la ubicación --}}
                <tr>
                    <td>@lang('Fecha de completado')</td>
                    <td>@datetime($box->signDate)</td>
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
            @if ($signer->getIfCommentExists(\App\Enums\ValidationType::TEXT_BOX_VERIFICATION))
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
                                    <small>"{{ $signer->getIfCommentExists(\App\Enums\ValidationType::TEXT_BOX_VERIFICATION) }}"</small>
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
