{{-- Las capturas de pantalla --}}
@foreach ($document->signers as $signer)
@foreach ($signer->captures as $capture)
<tr>
    <th class="text-center" data-label="@lang('Tipo')"><i class="fas fa-desktop fa-2x text-secondary"></i></th>
    <td data-label="@lang('Validación')">@lang('Captura de Pantalla')</td>
    <td data-label="@lang('Facial')" class="text-center">
        <i class="fas fa-ellipsis-h"></i>
    </td>
    <td class="text-secondary" data-label="@lang('Fecha')">@datetime($capture->created_at)</td>
    <td data-label="@lang('Usuario')">
        <div>
            @if ($signer->name || $signer->lastname)
            {{$signer->name}} {{$signer->lastname}}
            @elseif ($signer->email)
            {{$signer->email}}
            @else
            {{$signer->phone}}
            @endif
        </div>
    </td>
    <td data-label="@lang('IP')">
        <div class="text-info">{{$capture->ip}}</div>
    </td>
    <td data-label="@lang('Sistema')">
        @if ($capture->device)
        <div class="text-info bold">
            @userdevice($capture->device)
        </div>
        @endif
        <div class="text-info">
            @useragent($capture->user_agent)
        </div>
    </td>
    <td class="text-center" data-label="@lang('Ubicación')">
        <div>
            <a target="_blank" class="btn btn-primary" href="https://www.google.com/maps/search/?api=1&query={{$capture->latitude}},{{$capture->longitude}}"
                data-toggle="tooltip" data-placement="top" data-original-title="@lang('Ver Ubicación')">
                <i class="fas fa-map-marker-alt fa-2x"></i>
            </a>
        </div>
    </td>
    <td class="text-center">
        <a href="@route('dashboard.screen.get', ['id' => $capture->id])"  class="btn btn-success square"
            data-toggle="tooltip" data-placement="top" data-original-title="@lang('Descargar la Grabación')">
            <i class="fas fa-download"></i>
        </a>
    </td>
</tr>                        
@endforeach
@endforeach
{{--/Las capturas de pantalla --}}