{{-- Validaciones de videos efectuadas
    Si este documento no tiene validaciones de video no se muestra nada --}}
@if ($document->mustBeValidateByVideoFile() && $document->videos->count())

    <p>
    <table>
        <thead>
            <tr>
                <th colspan="2">@lang('Validaciones de videos')</th>
            </tr>
        </thead>
    </table>
    </p>

    @foreach ($document->signers as $signer)
        @foreach ($signer->videos as $video)
            <p>
            <table>
                <thead>
                    <tr>
                        <th colspan="2">
                            {{ $signer->name }} {{ $signer->lastname }} [ Video ]
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>@lang('Firmante')</th>
                        <td> {{ $signer->name }} {{ $signer->lastname }} {{ $signer->dni }} {{ $signer->email }}</td>
                    </tr>
                    <tr>
                        <td>@lang('Archivo')</td>
                        <td class="medium">
                            {{ $video->path }}
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Duración de la Grabación')</td>
                        <td>
                            {{ $video->duration }} mm:ss
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Dirección IP')</td>
                        <td>
                            <div>
                                {{ $video->ip }}
                            </div>
                            <div>
                                @hostname($video->ip)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Sistema Utilizado')</td>
                        <td>
                            @if ($video->device)
                                <div>
                                    @userdevice($video->device)
                                </div>
                            @endif
                            @useragent($video->user_agent)
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Ubicación')</td>
                        <td>
                            <div>
                                {{ $video->latitude }} {{ $video->longitude }}
                            </div>
                            <div>
                                @if ($video->latitude && $video->longitude)
                                    <a
                                        href="https://www.google.com/maps/search/?api=1&query={{ $video->latitude }},{{ $video->longitude }}">
                                        @lang('Ver en Google Maps')
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Fecha de Grabación')</td>
                        <td>@datetime($video->created_at)</td>
                    </tr>
                </tbody>
            </table>
            </p>

            {{-- Próxima página --}}
            <div class="break"></div>
            {{-- / Próxima página --}}

        @endforeach

        {{-- comentario de la validacion --}}
        @if ($signer->getIfCommentExists(\App\Enums\ValidationType::VIDEO_FILE_VERIFICATION))
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
                                <small>"{{ $signer->getIfCommentExists(\App\Enums\ValidationType::VIDEO_FILE_VERIFICATION) }}"</small>
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
{{-- / Validaciones de video efectuadas --}}
