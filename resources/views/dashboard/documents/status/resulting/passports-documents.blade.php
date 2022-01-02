{{-- Los documentos identificativos --}}
@foreach ($document->signers as $signer)
@foreach ($signer->passports as $passport)
<tr>
    <th class="text-center" data-label="@lang('Tipo')"><i class="fas fa-id-card fa-2x text-primary"></i></th>
    <td data-label="@lang('Validación')">
        @lang('Documento Identificativo')
    </td>
    <td data-label="@lang('Facial')" class="text-center">
        {{-- Resultado de la identificación facial --}}
        @if (!$passport->face_recognition)
            <i class="fas fa-times-circle fa-2x text-danger"
                data-toggle="tooltip" data-placement="top" data-original-title="@lang('El resultado del reconocimiento facial ha sido negativo')"></i>
        @else
            <i class="fas fa-check-circle fa-2x text-success"
            data-toggle="tooltip" data-placement="top" data-original-title="@lang('El resultado del reconocimiento facial ha sido positivo')"></i>
        @endif
        {{--/Resultado de la identificación facial --}}
    </td>
    <td class="text-secondary" data-label="@lang('Fecha')">@datetime($passport->created_at)</td>
    <td>
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
        <div class="text-info">{{$passport->ip}}</div>
    </td>
    <td data-label="@lang('Sistema')">
        <div class="text-info">{{$passport->user_agent}}</div>
    </td>
    <td class="text-center" data-label="@lang('Ubicación')">
        <div>
            <a target="_blank" class="btn btn-primary" href="https://www.google.com/maps/search/?api=1&query={{$passport->latitude}},{{$passport->longitude}}"
                data-toggle="tooltip" data-placement="top" data-original-title="@lang('Ver Ubicación')">
                <i class="fas fa-map-marker-alt fa-2x"></i>
            </a>
        </div>
    </td>
    <td class="text-center">
        <a href="@route('dashboard.passport.get', ['id' => $passport->id])"  class="btn btn-success square"
            data-toggle="tooltip" data-placement="top" data-original-title="@lang('Descargar los documentos')">
            <i class="fas fa-download"></i>
        </a>
    </td>
</tr>                        
@endforeach
@endforeach
{{--/Los documentos identificativos --}}