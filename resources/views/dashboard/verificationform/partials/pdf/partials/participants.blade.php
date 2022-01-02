{{-- Listado de Personas que forman parte --}}
<p>
<table>
    <thead>
        <tr>
            <th colspan="2">@lang('Participantes en el proceso')</th>
        </tr>
    </thead>
    <tbody>

        {{-- Creador --}}
        <tr>
            <td>
                {{ $verificationForm->user->name }} {{ $verificationForm->user->lastname }}
                <div>
                    @if ($verificationForm->user->email)
                        <a href="mailto:{{ $verificationForm->user->email }}">{{ $verificationForm->user->email }}</a>
                    @elseif ($verificationForm->user->phone)
                        <a href="tel:{{ $verificationForm->user->phone }}">{{ $verificationForm->user->phone }}</a>
                    @endif
                </div>
            </td>
            <td>
                @lang('Solicitante')
            </td>
        </tr>
        {{-- Creador --}}

        {{-- Firmantes --}}
        @foreach ($verificationForm->signers as $signer)
            <tr>
                <td>
                    {{ $signer->name }} {{ $signer->lastname }}
                    <div>
                        @if ($signer->email)
                            <a href="mailto:{{ $signer->email }}">{{ $signer->email }}</a>
                        @elseif ($signer->phone)
                            <a href="tel:{{ $signer->phone }}">{{ $signer->phone }}</a>
                        @endif
                    </div>
                </td>
                <td>
                    @lang('Solicitado')
                </td>
            </tr>
        @endforeach
        {{-- /Firmantes --}}
    </tbody>
</table>
</p>
{{-- /Listado de Personas que forman parte --}}
