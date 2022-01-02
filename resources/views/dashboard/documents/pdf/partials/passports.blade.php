{{-- Validaciones con documentos identificativos --}}
@if ($document->mustBeValidateByPassport())

    @foreach ($document->signers as $signer)
        @foreach ($signer->passports as $passport)
            <p>
            <table>
                <thead>
                    <tr>
                        <th colspan="2">
                            {{ $signer->name }} {{ $signer->lastname }} [ Doc: {{ $passport->number }} ]
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>@lang('Validante')</th>
                        <td> {{ $signer->name }} {{ $signer->lastname }} {{ $signer->dni }} {{ $signer->email }}</td>
                    </tr>
                    <tr>
                        <td>@lang('Documento')</td>
                        <td>
                            {{ $passport->number }}
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Dirección IP')</td>
                        <td>
                            <div>
                                {{ $passport->ip }}
                            </div>
                            <div>
                                @hostname($passport->ip)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Reconocimiento Facial')</td>
                        <td>
                            @switch ($passport->face_recognition)
                                @case(-1)
                                    @lang('No realizado')
                                @break
                                @case(0)
                                    @lang('Reconocimiento facial negativo')
                                @break
                                @case(+1)
                                    @lang('Reconocimiento facial positivo')
                                @break
                            @endswitch
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Sistema Utilizado')</td>
                        <td>
                            @if ($passport->device)
                                <div>
                                    @userdevice($passport->device)
                                </div>
                            @endif
                            @useragent($passport->user_agent)
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Ubicación')</td>
                        <td>
                            <div>
                                {{ $passport->latitude }} {{ $passport->longitude }}
                            </div>
                            <div>
                                <a
                                    href="https://www.google.com/maps/search/?api=1&query={{ $passport->latitude }},{{ $passport->longitude }}">
                                    @lang('Ver en Google Maps')
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Fecha de Acreditación')</td>
                        <td>@datetime($passport->created_at)</td>
                    </tr>
                </tbody>
            </table>
            </p>

            {{-- Próxima página --}}
            <div class="break"></div>
            {{-- / Próxima página --}}

        @endforeach

        {{-- comentario de la validacion --}}
        @if ($signer->getIfCommentExists(\App\Enums\ValidationType::PASSPORT_VERIFICATION))
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
                                <small>"{{ $signer->getIfCommentExists(\App\Enums\ValidationType::PASSPORT_VERIFICATION) }}"</small>
                            </p>
                        </td>
                    </tr>
                </tbody>
            </table>
            </p>
        @endif
        {{-- /comentario de la validacion --}}
    @endforeach

@endif
{{-- /Validaciones con documentos identificativos --}}
