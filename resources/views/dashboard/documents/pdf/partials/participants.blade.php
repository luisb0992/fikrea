{{-- Listado de Personas que forman parte --}}
<p>
    <table>
        <thead>
            <tr>
                <th colspan="3">@lang('Participantes')</th>
            </tr>
        </thead>
    </table>
</p>

<p>
    <table>
        <thead>
            <tr>
                <th>@lang('Firmante')</th>
                <th>@lang('Observaciones')</th>
                <th>@lang('Estado')</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($document->signers as $signer)
            <tr>
                <td>
                    {{$signer->name}} {{$signer->lastname}}
                    <div>
                        @if ($signer->email)
                            <a href="mailto:{{$signer->email}}">{{$signer->email}}</a>
                        @elseif ($signer->phone)
                            <a href="tel:{{$signer->phone}}">{{$signer->phone}}</a>
                        @endif
                    </div>
                </td>
                <td>
                    @if ($signer->hasBeenCanceled())
                        <div>
                            <span class="bold">@date($signer->canceled_at)</span>
                            <span>
                                @lang('El proceso fue cancelado por el usuario firmante')
                            </span>
                        </div>
                        @if ($signer->canceled_subject)
                        <div>
                            {{$signer->canceled_subject}}
                        </div>
                        @endif
                    @else
                        {{-- Si no ha cancelado, puede que tenga procesos cancelados, los muestro --}}
                        @if ($signer->hasCanceledValidations())
                        @foreach (
                            $signer->validations()
                                ->filter( fn ($validation) => $validation->process->isCanceled())
                            as $validation
                            )
                            <div>
                                @lang("Validación") <b>@validation($validation)</b> @lang("cancelada")
                            </div>
                        @endforeach
                        @endif
                    @endif
                </td>
                <td class="text-center">
                    {{--Si el firmante ha cancelado el proceso en general--}}
                    @if ($signer->hasBeenCanceled())
                        @lang('Cancelado')
                    @else
                        {{--
                            En caso contrario verifico
                            Si el firmante tienes validaciones pendientes => Pendiente,
                            Si lo ha realizado todo o ha hecho X y cancelado el resto => Finalizado
                            pero con La info de los procesos que ha cancelado en Observaciones
                        --}}
                        @if ($signer->hasPendingValidations())
                            @lang('Pendiente')
                        @else
                            @lang('Realizado')
                        @endif
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>    
</p>

{{--/Listado de Personas que forman parte --}}