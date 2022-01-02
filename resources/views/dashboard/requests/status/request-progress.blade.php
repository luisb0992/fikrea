{{-- Información de la solicitud de documentos --}}
<div class="col-md-12 mt-4">
    <h5>
        <i class="fas fa-thermometer-half text-info"></i>
        @lang('Estado de la Solicitud')
    </h5>
</div>

{{-- Estado de la solicitud --}}
<div class="main-card card">

    <div class="card-body">

        <div class="progress mb-2">
            <div class="progress-bar progress-bar-animated bg-primary progress-bar-striped" role="progressbar" 
                aria-valuenow="{{$request->progress}}" aria-valuemin="0" aria-valuemax="100" style="width: {{$request->progress}}%;">
                {{$request->progress}} %
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
                        <th class="text-center">@lang('Documentos proporcionados')</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($request->signers as $signer)
                        <tr>
                            <td data-label="@lang('Usuario')">
                                {{$signer->name}} {{$signer->lastname}}
                                @if ($signer->email)
                                <div>
                                    <a href="mailto:{{$signer->email}}">{{$signer->email}}</a>
                                </div>
                                @elseif ($signer->phone)
                                <div>
                                    <a href="tel:{{$signer->phone}}">{{$signer->phone}}</a>
                                </div>
                                @endif
                            </td>
                            <td data-label="@lang('Realizada')" class="text-center">
                                @if ($signer->requestIsDone())
                                    <i class="fas fa-check fa-2x text-success"></i>
                                    <div class="text-secondary">
                                        {{-- Muestra la fecha en la que ha sido subido el primer documento 
                                            de la solicitud
                                            Todos los documentos aportados a la solicitud se suben a la vez
                                        --}}
                                        @datetime($signer->request()->files()->first()->created_at)
                                    </div>
                                @else
                                    {{-- Si el firmante está atendiendo la solicitud --}}
                                    @if ($request->signerIsActive($signer))
                                        <div class="progress" style="height: 10px;"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            data-original-title="@lang('Ahora mismo revisando esta solicitud')."
                                        >
                                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-info w-100" role="progressbar"></div>
                                        </div>
                                    @else
                                        <i class="fa fa-times fa-2x text-danger"></i>
                                    @endif
                                    {{-- /Si el firmante está atendiendo la solicitud --}}
                                @endif
                            </td>
                            <td data-label="@lang('IP')">
                                @if ($signer->requestIsDone())
                                    {{-- Muestra la ip desde la que se hs subido el primer documento --}}
                                    {{$signer->request()->files()->first()->ip}}
                                @endif
                            </td>
                            <td data-label="@lang('Sistema')" class="text-secondary">
                                @if ($signer->requestIsDone())
                                    {{-- Muestra el sistema desde la que se hs subido el primer documento --}}
                                    @if ($signer->request()->files()->first()->device)
                                    <div class="text-info bold">
                                        @userdevice($signer->request()->files()->first()->device)
                                    </div>
                                    @endif
                                    <div>
                                        @useragent($signer->request()->files()->first()->user_agent)
                                    </div>
                                @endif
                            </td>
                            <td data-label="@lang('Ubicación')" class="text-center">
                                @if ($signer->requestIsDone())
                                {{-- Muestra la ubicación desde la que se hs subido el primer documento --}}
                                    @if ($signer->requestFiles->first()->latitude 
                                                                && 
                                        $signer->requestFiles->first()->longitude)
                                    
                                        <a target="_blank" class="btn btn-primary square" href="https://www.google.com/maps/search/?api=1&query={{$signer->requestFiles->first()->latitude}},{{$signer->requestFiles->first()->longitude}}">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </a>
                                    
                                    @endif    
                                @endif 
                            </td>
                            <td data-label="@lang('Documentos Aportados')" class="text-center">
                                @if ($signer->requestIsDone())
                                    {{$signer->requestFiles->count()}}
                                @else
                                    <span class="text-danger">@lang('Ninguno')</span>
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
                        <th class="text-center">@lang('Documentos proporcionados')</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
{{--/Estado de la solicitud --}}

{{-- / Información de la solicitud de documentos --}}
