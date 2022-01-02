{{--
    Validaciones de captura de pantalla efectuadas
    Si este documento no tiene validaciones de captura de pantalla no se muestra nada
--}}      
@if ($document->mustBeValidateByScreenCapture() && $document->captures->count() > 0)

<p>
    <table>
        <thead>
            <tr>
                <th colspan="2">@lang('Capturas de pantalla')</th>
            </tr>
        </thead>
    </table>    
</p>

@foreach ($document->signers as $signer)
    @foreach ($signer->captures as $capture)
        <p>
            <table> 
                <thead>
                    <tr>
                        <th colspan="2">
                            {{$signer->name}} {{$signer->lastname}} [ @lang('Captura de Pantalla') ]
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>@lang('Firmante')</th>
                        <td> {{$signer->name}} {{$signer->lastname}} {{$signer->dni}} {{$signer->email}}</td>
                    </tr>
                    <tr>
                        <td>@lang('Archivo')</td>
                        <td class="medium">
                            {{$capture->path}}
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Duración de la Grabación')</td>
                        <td>
                            {{$capture->duration}} mm:ss
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Dirección IP')</td>
                        <td>
                            <div>
                                {{$capture->ip}}
                            </div>
                            <div>
                                @hostname($capture->ip)
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Sistema Utilizado')</td>
                        <td>@if ($capture->device)
                            <div>
                                @userdevice($capture->device)
                            </div>
                            @endif
                            @useragent($capture->user_agent)
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Ubicación')</td>
                        <td>
                            <div>
                                {{$capture->latitude}} {{$capture->longitude}}
                            </div>
                            <div>
                                @if ($capture->latitude && $capture->longitude)
                                <a href="https://www.google.com/maps/search/?api=1&query={{$capture->latitude}},{{$capture->longitude}}">
                                    @lang('Ver en Google Maps')
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('Fecha de Grabación')</td>
                        <td>@datetime($capture->created_at)</td>
                    </tr>
                </tbody>
            </table> 
        </p>
        
        {{-- Próxima página--}}
        <div class="break"></div>
        {{-- / Próxima página--}}
        
        @endforeach

        {{-- comentario de la validacion --}}
        @if ($signer->getIfCommentExists(\App\Enums\ValidationType::SCREEN_CAPTURE_VERIFICATION))
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
                                <small>"{{ $signer->getIfCommentExists(\App\Enums\ValidationType::SCREEN_CAPTURE_VERIFICATION) }}"</small>
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
{{--/ Validaciones de captura de pantalla efectuadas--}}