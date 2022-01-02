{{-- La lista de documentos solicitados --}}
<div class="main-card mb-3 card">
    <div class="card-body">
        <h5 class="card-title">
            @lang('Documento a renovar')
            ({{$request->expiringDocuments()->count()}})
        </h5>

        <div>
            <span class="bold">@lang('Nombre de la Solicitud')</span> : 
            <span class="text-primary">{{$request->name}}</span>
        </div>
        <div class="text-secondary">
            {!!$request->comment!!}
        </div>
        
        <div class="table-responsive mt-2">
            <table class="table">
                <thead>
                    @include('common.request.header-required-documents-list')
                </thead>
                <tbody>
                    @foreach ($request->expiringDocuments() as $requiredDocument)
                    <tr>
                        <td data-label="@lang('Documento')">
                            {{-- Si ya se ha aportado o no --}}
                            <i v-if="documentIsAlreadyAported('{{$requiredDocument->name}}')"
                                class="far check fa-check-square fa-2x text-success"
                            ></i>
                            <i v-else class="far check fa-square fa-2x text-warning"></i>
                            {{-- /Si ya se ha aportado o no --}}
                        </td>

                        <td data-label="@lang('Nombre')">
                            {{$requiredDocument->name}}
                        </td>

                        <td data-label="@lang('Comentarios')" class="text-secondary">
                            {{$requiredDocument->comment}}
                        </td>

                        <td data-label="@lang('Tipo')">
                            @if ($requiredDocument->type)
                                <span class="text-info">
                                    @lang($requiredDocument->mimeType->description ?? $requiredDocument->type)
                                </span>
                            @else
                                <span class="text-success">@lang('Cualquiera')</span>
                            @endif
                        </td>

                        <td data-label="@lang('Tamaño')">
                            @if ($requiredDocument->maxsize)
                                <span class="text-info">
                                    @filesize($requiredDocument->maxsize)
                                </span>
                            @else
                                <span class="text-success">@lang('Cualquiera')</span>
                            @endif
                        </td>

                        <td data-label="@lang('Validez')">
                            @if ($requiredDocument->issued_to)
                                <div>
                                    {{$requiredDocument->validity}}
                                    @switch ($requiredDocument->validity_unit)
                                        @case (\App\Enums\TimePeriod::DAY)
                                            @lang('días')
                                            @break
                                        @case (\App\Enums\TimePeriod::MONTH)
                                            @lang('meses')
                                            @break
                                        @case (\App\Enums\TimePeriod::YEAR)
                                            @lang('años')
                                            @break
                                    @endswitch
                                </div>
                                <div class="text-danger text-nowrap">
                                    <i class="fas fa-greater-than"></i> 
                                    @date($requiredDocument->issued_to)
                                </div>
                            @else
                                <span class="text-success">@lang('Cualquiera')</span>
                            @endif
                        </td>

                        <td data-label="@lang('Fecha de vencimiento')">
                            @if ($requiredDocument->has_expiration_date)
                            <span class="text-danger bold">
                    			@date($requiredDocument->file()->expiration_date)
                    		</span>
                            @else
                            <span class="text-success">@lang('No solicitada')</span>
                            @endif
                        </td>
                        
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>   
{{--/La lista de documentos solicitados --}}
