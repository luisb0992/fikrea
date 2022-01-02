<div class="table-responsive">    
    <table class="mb-0 table table-striped">
        <thead>
            @include('dashboard.partials.document-request-history.table-header')
        </thead>
        <tbody>
            @forelse($histories as $history)
            <tr>
                <td data-label="#">
                    {{$loop->iteration}}
                </td>

                {{--Destinatario--}}
                <td data-label="@lang('Destinatario')">
                    @if ($history->signer)
                        @if ($history->signer->name || $history->signer->lastname)
                        {{$history->signer->name}} {{$history->signer->lastname}}
                        @else
                            @if ($history->signer->email)
                            {{$history->signer->email}}
                            @else
                                @if ($history->signer->phone)
                                {{$history->signer->phone}}
                                @endif
                            @endif
                        @endif
                    @else
                    <span class="text-warning">
                        Desconocido
                    </span>
                    @endif
                </td>

                {{--Detalles de visita--}}
                <td class="align-middle" data-label="@lang('Duración')">
                    <div>
                        <span class="text-success">{{$history->starts_at->format('d-m-Y H:i:s')}}</span>
                    </div>
                    <div>
                        <span class="text-info">
                            {{$history->ends_at? $history->ends_at->format('d-m-Y H:i:s'):''}}
                        </span>
                    </div>
                    @if ($history->ends_at)
                    <div>
                        <span class="text-warning">
                            @lang('Duración'): {{ date("i:s", $history->duration ?? 0) }}  mm:ss</span>
                    </div>
                    @endif
                </td>

                {{--Ip--}}
                <td data-label="@lang('IP')">
                    {{$history->ip}}
                </td>

                {{--Navegador--}}
                <td data-label="@lang('Navegador')">
                    {{$history->getBrowser()}}
                </td>

                {{--Sistema operativo y dispositivo--}}
                <td data-label="@lang('Sistema')">
                    {{$history->getPlatform()}}
                </td>
                
                {{--Geolocalización--}}
                <td class="text-center">        
                    <div>
                        <a target="_blank" class="btn btn-primary square" href="https://www.google.com/maps/search/?api=1&query={{$visit->latitude??null}},{{$visit->longitude??null}}">
                            <i class="fas fa-map-marker-alt"></i>
                        </a>
                    </div>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center">@lang('Ningún elemento registrado')</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            @include('dashboard.partials.document-request-history.table-header')
        </tfoot>
    </table>
</div>

{{--Paginador --}}
<div class="paginator-wrapper mt-1 ml-1">
    {{$histories->links()}}

    <em>
        @lang('Se muestran :files de un total de :total registros.', [
            'files' => $histories->count(),
            'total' => $histories->total(),
        ])
    </em>
    
</div>
{{--/Paginador --}}