{{--
    Envíos realizados del documento
    Si este documento no se ha enviado no se muestra nada
--}}
@if ($document->sharings->count())
    <p>
        <table>
            <thead>
                <tr>
                    <th colspan="2">@lang('Envíos realizados de este documento')</th>
                </tr>
            </thead>
        </table>
    </p>

    <p>
        <table>
            <thead>
                <tr>
                    <th>@lang('Fecha de Envío')</th>
                    <th>@lang('Destinatarios')</th>
                    <th>@lang('Tipo de envío')</th>
                    <th>@lang('Atendido')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($document->sharings as $sharing)
                    <tr>
                        <td>
                            @date($sharing->sent_at)
                            <div>
                                @time($sharing->sent_at)
                            </div>
                        </td>
                        <td>
                            @isset($noSigners)
                                @foreach ($sharing->contacts as $contact)
                                    <div>
                                        @if ($contact->name || $contact->lastname)
                                            {{ $contact->name }} {{ $contact->lastname }}
                                        @endif

                                        @if ($contact->email)
                                            {{ $contact->email }}
                                        @endif

                                        @if ($contact->phone)
                                            {{ $contact->phone }}
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                @foreach ($sharing->signers->filter(fn($signer) => !$signer->creator) as $signer)
                                    <div>
                                        @if ($signer->name || $signer->lastname)
                                            {{ $signer->name }} {{ $signer->lastname }}
                                        @endif

                                        @if ($signer->email)
                                            {{ $signer->email }}
                                        @endif

                                        @if ($signer->phone)
                                            {{ $signer->phone }}
                                        @endif
                                    </div>
                                @endforeach
                            @endisset
                        </td>
                        <td class="text-center">
                            {{ \App\Enums\DocumentSharingType::fromValue($sharing->type) }}
                        </td>
                        <td>
                            @if ($sharing->visited_at)
                                @date($sharing->visited_at)
                                <div>
                                    @time($sharing->visited_at)
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </p>

    {{-- Próxima página --}}
    <div class="break"></div>
    {{-- / Próxima página --}}

@endif
{{-- /Envíos realizados del documento --}}
