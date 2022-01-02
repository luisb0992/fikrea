{{-- Validaciones de audio efectuadas
    Si este documento no tiene validaciones de audio no se muestra nada --}}
@if ($document->mustBeValidateByAudioFile() && $document->audios->count())

    <p>
    <table>
        <thead>
            <tr>
                <th colspan="2">@lang('Validaciones de audios')</th>
            </tr>
        </thead>
    </table>
    </p>

    @foreach ($document->signers as $signer)

        @foreach ($signer->audios as $audio)
            <p>
            <table>
                <thead>
                    <tr>
                        <th colspan="2">
                            {{ $signer->name }} {{ $signer->lastname }} [ Audio ]
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>@lang('Validante')</th>
                        <td>{{ $signer->name }} {{ $signer->lastname }} {{ $signer->dni }} {{ $signer->email }}</td>
                    </tr>
                    <tr>
                        <td>@lang('Archivo')</td>
                        <td class="medium">
                            {{ $audio->path }}
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Duración de la Grabación')</td>
                        <td>
                            {{ $audio->duration }} mm:ss
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Dirección IP')</td>
                        <td>
                            <div>
                                {{ $audio->ip }}
                            </div>
                            <div>
                                @hostname($audio->ip)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Sistema Utilizado')</td>
                        <td>
                            @if ($audio->device)
                                <div>
                                    @userdevice($audio->device)
                                </div>
                            @endif
                            @useragent($audio->user_agent)
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Ubicación')</td>
                        <td>
                            <div>
                                {{ $audio->latitude }} {{ $audio->longitude }}
                            </div>
                            <div>
                                @if ($audio->latitude && $audio->longitude)
                                    <a
                                        href="https://www.google.com/maps/search/?api=1&query={{ $audio->latitude }},{{ $audio->longitude }}">
                                        @lang('Ver en Google Maps')
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Fecha de Grabación')</td>
                        <td>@datetime($audio->created_at)</td>
                    </tr>
                </tbody>
            </table>
            </p>

            {{-- Próxima página --}}
            <div class="break"></div>
            {{-- / Próxima página --}}

        @endforeach

        {{-- comentario de la validacion --}}
        @if ($signer->getIfCommentExists(\App\Enums\ValidationType::AUDIO_FILE_VERIFICATION))
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
                                    <small>"{{ $signer->getIfCommentExists(\App\Enums\ValidationType::AUDIO_FILE_VERIFICATION) }}"</small>
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
{{-- / Validaciones de audio efectuadas --}}
