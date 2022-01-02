{{-- Información de la verificación de documentos --}}
<div class="col-md-12 mt-4">
    <h5>
        <i class="fas fa-thermometer-half text-info"></i>
        @lang('Estado de la verificación de datos')
    </h5>
</div>

{{-- Estado de la verificación --}}
<div class="col-md-12 mb-4">
    <div class="main-card card">

        <div class="card-body">

            <div class="progress mb-2">
                <div class="progress-bar progress-bar-animated bg-primary progress-bar-striped" role="progressbar"
                    aria-valuenow="{{ $verificationForm->progress }}" aria-valuemin="0" aria-valuemax="100"
                    style="width: {{ $verificationForm->progress }}%;">
                    {{ $verificationForm->progress }} %
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>@lang('Usuario')</th>
                            <th class="text-center">@lang('Realizada')</th>
                            <th>@lang('IP')</th>
                            <th>@lang('Sistema')</th>
                            <th class="text-center">@lang('Ubicación')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($verificationForm->signers as $signer)
                            <tr>
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
                                <td data-label="@lang('Realizada')" class="text-center">

                                    {{-- si realizo la verificación --}}
                                    @if ($signer->verificationFormIsDone())

                                        <i class="fas fa-check fa-2x text-success"></i>
                                        <div class="text-secondary">
                                            @datetime($signer->verificationform_at)
                                        </div>

                                        {{-- Si el firmante está atendiendo la verificación --}}
                                    @else
                                        @if ($verificationForm->signerIsActive($signer))
                                            <div class="progress" style="height: 10px;" data-toggle="tooltip"
                                                data-original-title="@lang('Ahora mismo revisando esta verificación')">
                                                <div class="progress-bar progress-bar-striped progress-bar-animated bg-info w-100"
                                                    role="progressbar"></div>
                                            </div>
                                        @else
                                            <i class="fa fa-times fa-2x text-danger"></i>
                                        @endif
                                    @endif
                                </td>
                                <td data-label="@lang('IP')">

                                    {{-- Muestra la ip desde la que se ha verificado el primer campo --}}
                                    @if ($signer->verificationFormIsDone())
                                        {{ $verificationForm->fieldsRow()->first()->ip }}
                                    @else
                                        @lang('No realizado aún')
                                    @endif
                                </td>
                                <td data-label="@lang('Sistema')" class="text-secondary">

                                    {{-- Muestra el sistema desde la que se hs subido el primer campo --}}
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
                                        @lang('No realizado aún')
                                    @endif
                                </td>
                                <td data-label="@lang('Ubicación')" class="text-center">

                                    {{-- Muestra la ubicación desde la que se hs subido el primer campo --}}
                                    @if ($signer->verificationFormIsDone())
                                        @if ($verificationForm->fieldsRow()->first()->latitude && $verificationForm->fieldsRow()->first()->longitude)

                                            <a target="_blank" class="btn btn-primary square"
                                                href="https://www.google.com/maps/search/?api=1&query={{ $verificationForm->fieldsRow()->first()->latitude }},{{ $verificationForm->fieldsRow()->first()->longitude }}">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </a>

                                        @endif
                                    @else
                                        @lang('No realizado aún')
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <td colspan="6" class="text-center">@lang('No hay registros')</td>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>@lang('Usuario')</th>
                            <th class="text-center">@lang('Realizada')</th>
                            <th>@lang('IP')</th>
                            <th>@lang('Sistema')</th>
                            <th class="text-center">@lang('Ubicación')</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- /Estado de la verificación --}}

{{-- / Información de la verificación de documentos --}}
