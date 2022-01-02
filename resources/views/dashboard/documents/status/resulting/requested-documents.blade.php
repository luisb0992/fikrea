{{-- Los documentos aportados --}}
@foreach ($document->signers as $signer)
@if ($signer->request())
@foreach ($signer->request()->files as $file)
<tr>

    <th class="text-center" data-label="@lang('Tipo')">
        @include('dashboard.partials.file-icon', ['type' => $file->type])
    </th>

    <td data-label="@lang('Validación')">
        @lang('Solicitud de Documento')
    </td>

    <td data-label="@lang('Facial')" class="text-center">
        <i class="fas fa-ellipsis-h"></i>
    </td>

    <td class="text-secondary" data-label="@lang('Fecha')">
        @datetime($file->created_at)
    </td>

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

    <td data-label="@lang('Ip')">
        {{$file->ip}}
    </td>

    <td data-label="@lang('Sistema')">
        @if ($file->device)
        <div class="text-info bold">
            @userdevice($file->device)
        </div>
        @endif
        <div>
            @useragent($file->user_agent)
        </div>
    </td>

    <td data-label="@lang('Ubicación')" class="text-center">
        @if ($file->latitude && $file->longitude)
            <a target="_blank" class="btn btn-primary square" href="https://www.google.com/maps/search/?api=1&query={{$file->latitude}},{{$file->longitude}}">
                <i class="fas fa-map-marker-alt"></i>
            </a>
        @endif
    </td>

    <td class="text-center">
        <a class="btn btn-success square" href="@route('dashboard.document.request.download.file', ['id' => $file->id])">
            <i class="fas fa-download"></i>
        </a>
    </td>

</tr>
@endforeach
@endif

@endforeach
{{--/Los documentos aportados --}}