<table class="table table-striped">
<thead>
    @include('dashboard.requests.history.header-status-table')
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
                        @date($signer->request()->files()->first()->created_at)
	                    <div class="text-info">
	                        @time($signer->request()->files()->first()->created_at)
	                    </div>
                    </div>
                @else
                    <i class="fa fa-times fa-2x text-danger"></i>
                @endif
            </td>
            <td data-label="@lang('Ip')">
                @if ($signer->requestIsDone())
                    {{-- Muestra la ip desde la que se hs subido el primer documento --}}
                    {{$signer->request()->files()->first()->ip}}
                @endif
            </td>
            <td data-label="@lang('Sistema')" class="text-secondary">
                @if ($signer->requestIsDone())
                    {{-- Muestra el sistema desde la que se ha subido el primer documento --}}
                    @useragent($signer->request()->files()->first()->user_agent)
                @endif
            </td>
            <td data-label="@lang('Ubicación')" class="text-center">
                @if ($signer->requestIsDone())
                {{-- Muestra la ubicación desde la que se ha subido el primer documento --}}
                    @if ($signer->requestFiles->first()->latitude 
                                                && 
                        $signer->requestFiles->first()->longitude)
                    
                        <a target="_blank" class="btn btn-primary square" href="https://www.google.com/maps/search/?api=1&query={{$signer->requestFiles->first()->latitude}},{{$signer->requestFiles->first()->longitude}}">
                            <i class="fas fa-map-marker-alt"></i>
                        </a>
                    @else
                        <div class="text-center">
                            <div class="text-danger bold p-2">@lang('No se ha obtenido')</div>
                        </div>
                    @endif    
                @endif 
            </td>
            <td data-label="@lang('Documentos Aportados')" class="text-center">
                @if ($signer->requestIsDone())
                    {{$signer->requestFiles->count()}}
                @else
                    <span class="bg-danger text-white p-2">@lang('Ninguno')</span>
                @endif
            </td>
        </tr>
    @empty
        <td colspan="6" class="text-center">@lang('No hay registros')</td>
    @endforelse
</tbody>
<tfoot>
    @include('dashboard.requests.history.header-status-table')
</tfoot>
</table>