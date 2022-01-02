{{--  Visitas registradas para la veririficacion de datos  --}}
<div class="col-md-12 mt-4">
    <h5>
        <i class="fas fa-history text-info"></i>
        @lang('Listado de visitas de los usuarios')
    </h5>
</div>

<div class="col-md-12 mb-5">
    <div class="main-card card">
        <div class="card-body table-responsive">
            <div class="table-responsive col-md-12">
                <table class="mb-0 table table-striped">
                    <thead>
                        @include('dashboard.verificationform.partials.history.header-history-verificationform-table')
                    </thead>
                    <tbody>
                        @forelse($verificationForm->visits as $visit)
                            <tr>
                                <th scope="row" data-label="@lang('Visita') #">{{ $loop->iteration }}</th>

                                <td data-label="@lang('Firmante')">
                                    {{ $visit->signer->name }} {{ $visit->signer->lastname }}
                                    <div>
                                        @if ($visit->signer->email)
                                            <a href="mailto:{{ $visit->signer->email }}">{{ $visit->signer->email }}</a>
                                        @else
                                            <a href="tel:{{ $visit->signer->phone }}">{{ $visit->signer->phone }}</a>
                                        @endif
                                    </div>
                                </td>

                                <td data-label="@lang('Hora Inicio')">
                                    @if ($visit->starts_at)
                                        @date($visit->starts_at)
                                        <div class="text-info">
                                            @time($visit->starts_at)
                                        </div>
                                    @endif
                                </td>

                                <td data-label="@lang('Hora Fin')">
                                    @if ($visit->ends_at)
                                        @date($visit->ends_at)
                                        <div class="text-info">
                                            @time($visit->ends_at)
                                        </div>
                                    @else
                                        <span class="bg-danger text-white nowrap p-2">@lang('No Completado')</span>
                                    @endif
                                </td>

                                <td data-label="@lang('Duración')" class="text-center">
                                    <div class="text-secondary">{{ gmdate('i:s', $visit->duration) }}</div>
                                </td>

                                <td data-label="@lang('Ip')">
                                    <div class="text-info">{{ $visit->ip }}</div>
                                </td>

                                <td data-label="@lang('Sistema')">
                                    @if ($visit->device)
                                        <div class="text-info bold">
                                            @userdevice($visit->device)
                                        </div>
                                    @endif
                                    @useragent($visit->user_agent)
                                </td>

                                <td data-label="@lang('Ubicación')" class="text-center">
                                    <div>
                                        @if ($visit->latitude && $visit->longitude)
                                            <a target="_blank" class="btn btn-primary square"
                                                href="https://www.google.com/maps/search/?api=1&query={{ $visit->latitude }},{{ $visit->longitude }}">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </a>
                                        @else
                                            <div class="text-center">
                                                <div class="text-danger bold p-2">@lang('No se ha obtenido')</div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">@lang('Ninguna visita registrada')</td>
                            </tr>
                        @endforelse

                    </tbody>
                    <tfoot>
                        @include('dashboard.verificationform.partials.history.header-history-verificationform-table')
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
