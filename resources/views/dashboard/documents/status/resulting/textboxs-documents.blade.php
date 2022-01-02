{{-- El documento con los textos 
        Mostramos una línea separada para cada firmante del documento
        Sólo se muestran aquellos firmantes que deben realizar la edición de documentos
--}}
@foreach($document->signers as $signer)
    @if ($signer->mustValidate($document, \App\Enums\ValidationType::TEXT_BOX_VERIFICATION))
    <tr>
        <th class="text-center" data-label="@lang('Tipo')"><i class="fas fa-file-alt fa-2x text-danger"></i></th>
        <td data-label="@lang('Validación')">@lang('Editor de documento')</td>
        <td data-label="@lang('Facial')" class="text-center">
            <i class="fas fa-ellipsis-h"></i>
        </td>
        <td class="text-secondary" data-label="@lang('Fecha')">@datetime($document->update_at)</td>
        <td data-label="@lang('Usuario')">
                
            @if ($signer->name || $signer->lastname)
            {{$signer->name}} {{$signer->lastname}}
            @elseif ($signer->email)
            {{$signer->email}}
            @else
            {{$signer->phone}}
            @endif
                
        </td>
        <td data-label="@lang('IP')">
            {{-- Sólo mostramos la dirección IP de la primera firma
                asumiendo que para todas ellas no ha cambiado    
            --}}
            @if ($signer->boxs->first() && $signer->boxs->first()->ip)
                <div class="text-info">{{$signer->boxs->first()->ip}}</div>
            @endif
        </td>
        <td data-label="@lang('Sistema')">
            {{-- Sólo mostramos el sistema de la primera firma 
                asumiendo que para todas ellas no ha cambiado    
            --}}
            @if ($signer->boxs->first() && $signer->boxs->first()->device)
                @if ($signer->boxs->first()->device)
                <div class="text-info bold">
                    @userdevice($signer->boxs->first()->device)
                </div>
                @endif
                <div class="text-info">
                    @useragent($signer->boxs->first()->user_agent)
                </div>
            @endif
        </td>
        <td class="text-center" data-label="@lang('Ubicación')">
            {{-- Sólo mostramos la primera ubicación registrada
                asumiendo que para todas ellas no ha cambiado    
            --}} 
            @if ($signer->boxs->first() && $signer->boxs->first()->latitude && $signer->boxs->first()->longitude)
            <div>
                <a target="_blank" class="btn btn-primary square" href="https://www.google.com/maps/search/?api=1&query={{$signer->boxs->first()->latitude}},{{$signer->boxs->first()->longitude}}"
                    data-toggle="tooltip" data-placement="top" data-original-title="@lang('Ver Ubicación')">
                    <i class="fas fa-map-marker-alt fa-2x"></i>
                </a>
            </div>
            @endif
        </td>
        <td class="text-center">

            @if ($document->isInProcess())
            {{-- Si el documento se está procesando en este momento (para generar el documento firmado --}}
                <span class="btn btn-secondary square"
                    data-toggle="tooltip" data-placement="top" data-original-title="@lang('El documento se está procesando...')">
                    <i class="fas fa-hourglass-half"></i>
                </span> 
            @else
                {{-- El documento está listo para su descarga --}}
                <a href="@route('dashboard.document.signed.get', ['id' => $document->id])"  class="btn btn-success square"
                    data-toggle="tooltip" data-placement="top" data-original-title="@lang('Descargar el Documento')">
                    <i class="fas fa-download"></i>
                </a>
            @endif

        </td>
    </tr>
    @endif
@endforeach
{{--/El documento firmado --}}