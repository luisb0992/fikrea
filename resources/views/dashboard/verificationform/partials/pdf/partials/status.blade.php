{{-- Estado --}}
<p>
<table>
    <thead>
        <tr>
            <th colspan="2">@lang('Estado de la verificación de datos')</th>
        </tr>
        <tr>
            <td>@lang('Progreso')</td>
            <td>{{ $verificationForm->progress }} %</td>
        </tr>
    </thead>
</table>
</p>

@foreach ($verificationForm->signers as $signer)
    <p>
    <table>
        <tbody>

            <tr>
                <td>@lang('Usuario')</td>
                <td data-label="@lang('Usuario')">
                    {{ $signer->name }} {{ $signer->lastname }}
                    @if ($signer->email)
                        <div>
                            <a href="mailto:{{ $signer->email }}">{{ $signer->email }}</a>
                        </div>
                    @elseif ($signer->phone)
                        <div>
                            <a href="tel:{{ $signer->phone }}">{{ $signer->phone }}</a>
                        </div>
                    @endif
                </td>
            </tr>

            <tr>
                <td>@lang('Realizada')</td>
                <td data-label="@lang('Realizada')">

                    {{-- si realizo la verificación --}}
                    @if ($signer->verificationFormIsDone())

                        <div class="text-secondary">
                            @datetime($signer->verificationform_at)
                        </div>

                    {{-- Si el firmante está atendiendo la verificación --}}
                    @else
                        @lang('Pendiente')
                    @endif
                </td>
            </tr>

            <tr>
                <td>@lang('IP')</td>
                <td data-label="@lang('IP')">
                    @if ($signer->verificationFormIsDone())
                        {{ $verificationForm->fieldsRow()->first()->ip ?? '---' }}
                    @else
                        @lang('Pendiente')
                    @endif
                </td>
            </tr>

            <tr>
                <td>@lang('Sistema')</td>
                <td data-label="@lang('Sistema')" class="text-secondary">
                    @if ($signer->verificationFormIsDone())
                        @if ($verificationForm->fieldsRow()->first()->device)
                            <div class="text-info bold">
                                @userdevice($verificationForm->fieldsRow()->first()->device)
                            </div>
                        @endif
                        <div>
                            @useragent($verificationForm->fieldsRow()->first()->user_agent)
                        </div>
                    @else
                        @lang('Pendiente')
                    @endif
                </td>
            </tr>

            <tr>
                <td>@lang('Ubicación')</td>
                <td data-label="@lang('Ubicación')">
                    @if ($signer->verificationFormIsDone())
                        @if ($verificationForm->fieldsRow()->first()->latitude && $verificationForm->fieldsRow()->first()->longitude)
                            <a target="_blank"
                                href="https://www.google.com/maps/search/?api=1&query={{ $verificationForm->fieldsRow()->first()->latitude }},{{ $verificationForm->fieldsRow()->first()->longitude }}">
                                @lang('Ubicación')
                            </a>
                        @endif
                    @else
                        @lang('Pendiente')
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
    </p>
@endforeach
{{-- /Estado --}}
