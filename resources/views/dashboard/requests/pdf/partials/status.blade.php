{{-- Estado de la solicitud --}}
<p>
    <table>
        <thead>
            <tr>
                <th colspan="2">@lang('Estado de la solicitud de documentos')</th>
            </tr>
            <tr>
                <td>@lang('Progreso')</td>
                <td>{{$request->progress}} %</td>
            </tr>
        </thead>
    </table>    
</p>

@forelse ($request->signers as $signer)
<p>
    <table>
        <tbody>

            <tr>
                <td>@lang('Usuario')</td>
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
            </tr>

            <tr>
                <td>@lang('Realizada')</td>
                <td data-label="@lang('Realizada')">
                    @if ($signer->requestIsDone())
                        <div class="text-secondary">
                            @datetime($signer->request()->files()->first()->created_at)
                        </div>
                    @else
                        @lang('Pendiente')
                    @endif
                </td>
            </tr>

            <tr>
                <td>@lang('IP')</td>
                <td data-label="@lang('IP')">
                    @if ($signer->requestIsDone())
                        {{-- Muestra la ip desde la que se hs subido el primer documento --}}
                        {{$signer->request()->files()->first()->ip}}
                    @endif
                </td>
            </tr>

            <tr>
                <td>@lang('Sistema')</td>
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
            </tr>

            <tr>
                <td>@lang('Ubicación')</td>
                <td data-label="@lang('Ubicación')">
                    @if ($signer->requestIsDone())
                    {{-- Muestra la ubicación desde la que se hs subido el primer documento --}}
                        @if ($signer->requestFiles->first()->latitude 
                                                    && 
                            $signer->requestFiles->first()->longitude)
                        
                            <a target="_blank" href="https://www.google.com/maps/search/?api=1&query={{$signer->requestFiles->first()->latitude}},{{$signer->requestFiles->first()->longitude}}">
                                @lang('Ubicación')
                            </a>
                        @endif    
                    @endif 
                </td>
            </tr>

            <tr>
                <td>@lang('Documentos Aportados')</td>
                <td data-label="@lang('Documentos Aportados')">
                    @if ($signer->requestIsDone())
                        {{$signer->requestFiles->count()}}
                    @else
                        <span class="p-2">@lang('Ninguno')</span>
                    @endif
                </td>
            </tr>

        </tbody>
    </table>
</p>
@empty
@endforelse
{{--/Estado de la solicitud --}}
