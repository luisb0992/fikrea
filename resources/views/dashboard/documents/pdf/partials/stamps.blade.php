{{--
    Sellos estampados sobre el documento
    Si este documento no tiene estampas de sellos no se muestra nada
--}}
@if ($document->stamps->isNotEmpty())
<p>
    <table>
        <thead>
            <tr>
                <th colspan="2">@lang('Sellos estampados')</th>
            </tr>
        </thead>
    </table>    
</p>

@foreach ($document->stamps as $stamp)
<p>
    <table>
        <thead>
            <tr>
                <th colspan="2">
                    @lang('Sellado página :page', ['page' => $stamp->page])
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>@lang('Firmante')</td>
                <td class="text-center">
                    <div>
                       {{-- Los sellos los coloca el autor/creador del documento --}}
                       {{$document->user->name}} {{$document->user->lastname}}
                    </div>
                </td>
            </tr>
            <tr>
                <td>@lang('Sello')</td>
                <td class="text-center">
                    <div>
                        <img src="{{$stamp->stamp}}" alt="" />
                    </div>
                </td>
            </tr>
            <tr>
                <td>@lang('Página')</td>
                <td>
                    {{$stamp->page}}
                </td>
            </tr>
            <tr>
                <td>@lang('Fecha de Estampado')</td>
                <td>@datetime($stamp->created_at)</td>
            </tr>
        </tbody>
    </table>  
</p>

{{-- Próxima página--}}
<div class="break"></div>
{{-- / Próxima página--}}

@endforeach
@endif
{{--/ Sellos estampados sobre el documento --}}