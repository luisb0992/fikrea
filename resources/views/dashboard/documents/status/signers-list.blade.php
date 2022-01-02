{{-- Listado de Firmantes --}}
<div class="col-md-12 mt-4">
    <h5>
        <i class="fas fa-user-friends text-secondary"></i>
        @lang('Listado de firmantes')
    </h5>
</div>

<div class="col-md-12">
    <div class="main-card card">

        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>@lang('Firmante')</th>
                        <th class="text-center">@lang('Visitas')</th>
                        <th>@lang('Observaciones')</th>
                        <th>@lang('Estado')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($document->signers as $signer)
                    <tr>
                        <td data-label="@lang('Firmante')">
                            {{$signer->name}} {{$signer->lastname}}
                            <div>
                                @if ($signer->email)
                                    <a href="mailto:{{$signer->email}}">{{$signer->email}}</a>
                                @elseif ($signer->phone)
                                    <a href="tel:{{$signer->phone}}">{{$signer->phone}}</a>
                                @endif
                            </div>
                        </td>
                        <td class="text-center" data-label="@lang('Visitas')">
                            {{$signer->visits->count()}}
                        </td>
                        <td data-label="@lang('Observaciones')">
                            @if ($signer->hasBeenCanceled())
                                <div>
                                    <span class="bold text-primary">@date($signer->canceled_at)</span>
                                    <span class="text-secondary">
                                        @lang('El proceso fue cancelado por el usuario firmante')
                                    </span>
                                </div>
                                <div>
                                    {{$signer->canceled_subject}}
                                </div>
                            @endif
                        </td>
                        <td data-label="@lang('Estado')">

                            {{--Si el firmante ha cancelado el proceso en general--}}
                            @if ($signer->hasBeenCanceled())
                                <i class="fa fa-times fa-2x text-danger"></i>
                            @else
                                {{--
                                    En caso contrario verifico
                                    Si el firmante tienes validaciones pendientes => Pendiente,
                                    Si lo ha realizado todo o ha hecho X y cancelado el resto => Finalizado
                                --}}
                                @if ($signer->hasPendingValidations())

                                    {{-- Si no es el creador, muestro su estado sobre el workspace --}}
                                    @if (!$signer->creator)
                                        @if ($signer->active)
                                            <span data-toggle="tooltip"
                                                data-placement="left"
                                                data-original-title="@lang('Activo en su área de trabajo')"
                                                class="ml-auto badge badge-pill badge-success p-2">
                                                @lang('En línea')
                                            </span>
                                        @endif
                                    @endif

                                    @lang('Pendiente')
                                @else
                                    <i class="fas fa-check fa-2x text-success"></i>
                                @endif
                            @endif

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
{{--/Listado de Firmantes --}}