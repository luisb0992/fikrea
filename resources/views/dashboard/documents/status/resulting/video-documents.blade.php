{{-- Los archivos de video --}}
@foreach ($document->signers as $signer)
@foreach ($signer->videos as $video)
<tr>
    <th class="text-center" data-label="@lang('Tipo')"><i class="fas fa-file-video fa-2x text-info"></i></th>
    <td data-label="@lang('Validación')">@lang('Archivo de Video')</td>
    <td data-label="@lang('Facial')" class="text-center">
        <i class="fas fa-ellipsis-h"></i>
    </td>
    <td class="text-secondary" data-label="@lang('Fecha')">@datetime($video->created_at)</td>
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
        <div class="text-info">{{$video->ip}}</div>
    </td>
    <td data-label="@lang('Sistema')">
        @if ($video->device)
        <div class="text-info bold">
            @userdevice($video->device)
        </div>
        @endif
        <div class="text-info">
            @useragent($video->user_agent)
        </div>
    </td>
    <td class="text-center" data-label="@lang('Ubicación')">
        <div>
            <a target="_blank" class="btn btn-primary" href="https://www.google.com/maps/search/?api=1&query={{$video->latitude}},{{$video->longitude}}"
                data-toggle="tooltip" data-placement="top" data-original-title="@lang('Ver Ubicación')">
                <i class="fas fa-map-marker-alt fa-2x"></i>
            </a>
        </div>
    </td>
    <td class="text-center">
        <a href="@route('dashboard.video.get', ['id' => $video->id])"  class="btn btn-success square"
            data-toggle="tooltip" data-placement="top" data-original-title="@lang('Descargar la Grabación')">
            <i class="fas fa-download"></i>
        </a>
    </td>
</tr>                        
@endforeach
@endforeach
{{--/Los archivos de video --}}